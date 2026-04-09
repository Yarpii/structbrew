<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;

final class StoreController extends BaseAdminController
{
    // ─── Websites ────────────────────────────────────────────

    /**
     * List all websites with their stores.
     */
    public function websites(): Response
    {
        $db = Database::getInstance();

        $websites = $db->table('websites')
            ->orderBy('sort_order', 'ASC')
            ->get();

        foreach ($websites as &$website) {
            $website['stores'] = $db->table('stores')
                ->where('website_id', $website['id'])
                ->orderBy('sort_order', 'ASC')
                ->get();
        }
        unset($website);

        return $this->adminView('admin/stores/websites', [
            'title'    => 'Websites',
            'websites' => $websites,
        ]);
    }

    /**
     * Show a single website with its stores.
     */
    public function storeWebsite(int $id): Response
    {
        $db = Database::getInstance();

        $website = $db->table('websites')->where('id', $id)->first();
        if (!$website) {
            Session::flash('error', 'Website not found.');
            return $this->redirect('/admin/stores/websites');
        }

        $stores = $db->table('stores')
            ->where('website_id', $id)
            ->orderBy('sort_order', 'ASC')
            ->get();

        foreach ($stores as &$store) {
            $store['view_count'] = $db->table('store_views')
                ->where('store_id', $store['id'])
                ->count();
        }
        unset($store);

        return $this->adminView('admin/stores/website-detail', [
            'title'   => 'Website: ' . $website['name'],
            'website' => $website,
            'stores'  => $stores,
        ]);
    }

    /**
     * Store a new website.
     */
    public function createWebsite(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/stores/websites');
        }

        $data = [
            'code'       => (string) $this->input('code', ''),
            'name'       => (string) $this->input('name', ''),
            'is_default' => $this->input('is_default', '0'),
            'sort_order' => $this->input('sort_order', '0'),
            'is_active'  => $this->input('is_active', '0'),
        ];

