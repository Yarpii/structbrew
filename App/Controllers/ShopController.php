<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Core\Seo;
use App\Core\StoreResolver;
use App\Core\Translator;
use App\Data\Products;
use Throwable;

final class ShopController extends Controller
{
    /**
     * @throws Throwable
     */
    public function index(Request $req): Response
    {
        Translator::page('shop');
        $category = $req->input('category', '');
        $sort = $req->input('sort', 'default');
        $q = $req->input('q', '');
        $vehicleId = max(0, (int) $req->input('vehicle_id', 0));
        $activeAttributes = $this->normalizeAttributeFilters($req->input('attr', []));
        $page = max(1, (int) $req->input('page', 1));
        $perPage = 24;

        $products = $q ? Products::search($q) : ($category ? Products::byCategory($category) : Products::all());
        if ($vehicleId > 0) {
            $products = $this->applyVehicleFilter($products, $vehicleId);
        }
        $attributeFacets = $this->buildAttributeFacets($products);
        $products = $this->applyAttributeFilters($products, $activeAttributes);

        if ($sort === 'price_asc') usort($products, fn($a, $b) => ($a['sale_price'] ?? $a['price']) <=> ($b['sale_price'] ?? $b['price']));
        if ($sort === 'price_desc') usort($products, fn($a, $b) => ($b['sale_price'] ?? $b['price']) <=> ($a['sale_price'] ?? $a['price']));
        if ($sort === 'rating') usort($products, fn($a, $b) => $b['rating'] <=> $a['rating']);
        if ($sort === 'name') usort($products, fn($a, $b) => $a['name'] <=> $b['name']);

        $total = count($products);
        $lastPage = max(1, (int) ceil($total / $perPage));
        if ($page > $lastPage) {
            $page = $lastPage;
        }
        $offset = ($page - 1) * $perPage;
        $products = array_slice($products, $offset, $perPage);

        $pagination = [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => $lastPage,
            'from' => $total > 0 ? $offset + 1 : 0,
            'to' => min($offset + $perPage, $total),
        ];

        return $this->view('shop.index', [
            'title'      => $q ? "Search: $q" : ($category ? Products::categories()[$category] ?? 'Shop' : 'Shop'),
            'products'   => $products,
            'categories' => Products::categories(),
            'activeCategory' => $category,
            'activeSort' => $sort,
            'searchQuery' => $q,
            'activeVehicleId' => $vehicleId,
            'selectedVehicleLabel' => $this->vehicleLabel($vehicleId),
            'attributeFacets' => $attributeFacets,
            'activeAttributes' => $activeAttributes,
            'pagination' => $pagination,
            'ads' => $this->activeAdsByPlacements(['shop_listing']),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function categories(): Response
    {
        $db = Database::getInstance();
        $storeViewId = StoreResolver::storeViewId();
        if ($storeViewId === null) {
            $defaultStoreView = $db->table('store_views')->where('is_default', 1)->first();
            $storeViewId = (int) ($defaultStoreView['id'] ?? 1);
        }

        $categories = $db->table('categories')
            ->where('is_active', 1)
            ->orderBy('id', 'ASC')
            ->get();

        $translations = $db->table('category_translations')
            ->where('store_view_id', (int) $storeViewId)
            ->get();

        $nameByCategory = [];
        foreach ($translations as $translation) {
            $nameByCategory[(int) $translation['category_id']] = (string) $translation['name'];
        }

        $childrenByParent = [];
        foreach ($categories as $category) {
            $parentId = $category['parent_id'] !== null ? (int) $category['parent_id'] : 0;
            $childrenByParent[$parentId][] = [
                'id' => (int) $category['id'],
                'slug' => (string) $category['slug'],
                'name' => (string) ($nameByCategory[(int) $category['id']] ?? $category['slug']),
            ];
        }

        $buildTree = function (int $parentId) use (&$buildTree, $childrenByParent): array {
            $nodes = [];
            foreach ($childrenByParent[$parentId] ?? [] as $child) {
                $child['children'] = $buildTree($child['id']);
                $nodes[] = $child;
            }

            return $nodes;
        };

        return $this->view('shop.categories', [
            'title' => 'Categories',
            'categoryTree' => $buildTree(0),
            'ads' => $this->activeAdsByPlacements(['shop_category']),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function show(Request $req, string $slug): Response
    {
        Translator::page('shop');
        Translator::page('vehicles');

        $product = Products::findBySlug($slug);
        if (!$product) {
            return $this->text('Product not found', 404);
        }
        $related = Products::related($product['id']);

        // SEO: product-specific meta + structured data
        Seo::title($product['name']);
        Seo::description(strip_tags($product['short_description'] ?? $product['description'] ?? ''));
        Seo::type('product');
        if (!empty($product['image'])) {
            Seo::image($product['image']);
        }
        $productUrl = 'https://' . ($_SERVER['HTTP_HOST'] ?? '') . '/shop/' . $slug;
        Seo::addJsonLd(Seo::productSchema($product, $productUrl));

        return $this->view('shop.show', [
            'title'   => $product['name'],
            'product' => $product,
            'related' => $related,
            'ads' => $this->activeAdsByPlacements(['shop_product']),
        ]);
    }

    public function apiProducts(Request $req): Response
    {
        $category = $req->input('category', '');
        $products = $category ? Products::byCategory($category) : Products::all();
        return $this->json(['products' => $products, 'total' => count($products)]);
    }

    private function applyVehicleFilter(array $products, int $vehicleId): array
    {
        $productVehicleRows = Database::getInstance()
            ->table('product_vehicles')
            ->where('vehicle_id', $vehicleId)
            ->get();

        if (empty($productVehicleRows)) {
            return [];
        }

        $allowedProductIds = array_map(static fn (array $row): int => (int) ($row['product_id'] ?? 0), $productVehicleRows);
        $allowedProductIds = array_values(array_unique($allowedProductIds));

        return array_values(array_filter($products, static fn (array $product): bool => in_array((int) ($product['id'] ?? 0), $allowedProductIds, true)));
    }

    private function vehicleLabel(int $vehicleId): string
    {
        if ($vehicleId <= 0) {
            return '';
        }

        $db = Database::getInstance();
        $vehicle = $db->table('vehicles')
            ->where('id', $vehicleId)
            ->where('is_active', 1)
            ->first();

        if (!$vehicle) {
            return '';
        }

        $brand = $db->table('brands')->where('id', (int) ($vehicle['brand_id'] ?? 0))->first();
        $brandName = (string) ($brand['name'] ?? '');
        return trim($brandName . ' ' . (string) ($vehicle['model'] ?? ''));
    }

    private function normalizeAttributeFilters(mixed $raw): array
    {
        if (!is_array($raw)) {
            return [];
        }

        $normalized = [];
        foreach ($raw as $key => $values) {
            if (!is_string($key) || $key === '') {
                continue;
            }

            $vals = is_array($values) ? $values : [$values];
            $vals = array_values(array_filter(array_map(static fn($v): string => trim((string) $v), $vals), static fn(string $v): bool => $v !== ''));
            if (!empty($vals)) {
                $normalized[$key] = array_values(array_unique($vals));
            }
        }

        return $normalized;
    }

    private function applyAttributeFilters(array $products, array $activeAttributes): array
    {
        if (empty($activeAttributes)) {
            return $products;
        }

        return array_values(array_filter($products, static function (array $product) use ($activeAttributes): bool {
            $searchAttributes = $product['search_attributes'] ?? [];
            foreach ($activeAttributes as $attrKey => $selectedValues) {
                $productValue = (string) ($searchAttributes[$attrKey] ?? '');
                if ($productValue === '' || !in_array($productValue, $selectedValues, true)) {
                    return false;
                }
            }
            return true;
        }));
    }

    private function buildAttributeFacets(array $products): array
    {
        $facetCounts = [];

        foreach ($products as $product) {
            foreach (($product['search_attributes'] ?? []) as $key => $value) {
                $key = trim((string) $key);
                $value = trim((string) $value);
                if ($key === '' || $value === '') {
                    continue;
                }

                $facetCounts[$key][$value] = ($facetCounts[$key][$value] ?? 0) + 1;
            }
        }

        $facets = [];
        foreach ($facetCounts as $key => $values) {
            arsort($values);
            $options = [];
            foreach ($values as $value => $count) {
                $options[] = ['value' => $value, 'count' => $count];
            }

            $facets[] = [
                'key' => $key,
                'label' => ucwords(str_replace(['_', '-'], ' ', $key)),
                'options' => array_slice($options, 0, 12),
            ];
        }

        usort($facets, static fn(array $a, array $b): int => strcmp($a['label'], $b['label']));
        return array_slice($facets, 0, 8);
    }

    private function activeAdsByPlacements(array $placements): array
    {
        if (empty($placements)) {
            return [];
        }

        try {
            $now = date('Y-m-d H:i:s');
            $rows = Database::getInstance()->table('marketing_ads')
                ->where('is_active', 1)
                ->whereRaw('(starts_at IS NULL OR starts_at <= :now0)', [':now0' => $now])
                ->whereRaw('(ends_at IS NULL OR ends_at >= :now1)', [':now1' => $now])
                ->orderBy('sort_order', 'ASC')
                ->orderBy('id', 'DESC')
                ->get();

            $allowed = array_fill_keys($placements, true);
            $mapped = [];

            foreach ($rows as $row) {
                $placement = (string) ($row['placement'] ?? '');
                if ($placement === '' || !isset($allowed[$placement]) || isset($mapped[$placement])) {
                    continue;
                }
                $mapped[$placement] = $row;
            }

            return $mapped;
        } catch (Throwable) {
            return [];
        }
    }
}
