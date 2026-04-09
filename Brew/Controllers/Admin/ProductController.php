<?php
declare(strict_types=1);

namespace Brew\Controllers\Admin;

use Brew\Core\Database;
use Brew\Core\Response;
use Brew\Core\Session;
use Brew\Core\Validator;

final class ProductController extends BaseAdminController
{
    /**
     * List products with search, filter, and pagination.
     */
    public function index(): Response
    {
        $db = Database::getInstance();

        $search      = (string) $this->request->query('search');
        $brandId     = $this->request->query('brand_id');
        $status      = $this->request->query('status');
        $storeViewId = $this->request->query('store_view_id');
        $page        = $this->page();
        $perPage     = 20;

        $query = $db->table('products')
            ->select(
                'products.*'
            );

        // Join translations if a store view filter or search is applied
        $svId = $storeViewId ? (int) $storeViewId : null;
        if ($svId) {
            $query->leftJoin(
                'product_translations',
                'products.id', '=', 'product_translations.product_id'
            );
            $query->where('product_translations.store_view_id', $svId);
        }

        if ($search !== '') {
            if (!$svId) {
                $query->leftJoin(
                    'product_translations',
                    'products.id', '=', 'product_translations.product_id'
                );
            }
            $query->whereRaw(
                "(products.sku LIKE :search_0 OR product_translations.name LIKE :search_1)",
                [':search_0' => "%{$search}%", ':search_1' => "%{$search}%"]
            );
        }

        if ($brandId) {
            $query->where('products.brand_id', (int) $brandId);
        }

        if ($status === 'active') {
            $query->where('products.is_active', 1);
        } elseif ($status === 'inactive') {
            $query->where('products.is_active', 0);
        }

        $query->orderBy('products.created_at', 'DESC');

        $products = $query->paginate($perPage, $page);

        // Enrich each product with its first translation and pricing
        foreach ($products['data'] as &$product) {
            $product['translation'] = $db->table('product_translations')
                ->where('product_id', $product['id'])
                ->first();
            $product['pricing'] = $db->table('product_pricing')
                ->where('product_id', $product['id'])
                ->first();
            $product['brand'] = $product['brand_id']
                ? $db->table('brands')->where('id', $product['brand_id'])->first()
                : null;
        }
        unset($product);

        $brands = $db->table('brands')->orderBy('name', 'ASC')->get();

        return $this->adminView('admin/products/index', [
            'title'        => 'Products',
            'products'     => $products,
            'brands'       => $brands,
            'search'       => $search,
            'brandId'      => $brandId,
            'status'       => $status,
            'storeViewId'  => $storeViewId,
        ]);
    }

    /**
     * Show product creation form.
     */
    public function create(): Response
    {
        $db = Database::getInstance();

        $brands     = $db->table('brands')->orderBy('name', 'ASC')->get();
        $categories = $db->table('categories')->orderBy('position', 'ASC')->get();
        $vehicles   = $db->table('vehicles')
            ->leftJoin('brands', 'vehicles.brand_id', '=', 'brands.id')
            ->select('vehicles.*', 'brands.name as brand_name')
            ->orderBy('brands.name', 'ASC')
            ->orderBy('vehicles.model', 'ASC')
            ->get();

        // Attach translations to categories for display
        foreach ($categories as &$category) {
            $category['translation'] = $db->table('category_translations')
                ->where('category_id', $category['id'])
                ->first();
        }
        unset($category);

        return $this->adminView('admin/products/create', [
            'title'      => 'Create Product',
            'brands'     => $brands,
            'categories' => $categories,
            'vehicles'   => $vehicles,
        ]);
    }

    /**
     * Store a new product with translations, pricing, categories, and vehicles.
     */
    public function store(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/products/create');
        }

        $data = [
            'sku'                 => (string) $this->input('sku', ''),
            'slug'                => (string) $this->input('slug', ''),
            'brand_id'            => $this->input('brand_id'),
            'weight'              => $this->input('weight'),
            'is_active'           => $this->input('is_active', '0'),
            'is_featured'         => $this->input('is_featured', '0'),
            'manage_stock'        => $this->input('manage_stock', '1'),
            'stock_qty'           => $this->input('stock_qty', '0'),
            'low_stock_threshold' => $this->input('low_stock_threshold', '5'),
            'oem_number'          => $this->input('oem_number'),
        ];