        $validator = Validator::make($data, [
            'code' => 'required|slug|unique:websites,code',
            'name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(fn($e) => $e[0], $validator->errors())));
            return $this->redirect('/admin/stores/websites');
        }

        $db = Database::getInstance();
        $now = date('Y-m-d H:i:s');

        $websiteId = $db->table('websites')->insert([
            'code'       => $data['code'],
            'name'       => $data['name'],
            'is_default' => (int) $data['is_default'],
            'sort_order' => (int) $data['sort_order'],
            'is_active'  => (int) $data['is_active'],
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->logActivity('create', 'website', $websiteId, null, $data);
        Session::flash('success', 'Website created successfully.');
        return $this->redirect('/admin/stores/websites');
    }

    /**
     * Update an existing website.
     */
    public function updateWebsite(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/stores/websites');
        }

        $db = Database::getInstance();

        $website = $db->table('websites')->where('id', $id)->first();
        if (!$website) {
            Session::flash('error', 'Website not found.');
            return $this->redirect('/admin/stores/websites');
        }

        $data = [
            'code'       => (string) $this->input('code', ''),
            'name'       => (string) $this->input('name', ''),
            'is_default' => $this->input('is_default', '0'),
            'sort_order' => $this->input('sort_order', '0'),
            'is_active'  => $this->input('is_active', '0'),
        ];

        $validator = Validator::make($data, [
            'code' => 'required|slug|unique:websites,code,' . $id,
            'name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(fn($e) => $e[0], $validator->errors())));
            return $this->redirect('/admin/stores/websites/' . $id);
        }

        $db->table('websites')->where('id', $id)->update([
            'code'       => $data['code'],
            'name'       => $data['name'],
            'is_default' => (int) $data['is_default'],
            'sort_order' => (int) $data['sort_order'],
            'is_active'  => (int) $data['is_active'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->logActivity('update', 'website', $id, $website, $data);
        Session::flash('success', 'Website updated successfully.');
        return $this->redirect('/admin/stores/websites/' . $id);
    }

    /**
     * Delete a website.
     */
    public function deleteWebsite(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/stores/websites');
        }

        $db = Database::getInstance();

        $website = $db->table('websites')->where('id', $id)->first();
        if (!$website) {
            Session::flash('error', 'Website not found.');
            return $this->redirect('/admin/stores/websites');
        }

        // Prevent deletion if it has stores
        $storeCount = $db->table('stores')->where('website_id', $id)->count();
        if ($storeCount > 0) {
            Session::flash('error', 'Cannot delete a website that has stores. Remove them first.');
            return $this->redirect('/admin/stores/websites');
        }

        $db->table('websites')->where('id', $id)->delete();

        $this->logActivity('delete', 'website', $id, $website);
        Session::flash('success', 'Website deleted successfully.');
        return $this->redirect('/admin/stores/websites');
    }

    // ─── Store Views ─────────────────────────────────────────

    /**
     * List all store views.
     */
    public function views(): Response
    {
        $db = Database::getInstance();

        $views = $db->table('store_views')
            ->select('store_views.*', 'stores.name as store_name', 'websites.name as website_name')
            ->leftJoin('stores', 'store_views.store_id', '=', 'stores.id')
            ->leftJoin('websites', 'stores.website_id', '=', 'websites.id')
            ->orderBy('store_views.sort_order', 'ASC')
            ->get();

        foreach ($views as &$view) {
            $view['domain_count'] = $db->table('store_domains')
                ->where('store_view_id', $view['id'])
                ->count();
        }
        unset($view);

        return $this->adminView('admin/stores/views', [
            'title' => 'Store Views',
            'views' => $views,
        ]);
    }

    /**
     * Show store view creation form.
     */
    public function createView(): Response
    {
        $db = Database::getInstance();

        $stores = $db->table('stores')
            ->select('stores.*', 'websites.name as website_name')
            ->leftJoin('websites', 'stores.website_id', '=', 'websites.id')
            ->orderBy('websites.name', 'ASC')
            ->orderBy('stores.name', 'ASC')
            ->get();

        return $this->adminView('admin/stores/create-view', [
            'title'  => 'Create Store View',
            'stores' => $stores,
        ]);
    }

    /**
     * Store a new store view.
     */
    public function storeView(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/stores/views/create');
        }

        $data = [
            'store_id'      => (string) $this->input('store_id', ''),
            'code'          => (string) $this->input('code', ''),
            'name'          => (string) $this->input('name', ''),
            'locale'        => (string) $this->input('locale', 'en_US'),
            'currency_code' => (string) $this->input('currency_code', 'USD'),
            'theme'         => $this->input('theme', 'default'),
            'is_default'    => $this->input('is_default', '0'),
            'sort_order'    => $this->input('sort_order', '0'),
            'is_active'     => $this->input('is_active', '0'),
        ];

        $validator = Validator::make($data, [
            'store_id' => 'required|integer',
            'code'     => 'required|slug|unique:store_views,code',
            'name'     => 'required|max:255',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(fn($e) => $e[0], $validator->errors())));
            return $this->redirect('/admin/stores/views/create');
        }

        $db = Database::getInstance();
        $now = date('Y-m-d H:i:s');

        $viewId = $db->table('store_views')->insert([
            'store_id'      => (int) $data['store_id'],
            'code'          => $data['code'],
            'name'          => $data['name'],
            'locale'        => $data['locale'],
            'currency_code' => $data['currency_code'],
            'theme'         => $data['theme'] ?: 'default',
            'is_default'    => (int) $data['is_default'],
            'sort_order'    => (int) $data['sort_order'],
            'is_active'     => (int) $data['is_active'],
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        $this->logActivity('create', 'store_view', $viewId, null, $data);
        Session::flash('success', 'Store view created successfully.');
        return $this->redirect('/admin/stores/views');
    }

    /**
     * Show store view edit form.
     */
    public function editView(int $id): Response
    {
        $db = Database::getInstance();

        $storeViewRecord = $db->table('store_views')->where('id', $id)->first();
        if (!$storeViewRecord) {
            Session::flash('error', 'Store view not found.');
            return $this->redirect('/admin/stores/views');
        }

        $stores = $db->table('stores')
            ->select('stores.*', 'websites.name as website_name')
            ->leftJoin('websites', 'stores.website_id', '=', 'websites.id')
            ->orderBy('websites.name', 'ASC')
            ->orderBy('stores.name', 'ASC')
            ->get();

        return $this->adminView('admin/stores/edit-view', [
            'title'     => 'Edit Store View',
            'storeViewRecord' => $storeViewRecord,
            'stores'    => $stores,
        ]);
    }

    /**
     * Update an existing store view.
     */
    public function updateView(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/stores/views/' . $id . '/edit');
        }

        $db = Database::getInstance();

        $storeViewRecord = $db->table('store_views')->where('id', $id)->first();
        if (!$storeViewRecord) {
            Session::flash('error', 'Store view not found.');
            return $this->redirect('/admin/stores/views');
        }

        $data = [
            'store_id'      => (string) $this->input('store_id', ''),
            'code'          => (string) $this->input('code', ''),
            'name'          => (string) $this->input('name', ''),
            'locale'        => (string) $this->input('locale', 'en_US'),
            'currency_code' => (string) $this->input('currency_code', 'USD'),
            'theme'         => $this->input('theme', 'default'),
            'is_default'    => $this->input('is_default', '0'),
            'sort_order'    => $this->input('sort_order', '0'),
            'is_active'     => $this->input('is_active', '0'),
        ];

        $validator = Validator::make($data, [
            'store_id' => 'required|integer',
            'code'     => 'required|slug|unique:store_views,code,' . $id,
            'name'     => 'required|max:255',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(fn($e) => $e[0], $validator->errors())));
            return $this->redirect('/admin/stores/views/' . $id . '/edit');
        }

        $db->table('store_views')->where('id', $id)->update([
            'store_id'      => (int) $data['store_id'],
            'code'          => $data['code'],
            'name'          => $data['name'],
            'locale'        => $data['locale'],
            'currency_code' => $data['currency_code'],
            'theme'         => $data['theme'] ?: 'default',
            'is_default'    => (int) $data['is_default'],
            'sort_order'    => (int) $data['sort_order'],
            'is_active'     => (int) $data['is_active'],
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        $this->logActivity('update', 'store_view', $id, $storeViewRecord, $data);
        Session::flash('success', 'Store view updated successfully.');
        return $this->redirect('/admin/stores/views/' . $id . '/edit');
    }

    /**
     * Delete a store view.
     */
    public function deleteView(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/stores/views');
        }

        $db = Database::getInstance();

        $storeViewRecord = $db->table('store_views')->where('id', $id)->first();
        if (!$storeViewRecord) {
            Session::flash('error', 'Store view not found.');
            return $this->redirect('/admin/stores/views');
        }

        // Prevent deletion of default store view
        if ((int) ($storeViewRecord['is_default'] ?? 0) === 1) {
            Session::flash('error', 'Cannot delete the default store view.');
            return $this->redirect('/admin/stores/views');
        }

        // Check for orders referencing this view
        $orderCount = $db->table('orders')->where('store_view_id', $id)->count();
        if ($orderCount > 0) {
            Session::flash('error', 'Cannot delete a store view that has associated orders.');
            return $this->redirect('/admin/stores/views');
        }

        $db->beginTransaction();

        try {
            $db->table('store_domains')->where('store_view_id', $id)->delete();
            $db->table('store_views')->where('id', $id)->delete();

            $db->commit();

            $this->logActivity('delete', 'store_view', $id, $storeViewRecord);
            Session::flash('success', 'Store view deleted successfully.');

        } catch (\Throwable $e) {
            $db->rollback();
            Session::flash('error', 'Failed to delete store view: ' . $e->getMessage());
        }

        return $this->redirect('/admin/stores/views');
    }

    // ─── Domains ─────────────────────────────────────────────

    /**
     * List all domains.
     */
    public function domains(): Response
    {
        $db = Database::getInstance();

        $domains = $db->table('store_domains')
            ->select('store_domains.*', 'store_views.name as store_view_name', 'store_views.code as store_view_code')
            ->leftJoin('store_views', 'store_domains.store_view_id', '=', 'store_views.id')
            ->orderBy('store_domains.domain', 'ASC')
            ->get();

        return $this->adminView('admin/stores/domains', [
            'title'   => 'Domains',
            'domains' => $domains,
        ]);
    }

    /**
     * Show domain creation form.
     */
    public function createDomain(): Response
    {
        $db = Database::getInstance();

        $storeViewsList = $db->table('store_views')
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->get();

        return $this->adminView('admin/stores/create-domain', [
            'title'          => 'Create Domain',
            'storeViewsList' => $storeViewsList,
        ]);
    }

    /**
     * Store a new domain.
     */
    public function storeDomain(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/stores/domains/create');
        }

        $data = [
            'store_view_id' => (string) $this->input('store_view_id', ''),
            'domain'        => (string) $this->input('domain', ''),
            'is_active'     => $this->input('is_active', '0'),
            'is_primary'    => $this->input('is_primary', '0'),
        ];

        $validator = Validator::make($data, [
            'store_view_id' => 'required|integer',
            'domain'        => 'required|max:255|unique:store_domains,domain',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(fn($e) => $e[0], $validator->errors())));
            return $this->redirect('/admin/stores/domains/create');
        }

        $db = Database::getInstance();
        $now = date('Y-m-d H:i:s');

        $domainId = $db->table('store_domains')->insert([
            'store_view_id' => (int) $data['store_view_id'],
            'domain'        => strtolower($data['domain']),
            'is_active'     => (int) $data['is_active'],
            'is_primary'    => (int) $data['is_primary'],
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);

        $this->logActivity('create', 'store_domain', $domainId, null, $data);
        Session::flash('success', 'Domain created successfully.');
        return $this->redirect('/admin/stores/domains');
    }

    /**
     * Delete a domain.
     */
    public function deleteDomain(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/stores/domains');
        }

        $db = Database::getInstance();

        $domain = $db->table('store_domains')->where('id', $id)->first();
        if (!$domain) {
            Session::flash('error', 'Domain not found.');
            return $this->redirect('/admin/stores/domains');
        }

        $db->table('store_domains')->where('id', $id)->delete();

        $this->logActivity('delete', 'store_domain', $id, $domain);
        Session::flash('success', 'Domain deleted successfully.');
        return $this->redirect('/admin/stores/domains');
    }
}
