<?php
declare(strict_types=1);

namespace Brew\Controllers\Admin;

use Brew\Core\Database;
use Brew\Core\Response;
use Brew\Core\Session;
use Brew\Core\Validator;

final class BrandController extends BaseAdminController
{
    /**
     * List all brands.
     */
    public function index(): Response
    {
        $db = Database::getInstance();

        $page    = $this->page();
        $perPage = 20;

        $search = (string) $this->request->query('search');

        $query = $db->table('brands')->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC');

        if ($search !== '') {
            $query->whereRaw(
                "name LIKE :search_0",
                [':search_0' => "%{$search}%"]
            );
        }

        $brands = $query->paginate($perPage, $page);

        // Add product and vehicle counts
        foreach ($brands['data'] as &$brand) {
            $brand['product_count'] = $db->table('products')
                ->where('brand_id', $brand['id'])
                ->count();
            $brand['vehicle_count'] = $db->table('vehicles')
                ->where('brand_id', $brand['id'])
                ->count();
        }
        unset($brand);

        return $this->adminView('admin/brands/index', [
            'title'  => 'Brands',
            'brands' => $brands,
            'search' => $search,
        ]);
    }

    /**
     * Show brand creation form.
     */
    public function create(): Response
    {
        return $this->adminView('admin/brands/create', [
            'title' => 'Create Brand',
        ]);
    }

    /**
     * Store a new brand.
     */
    public function store(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/brands/create');
        }

        $data = [
            'name'        => (string) $this->input('name', ''),
            'slug'        => (string) $this->input('slug', ''),
            'logo'        => $this->input('logo'),
            'website_url' => $this->input('website_url'),
            'is_active'   => $this->input('is_active', '0'),
            'sort_order'  => $this->input('sort_order', '0'),
        ];

        $validator = Validator::make($data, [
            'name' => 'required|max:255',
            'slug' => 'required|slug|unique:brands,slug',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(fn($e) => $e[0], $validator->errors())));
            return $this->redirect('/admin/brands/create');
        }

        $db = Database::getInstance();
        $now = date('Y-m-d H:i:s');

        $brandId = $db->table('brands')->insert([
            'name'        => $data['name'],
            'slug'        => $data['slug'],
            'logo'        => $data['logo'] ?: null,
            'website_url' => $data['website_url'] ?: null,
            'is_active'   => (int) $data['is_active'],
            'sort_order'  => (int) $data['sort_order'],
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);

        $this->logActivity('create', 'brand', $brandId, null, $data);
        Session::flash('success', 'Brand created successfully.');
        return $this->redirect('/admin/brands');
    }

    /**
     * Show brand edit form.
     */
    public function edit(int $id): Response
    {
        $db = Database::getInstance();

        $brand = $db->table('brands')->where('id', $id)->first();
        if (!$brand) {
            Session::flash('error', 'Brand not found.');
            return $this->redirect('/admin/brands');
        }

        return $this->adminView('admin/brands/edit', [
            'title' => 'Edit Brand',
            'brand' => $brand,
        ]);
    }

    /**
     * Update an existing brand.
     */
    public function update(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/brands/' . $id . '/edit');
        }

        $db = Database::getInstance();

        $brand = $db->table('brands')->where('id', $id)->first();
        if (!$brand) {
            Session::flash('error', 'Brand not found.');
            return $this->redirect('/admin/brands');
        }

        $data = [
            'name'        => (string) $this->input('name', ''),
            'slug'        => (string) $this->input('slug', ''),
            'logo'        => $this->input('logo'),
            'website_url' => $this->input('website_url'),
            'is_active'   => $this->input('is_active', '0'),
            'sort_order'  => $this->input('sort_order', '0'),
        ];

        $validator = Validator::make($data, [
            'name' => 'required|max:255',
            'slug' => 'required|slug|unique:brands,slug,' . $id,
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(fn($e) => $e[0], $validator->errors())));
            return $this->redirect('/admin/brands/' . $id . '/edit');
        }

        $db->table('brands')->where('id', $id)->update([
            'name'        => $data['name'],
            'slug'        => $data['slug'],
            'logo'        => $data['logo'] ?: null,
            'website_url' => $data['website_url'] ?: null,
            'is_active'   => (int) $data['is_active'],
            'sort_order'  => (int) $data['sort_order'],
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        $this->logActivity('update', 'brand', $id, $brand, $data);
        Session::flash('success', 'Brand updated successfully.');
        return $this->redirect('/admin/brands/' . $id . '/edit');
    }

    /**
     * Delete a brand.
     */
    public function delete(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/brands');
        }

        $db = Database::getInstance();

        $brand = $db->table('brands')->where('id', $id)->first();
        if (!$brand) {
            Session::flash('error', 'Brand not found.');
            return $this->redirect('/admin/brands');
        }

        // Check if brand has products
        $productCount = $db->table('products')->where('brand_id', $id)->count();
        if ($productCount > 0) {
            Session::flash('error', 'Cannot delete a brand that has associated products. Reassign or remove them first.');
            return $this->redirect('/admin/brands');
        }

        // Check if brand has vehicles
        $vehicleCount = $db->table('vehicles')->where('brand_id', $id)->count();
        if ($vehicleCount > 0) {
            Session::flash('error', 'Cannot delete a brand that has associated vehicles. Remove them first.');
            return $this->redirect('/admin/brands');
        }

        $db->table('brands')->where('id', $id)->delete();

        $this->logActivity('delete', 'brand', $id, $brand);
        Session::flash('success', 'Brand deleted successfully.');
        return $this->redirect('/admin/brands');
    }
}