        $validator = Validator::make($data, [
            'sku'  => 'required|unique:products,sku',
            'slug' => 'required|slug|unique:products,slug',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(fn($e) => $e[0], $validator->errors())));
            return $this->redirect('/admin/products/create');
        }

        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            $now = date('Y-m-d H:i:s');
            $productId = $db->table('products')->insert([
                'sku'                 => $data['sku'],
                'slug'                => $data['slug'],
                'brand_id'            => $data['brand_id'] ?: null,
                'weight'              => $data['weight'] ?: null,
                'is_active'           => (int) $data['is_active'],
                'is_featured'         => (int) $data['is_featured'],
                'manage_stock'        => (int) $data['manage_stock'],
                'stock_qty'           => (int) $data['stock_qty'],
                'low_stock_threshold' => (int) $data['low_stock_threshold'],
                'oem_number'          => $data['oem_number'] ?: null,
                'created_at'          => $now,
                'updated_at'          => $now,
            ]);

            // Save translations per store view
            $translations = $this->input('translations', []);
            if (is_array($translations)) {
                foreach ($translations as $svId => $trans) {
                    if (empty($trans['name'])) {
                        continue;
                    }
                    $db->table('product_translations')->insert([
                        'product_id'        => $productId,
                        'store_view_id'     => (int) $svId,
                        'name'              => $trans['name'],
                        'short_description' => $trans['short_description'] ?? null,
                        'description'       => $trans['description'] ?? null,
                        'meta_title'        => $trans['meta_title'] ?? null,
                        'meta_description'  => $trans['meta_description'] ?? null,
                        'url_key'           => $trans['url_key'] ?? $data['slug'],
                    ]);
                }
            }

            // Save pricing per store view
            $pricing = $this->input('pricing', []);
            if (is_array($pricing)) {
                foreach ($pricing as $svId => $price) {
                    if (empty($price['price'])) {
                        continue;
                    }
                    $db->table('product_pricing')->insert([
                        'product_id'    => $productId,
                        'store_view_id' => (int) $svId,
                        'price'         => (float) $price['price'],
                        'sale_price'    => !empty($price['sale_price']) ? (float) $price['sale_price'] : null,
                        'cost_price'    => !empty($price['cost_price']) ? (float) $price['cost_price'] : null,
                        'currency_code' => $price['currency_code'] ?? 'EUR',
                    ]);
                }
            }

            // Attach categories
            $categoryIds = $this->input('category_ids', []);
            if (is_array($categoryIds)) {
                foreach ($categoryIds as $catId) {
                    $db->table('product_categories')->insert([
                        'product_id'  => $productId,
                        'category_id' => (int) $catId,
                    ]);
                }
            }

            // Attach vehicles
            $vehicleIds = $this->input('vehicle_ids', []);
            if (is_array($vehicleIds)) {
                foreach ($vehicleIds as $vId) {
                    $db->table('product_vehicles')->insert([
                        'product_id' => $productId,
                        'vehicle_id' => (int) $vId,
                    ]);
                }
            }

            $db->commit();

            $this->logActivity('create', 'product', $productId, null, $data);
            Session::flash('success', 'Product created successfully.');
            return $this->redirect('/admin/products/' . $productId . '/edit');

        } catch (\Throwable $e) {
            $db->rollback();
            Session::flash('error', 'Failed to create product: ' . $e->getMessage());
            return $this->redirect('/admin/products/create');
        }
    }

    /**
     * Show product edit form.
     */
    public function edit(int $id): Response
    {
        $db = Database::getInstance();

        $product = $db->table('products')->where('id', $id)->first();
        if (!$product) {
            Session::flash('error', 'Product not found.');
            return $this->redirect('/admin/products');
        }

        $brands     = $db->table('brands')->orderBy('name', 'ASC')->get();
        $categories = $db->table('categories')->orderBy('position', 'ASC')->get();
        $vehicles   = $db->table('vehicles')
            ->leftJoin('brands', 'vehicles.brand_id', '=', 'brands.id')
            ->select('vehicles.*', 'brands.name as brand_name')
            ->orderBy('brands.name', 'ASC')
            ->orderBy('vehicles.model', 'ASC')
            ->get();

        // Attach translations to categories
        foreach ($categories as &$category) {
            $category['translation'] = $db->table('category_translations')
                ->where('category_id', $category['id'])
                ->first();
        }
        unset($category);

        // Load existing translations keyed by store_view_id
        $translationRows = $db->table('product_translations')
            ->where('product_id', $id)
            ->get();
        $translations = [];
        foreach ($translationRows as $row) {
            $translations[(int) $row['store_view_id']] = $row;
        }

        // Load existing pricing keyed by store_view_id
        $pricingRows = $db->table('product_pricing')
            ->where('product_id', $id)
            ->get();
        $pricingData = [];
        foreach ($pricingRows as $row) {
            $pricingData[(int) $row['store_view_id']] = $row;
        }

        // Load attached category IDs
        $attachedCategoryIds = array_column(
            $db->table('product_categories')->where('product_id', $id)->get(),
            'category_id'
        );

        // Load attached vehicle IDs
        $attachedVehicleIds = array_column(
            $db->table('product_vehicles')->where('product_id', $id)->get(),
            'vehicle_id'
        );

        // Load images
        $images = $db->table('product_images')
            ->where('product_id', $id)
            ->orderBy('sort_order', 'ASC')
            ->get();

        return $this->adminView('admin/products/edit', [
            'title'               => 'Edit Product',
            'product'             => $product,
            'brands'              => $brands,
            'categories'          => $categories,
            'vehicles'            => $vehicles,
            'translations'        => $translations,
            'pricingData'         => $pricingData,
            'attachedCategoryIds' => $attachedCategoryIds,
            'attachedVehicleIds'  => $attachedVehicleIds,
            'images'              => $images,
        ]);
    }

    /**
     * Update an existing product and all its relations.
     */
    public function update(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/products/' . $id . '/edit');
        }

        $db = Database::getInstance();

        $product = $db->table('products')->where('id', $id)->first();
        if (!$product) {
            Session::flash('error', 'Product not found.');
            return $this->redirect('/admin/products');
        }

        $data = [
            'sku'                 => (string) $this->input('sku', ''),
            'slug'                => (string) $this->input('slug', ''),
            'brand_id'            => $this->input('brand_id'),
            'weight'              => $this->input('weight'),
            'is_active'           => $this->input('is_active', '0'),
            'is_featured'         => $this->input('is_featured', '0'),
            'manage_stock'        => $this->input('manage_stock', '1'),
            'stock_qty'           => $this->input('stock_qty', '0'),
            'low_stock_threshold' => $this->input('low_stock_threshold', '5'),
            'oem_number'          => $this->input('oem_number'),
        ];

        $validator = Validator::make($data, [
            'sku'  => 'required|unique:products,sku,' . $id,
            'slug' => 'required|slug|unique:products,slug,' . $id,
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(fn($e) => $e[0], $validator->errors())));
            return $this->redirect('/admin/products/' . $id . '/edit');
        }

        $db->beginTransaction();

        try {
            $now = date('Y-m-d H:i:s');
            $db->table('products')->where('id', $id)->update([
                'sku'                 => $data['sku'],
                'slug'                => $data['slug'],
                'brand_id'            => $data['brand_id'] ?: null,
                'weight'              => $data['weight'] ?: null,
                'is_active'           => (int) $data['is_active'],
                'is_featured'         => (int) $data['is_featured'],
                'manage_stock'        => (int) $data['manage_stock'],
                'stock_qty'           => (int) $data['stock_qty'],
                'low_stock_threshold' => (int) $data['low_stock_threshold'],
                'oem_number'          => $data['oem_number'] ?: null,
                'updated_at'          => $now,
            ]);

            // Update translations per store view (upsert approach)
            $translations = $this->input('translations', []);
            if (is_array($translations)) {
                foreach ($translations as $svId => $trans) {
                    if (empty($trans['name'])) {
                        continue;
                    }
                    $existing = $db->table('product_translations')
                        ->where('product_id', $id)
                        ->where('store_view_id', (int) $svId)
                        ->first();

                    $transData = [
                        'name'              => $trans['name'],
                        'short_description' => $trans['short_description'] ?? null,
                        'description'       => $trans['description'] ?? null,
                        'meta_title'        => $trans['meta_title'] ?? null,
                        'meta_description'  => $trans['meta_description'] ?? null,
                        'url_key'           => $trans['url_key'] ?? $data['slug'],
                    ];

                    if ($existing) {
                        $db->table('product_translations')
                            ->where('id', $existing['id'])
                            ->update($transData);
                    } else {
                        $db->table('product_translations')->insert(array_merge($transData, [
                            'product_id'    => $id,
                            'store_view_id' => (int) $svId,
                        ]));
                    }
                }
            }

            // Update pricing per store view (upsert approach)
            $pricing = $this->input('pricing', []);
            if (is_array($pricing)) {
                foreach ($pricing as $svId => $price) {
                    if (empty($price['price'])) {
                        continue;
                    }
                    $existing = $db->table('product_pricing')
                        ->where('product_id', $id)
                        ->where('store_view_id', (int) $svId)
                        ->first();

                    $priceData = [
                        'price'         => (float) $price['price'],
                        'sale_price'    => !empty($price['sale_price']) ? (float) $price['sale_price'] : null,
                        'cost_price'    => !empty($price['cost_price']) ? (float) $price['cost_price'] : null,
                        'currency_code' => $price['currency_code'] ?? 'EUR',
                    ];

                    if ($existing) {
                        $db->table('product_pricing')
                            ->where('id', $existing['id'])
                            ->update($priceData);
                    } else {
                        $db->table('product_pricing')->insert(array_merge($priceData, [
                            'product_id'    => $id,
                            'store_view_id' => (int) $svId,
                        ]));
                    }
                }
            }

            // Sync categories
            $db->table('product_categories')->where('product_id', $id)->delete();
            $categoryIds = $this->input('category_ids', []);
            if (is_array($categoryIds)) {
                foreach ($categoryIds as $catId) {
                    $db->table('product_categories')->insert([
                        'product_id'  => $id,
                        'category_id' => (int) $catId,
                    ]);
                }
            }

            // Sync vehicles
            $db->table('product_vehicles')->where('product_id', $id)->delete();
            $vehicleIds = $this->input('vehicle_ids', []);
            if (is_array($vehicleIds)) {
                foreach ($vehicleIds as $vId) {
                    $db->table('product_vehicles')->insert([
                        'product_id' => $id,
                        'vehicle_id' => (int) $vId,
                    ]);
                }
            }

            $db->commit();

            $this->logActivity('update', 'product', $id, $product, $data);
            Session::flash('success', 'Product updated successfully.');
            return $this->redirect('/admin/products/' . $id . '/edit');

        } catch (\Throwable $e) {
            $db->rollback();
            Session::flash('error', 'Failed to update product: ' . $e->getMessage());
            return $this->redirect('/admin/products/' . $id . '/edit');
        }
    }

    /**
     * Delete a product and all related data.
     */
    public function delete(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/products');
        }

        $db = Database::getInstance();

        $product = $db->table('products')->where('id', $id)->first();
        if (!$product) {
            Session::flash('error', 'Product not found.');
            return $this->redirect('/admin/products');
        }

        $db->beginTransaction();

        try {
            // Cascade deletes are set up via FK, but explicitly clean pivot tables
            $db->table('product_categories')->where('product_id', $id)->delete();
            $db->table('product_vehicles')->where('product_id', $id)->delete();
            $db->table('product_images')->where('product_id', $id)->delete();
            $db->table('product_translations')->where('product_id', $id)->delete();
            $db->table('product_pricing')->where('product_id', $id)->delete();
            $db->table('product_attributes')->where('product_id', $id)->delete();
            $db->table('products')->where('id', $id)->delete();

            $db->commit();

            $this->logActivity('delete', 'product', $id, $product);
            Session::flash('success', 'Product deleted successfully.');

        } catch (\Throwable $e) {
            $db->rollback();
            Session::flash('error', 'Failed to delete product: ' . $e->getMessage());
        }

        return $this->redirect('/admin/products');
    }
}
