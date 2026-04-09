<?php
declare(strict_types=1);

namespace Brew\Controllers\Admin;

use Brew\Core\Database;
use Brew\Core\Response;
use Brew\Core\Session;
use Brew\Core\Validator;

final class ContentController extends BaseAdminController
{
    // ─── CMS Pages ───────────────────────────────────────────

    /**
     * List all CMS pages.
     */
    public function pages(): Response
    {
        $db = Database::getInstance();

        $page    = $this->page();
        $perPage = 20;

        $pages = $db->table('cms_pages')
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage, $page);

        // Attach first available translation
        foreach ($pages['data'] as &$cmsPage) {
            $cmsPage['translation'] = $db->table('cms_page_translations')
                ->where('cms_page_id', $cmsPage['id'])
                ->first();
        }
        unset($cmsPage);

        return $this->adminView('admin/content/pages', [
            'title' => 'CMS Pages',
            'pages' => $pages,
        ]);
    }

    /**
     * Show page creation form.
     */
    public function createPage(): Response
    {
        return $this->adminView('admin/content/create-page', [
            'title' => 'Create CMS Page',
        ]);
    }

    /**
     * Store a new CMS page with translations.
     */
    public function storePage(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/content/pages/create');
        }

        $data = [
            'slug'      => (string) $this->input('slug', ''),
            'is_active' => $this->input('is_active', '0'),
        ];

        $validator = Validator::make($data, [
            'slug' => 'required|slug|unique:cms_pages,slug',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(fn($e) => $e[0], $validator->errors())));
            return $this->redirect('/admin/content/pages/create');
        }

        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            $now = date('Y-m-d H:i:s');
            $pageId = $db->table('cms_pages')->insert([
                'slug'       => $data['slug'],
                'is_active'  => (int) $data['is_active'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Save translations per store view
            $translations = $this->input('translations', []);
            if (is_array($translations)) {
                foreach ($translations as $svId => $trans) {
                    if (empty($trans['title'])) {
                        continue;
                    }
                    $db->table('cms_page_translations')->insert([
                        'cms_page_id'      => $pageId,
                        'store_view_id'    => (int) $svId,
                        'title'            => $trans['title'],
                        'content'          => $trans['content'] ?? '',
                        'meta_title'       => $trans['meta_title'] ?? null,
                        'meta_description' => $trans['meta_description'] ?? null,
                    ]);
                }
            }

            $db->commit();

            $this->logActivity('create', 'cms_page', $pageId, null, $data);
            Session::flash('success', 'Page created successfully.');
            return $this->redirect('/admin/content/pages');

        } catch (\Throwable $e) {
            $db->rollback();
            Session::flash('error', 'Failed to create page: ' . $e->getMessage());
            return $this->redirect('/admin/content/pages/create');
        }
    }

    /**
     * Show page edit form.
     */
    public function editPage(int $id): Response
    {
        $db = Database::getInstance();

        $cmsPage = $db->table('cms_pages')->where('id', $id)->first();
        if (!$cmsPage) {
            Session::flash('error', 'Page not found.');
            return $this->redirect('/admin/content/pages');
        }

        // Load translations keyed by store_view_id
        $translationRows = $db->table('cms_page_translations')
            ->where('cms_page_id', $id)
            ->get();
        $translations = [];
        foreach ($translationRows as $row) {
            $translations[(int) $row['store_view_id']] = $row;
        }

        return $this->adminView('admin/content/edit-page', [
            'title'        => 'Edit Page',
            'cmsPage'      => $cmsPage,
            'translations' => $translations,
        ]);
    }

    /**
     * Update an existing CMS page and its translations.
     */
    public function updatePage(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/content/pages/' . $id . '/edit');
        }

        $db = Database::getInstance();

        $cmsPage = $db->table('cms_pages')->where('id', $id)->first();
        if (!$cmsPage) {
            Session::flash('error', 'Page not found.');
            return $this->redirect('/admin/content/pages');
        }

        $data = [
            'slug'      => (string) $this->input('slug', ''),
            'is_active' => $this->input('is_active', '0'),
        ];

        $validator = Validator::make($data, [
            'slug' => 'required|slug|unique:cms_pages,slug,' . $id,
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(fn($e) => $e[0], $validator->errors())));
            return $this->redirect('/admin/content/pages/' . $id . '/edit');
        }

        $db->beginTransaction();

        try {
            $db->table('cms_pages')->where('id', $id)->update([
                'slug'       => $data['slug'],
                'is_active'  => (int) $data['is_active'],
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // Upsert translations
            $translations = $this->input('translations', []);
            if (is_array($translations)) {
                foreach ($translations as $svId => $trans) {
                    if (empty($trans['title'])) {
                        continue;
                    }

                    $existing = $db->table('cms_page_translations')
                        ->where('cms_page_id', $id)
                        ->where('store_view_id', (int) $svId)
                        ->first();

                    $transData = [
                        'title'            => $trans['title'],
                        'content'          => $trans['content'] ?? '',
                        'meta_title'       => $trans['meta_title'] ?? null,
                        'meta_description' => $trans['meta_description'] ?? null,
                    ];

                    if ($existing) {
                        $db->table('cms_page_translations')
                            ->where('id', $existing['id'])
                            ->update($transData);
                    } else {
                        $db->table('cms_page_translations')->insert(array_merge($transData, [
                            'cms_page_id'   => $id,
                            'store_view_id' => (int) $svId,
                        ]));
                    }
                }
            }

            $db->commit();

            $this->logActivity('update', 'cms_page', $id, $cmsPage, $data);
            Session::flash('success', 'Page updated successfully.');
            return $this->redirect('/admin/content/pages/' . $id . '/edit');

        } catch (\Throwable $e) {
            $db->rollback();
            Session::flash('error', 'Failed to update page: ' . $e->getMessage());
            return $this->redirect('/admin/content/pages/' . $id . '/edit');
        }
    }

    /**
     * Delete a CMS page and its translations.
     */
    public function deletePage(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/content/pages');
        }

        $db = Database::getInstance();

        $cmsPage = $db->table('cms_pages')->where('id', $id)->first();
        if (!$cmsPage) {
            Session::flash('error', 'Page not found.');
            return $this->redirect('/admin/content/pages');
        }

        $db->beginTransaction();

        try {
            $db->table('cms_page_translations')->where('cms_page_id', $id)->delete();
            $db->table('cms_pages')->where('id', $id)->delete();

            $db->commit();

            $this->logActivity('delete', 'cms_page', $id, $cmsPage);
            Session::flash('success', 'Page deleted successfully.');

        } catch (\Throwable $e) {
            $db->rollback();
            Session::flash('error', 'Failed to delete page: ' . $e->getMessage());
        }

        return $this->redirect('/admin/content/pages');
    }

    // ─── UI Translations ────────────────────────────────────

    /**
     * List and manage UI translations per store view.
     */
    public function translations(): Response
    {
        $db = Database::getInstance();

        $storeViewId = $this->request->query('store_view_id');
        $group       = (string) $this->request->query('group');
        $search      = (string) $this->request->query('search');
        $page        = $this->page();
        $perPage     = 50;

        // If saving translations via POST
        if ($this->request->method() === 'POST') {
            return $this->saveTranslations();
        }

        $query = $db->table('translations')
            ->orderBy('`group`', 'ASC')
            ->orderBy('`key`', 'ASC');

        if ($storeViewId) {
            $query->where('store_view_id', (int) $storeViewId);
        }

        if ($group !== '') {
            $query->where('`group`', $group);
        }

        if ($search !== '') {
            $query->whereRaw(
                "(`key` LIKE :search_0 OR value LIKE :search_1)",
                [':search_0' => "%{$search}%", ':search_1' => "%{$search}%"]
            );
        }

        $translations = $query->paginate($perPage, $page);

        // Get distinct groups for filter
        $groups = $db->table('translations')
            ->select('`group`')
            ->groupBy('`group`')
            ->get();
        $groupList = array_column($groups, 'group');

        return $this->adminView('admin/content/translations', [
            'title'        => 'Translations',
            'translations' => $translations,
            'groupList'    => $groupList,
            'storeViewId'  => $storeViewId,
            'group'        => $group,
            'search'       => $search,
        ]);
    }

    /**
     * Save posted translation values.
     */
    private function saveTranslations(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/content/translations');
        }

        $db = Database::getInstance();

        $items = $this->input('translations', []);
        if (!is_array($items)) {
            Session::flash('error', 'No translation data provided.');
            return $this->redirect('/admin/content/translations');
        }

        $db->beginTransaction();

        try {
            foreach ($items as $translationId => $value) {
                $db->table('translations')
                    ->where('id', (int) $translationId)
                    ->update(['value' => (string) $value]);
            }

            $db->commit();
            Session::flash('success', 'Translations saved successfully.');

        } catch (\Throwable $e) {
            $db->rollback();
            Session::flash('error', 'Failed to save translations: ' . $e->getMessage());
        }

        // Redirect back with same filters
        $storeViewId = $this->request->query('store_view_id');
        $group       = (string) $this->request->query('group');
        $params = [];
        if ($storeViewId) {
            $params[] = 'store_view_id=' . $storeViewId;
        }
        if ($group !== '') {
            $params[] = 'group=' . urlencode($group);
        }
        $qs = $params ? '?' . implode('&', $params) : '';

        return $this->redirect('/admin/content/translations' . $qs);
    }
}
