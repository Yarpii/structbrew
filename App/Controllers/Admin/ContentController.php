<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Core\Translator;
use App\Core\Validator;
use PDO;

final class ContentController extends BaseAdminController
{
    // ??? Pages ???????????????????????????????????????????????

    public function pages(): Response
    {
        $db = Database::getInstance();

        $page    = $this->page();
        $perPage = 20;
        $search  = (string) $this->request->query('q', '');
        $status  = (string) $this->request->query('status', '');

        $query = $db->table('cms_pages');

        if ($search !== '') {
            $query = $query->whereRaw('slug LIKE :search', [':search' => "%{$search}%"]);
        }

        if ($status === 'active') {
            $query = $query->where('is_active', 1);
        } elseif ($status === 'inactive') {
            $query = $query->where('is_active', 0);
        }

        $pages = $query->orderBy('created_at', 'DESC')->paginate($perPage, $page);

        $totalStoreViews = count($this->storeViews);
        foreach ($pages['data'] as &$p) {
            $trans = $db->table('cms_page_translations')
                ->where('cms_page_id', $p['id'])
                ->first();
            $p['translation'] = $trans;

            $p['translation_count'] = (int) $db->table('cms_page_translations')
                ->where('cms_page_id', $p['id'])
                ->count();
        }
        unset($p);

        return $this->adminView('admin/content/pages', [
            'title'           => 'CMS Pages',
            'pages'           => $pages,
            'totalStoreViews' => $totalStoreViews,
        ]);
    }

    public function createPage(): Response
    {
        return $this->adminView('admin/content/page_form', [
            'title'        => 'Create Page',
            'page'         => [],
            'translations' => [],
            'formAction'   => '/admin/content/pages',
        ]);
    }

    public function storePage(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect('/admin/content/pages/create');
        }

        $slug     = trim((string) $this->input('slug', ''));
        $isActive = $this->input('is_active') ? 1 : 0;
        $translations = $this->input('translations', []);

        $v = new Validator(['slug' => $slug], ['slug' => 'required|max:255']);
        if (!$v->passes()) {
            Session::flash('error', implode(' ', array_merge(...array_values($v->errors()))));
            return $this->redirect('/admin/content/pages/create');
        }

        $db = Database::getInstance();

        $existing = $db->table('cms_pages')->where('slug', $slug)->first();
        if ($existing) {
            Session::flash('error', 'A page with this slug already exists.');
            return $this->redirect('/admin/content/pages/create');
        }

        $pageId = $db->table('cms_pages')->insert([
            'slug'      => $slug,
            'is_active' => $isActive,
        ]);

        if (is_array($translations)) {
            foreach ($translations as $storeViewId => $trans) {
                $title   = trim((string) ($trans['title'] ?? ''));
                $content = trim((string) ($trans['content'] ?? ''));
                if ($title === '' && $content === '') {
                    continue;
                }
                $db->table('cms_page_translations')->insert([
                    'cms_page_id'      => $pageId,
                    'store_view_id'    => (int) $storeViewId,
                    'title'            => $title,
                    'content'          => $content,
                    'meta_title'       => trim((string) ($trans['meta_title'] ?? '')) ?: null,
                    'meta_description' => trim((string) ($trans['meta_description'] ?? '')) ?: null,
                ]);
            }
        }

        $action = $this->input('action', 'save');
        if ($action === 'save_continue') {
            return $this->redirect('/admin/content/pages/' . $pageId . '/edit');
        }

        Session::flash('success', 'Page created.');
        return $this->redirect('/admin/content/pages');
    }

    public function editPage(int $id): Response
    {
        $db = Database::getInstance();

        $page = $db->table('cms_pages')->where('id', $id)->first();
        if (!$page) {
            Session::flash('error', 'Page not found.');
            return $this->redirect('/admin/content/pages');
        }

        $transRows = $db->table('cms_page_translations')
            ->where('cms_page_id', $id)
            ->get();

        $translations = [];
        foreach ($transRows as $row) {
            $translations[(int) $row['store_view_id']] = $row;
        }

        return $this->adminView('admin/content/page_form', [
            'title'        => 'Edit Page',
            'page'         => $page,
            'translations' => $translations,
            'formAction'   => '/admin/content/pages/' . $id,
        ]);
    }

    public function updatePage(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect('/admin/content/pages/' . $id . '/edit');
        }

        $db = Database::getInstance();

        $page = $db->table('cms_pages')->where('id', $id)->first();
        if (!$page) {
            Session::flash('error', 'Page not found.');
            return $this->redirect('/admin/content/pages');
        }

        $slug         = trim((string) $this->input('slug', ''));
        $isActive     = $this->input('is_active') ? 1 : 0;
        $translations = $this->input('translations', []);

        $v = new Validator(['slug' => $slug], ['slug' => 'required|max:255']);
        if (!$v->passes()) {
            Session::flash('error', implode(' ', array_merge(...array_values($v->errors()))));
            return $this->redirect('/admin/content/pages/' . $id . '/edit');
        }

        $existing = $db->table('cms_pages')
            ->where('slug', $slug)
            ->whereRaw('id != :excludeId', [':excludeId' => $id])
            ->first();
        if ($existing) {
            Session::flash('error', 'A page with this slug already exists.');
            return $this->redirect('/admin/content/pages/' . $id . '/edit');
        }

        $db->table('cms_pages')->where('id', $id)->update([
            'slug'      => $slug,
            'is_active' => $isActive,
        ]);

        if (is_array($translations)) {
            foreach ($translations as $storeViewId => $trans) {
                $title           = trim((string) ($trans['title'] ?? ''));
                $content         = trim((string) ($trans['content'] ?? ''));
                $metaTitle       = trim((string) ($trans['meta_title'] ?? '')) ?: null;
                $metaDescription = trim((string) ($trans['meta_description'] ?? '')) ?: null;

                $existingTrans = $db->table('cms_page_translations')
                    ->where('cms_page_id', $id)
                    ->where('store_view_id', (int) $storeViewId)
                    ->first();

                if ($existingTrans) {
                    $db->table('cms_page_translations')
                        ->where('id', $existingTrans['id'])
                        ->update([
                            'title'            => $title,
                            'content'          => $content,
                            'meta_title'       => $metaTitle,
                            'meta_description' => $metaDescription,
                        ]);
                } elseif ($title !== '' || $content !== '') {
                    $db->table('cms_page_translations')->insert([
                        'cms_page_id'      => $id,
                        'store_view_id'    => (int) $storeViewId,
                        'title'            => $title,
                        'content'          => $content,
                        'meta_title'       => $metaTitle,
                        'meta_description' => $metaDescription,
                    ]);
                }
            }
        }

        $action = $this->input('action', 'save');
        if ($action === 'save_continue') {
            Session::flash('success', 'Page updated.');
            return $this->redirect('/admin/content/pages/' . $id . '/edit');
        }

        Session::flash('success', 'Page updated.');
        return $this->redirect('/admin/content/pages');
    }

    public function deletePage(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect('/admin/content/pages');
        }

        $db   = Database::getInstance();
        $page = $db->table('cms_pages')->where('id', $id)->first();
        if (!$page) {
            Session::flash('error', 'Page not found.');
            return $this->redirect('/admin/content/pages');
        }

        $db->table('cms_pages')->where('id', $id)->delete();

        Session::flash('success', 'Page deleted.');
        return $this->redirect('/admin/content/pages');
    }

    // ??? UI Translations ?????????????????????????????????????

    public function translations(): Response
    {
        $db = Database::getInstance();

        $selectedStoreViewId = (int) $this->request->query('store_view_id', 0);
        $group               = (string) $this->request->query('group', '');
        $search              = (string) $this->request->query('q', '');
        $status              = (string) $this->request->query('status', '');
        $page                = $this->page();
        $perPage             = 50;

        // Resolve master store view (is_default = 1)
        $masterStoreView = null;
        foreach ($this->storeViews as $sv) {
            if ((int) $sv['is_default'] === 1) {
                $masterStoreView = $sv;
                break;
            }
        }
        $masterStoreViewId = $masterStoreView ? (int) $masterStoreView['id'] : 0;

        // Find the selected store view
        $selectedStoreView = null;
        foreach ($this->storeViews as $sv) {
            if ((int) $sv['id'] === $selectedStoreViewId) {
                $selectedStoreView = $sv;
                break;
            }
        }

        $isMasterSelected = $selectedStoreViewId > 0 && $selectedStoreViewId === $masterStoreViewId;

        // Load all canonical keys from the en_US locale folder
        $allLocaleKeys = $this->loadLocaleKeys();
        $totalKeys     = count($allLocaleKeys);

        // Seed any missing DB rows for the selected store view
        if ($selectedStoreView !== null && $totalKeys > 0) {
            $this->seedTranslations($db, $selectedStoreViewId, $allLocaleKeys);
            // Also ensure all master keys exist in this store view (covers custom keys added via admin)
            if (!$isMasterSelected && $masterStoreViewId > 0) {
                $masterKeys = $db->raw(
                    'SELECT `key`, `group` FROM translations WHERE store_view_id = :mid',
                    [':mid' => $masterStoreViewId]
                )->fetchAll();
                if (!empty($masterKeys)) {
                    $this->seedTranslations($db, $selectedStoreViewId, $masterKeys);
                }
            }
        }

        // Total key count = master DB rows (covers both locale-file keys and custom keys)
        if ($masterStoreViewId > 0) {
            $totalKeys = (int) $db->table('translations')->where('store_view_id', $masterStoreViewId)->count();
        }

        // Stats per store view: translated = rows with non-empty value
        $statsRows = $db->raw(
            "SELECT store_view_id, SUM(value != '') AS translated FROM translations GROUP BY store_view_id"
        )->fetchAll();

        $translatedByView = [];
        foreach ($statsRows as $row) {
            $translatedByView[(int) $row['store_view_id']] = (int) $row['translated'];
        }

        $stats = [];
        foreach ($this->storeViews as $sv) {
            $svId       = (int) $sv['id'];
            $translated = $translatedByView[$svId] ?? 0;
            $stats[$svId] = [
                'total'      => $totalKeys,
                'translated' => $translated,
                'missing'    => $totalKeys - $translated,
                'percentage' => $totalKeys > 0 ? (int) round($translated / $totalKeys * 100) : 0,
            ];
        }

        // Group list for filter dropdown (from locale files + any custom groups in DB)
        $groups = Translator::availableGroups('en_US');
        if ($masterStoreViewId > 0) {
            $dbGroups = $db->raw(
                'SELECT DISTINCT `group` FROM translations WHERE store_view_id = :mid ORDER BY `group`',
                [':mid' => $masterStoreViewId]
            )->fetchAll(PDO::FETCH_COLUMN);
            $groups = array_values(array_unique(array_merge($groups, $dbGroups)));
        }
        sort($groups);

        // Paginate translations for the selected store view
        $translationKeys = [];
        $pagination = [
            'last_page'    => 1,
            'from'         => 0,
            'to'           => 0,
            'total'        => 0,
            'current_page' => 1,
        ];

        if ($selectedStoreView !== null) {
            $query = $db->table('translations')
                ->where('store_view_id', $selectedStoreViewId);

            if ($group !== '') {
                $query = $query->whereRaw('`group` = :grp', [':grp' => $group]);
            }

            if ($search !== '') {
                $query = $query->whereRaw(
                    '(`key` LIKE :srch1 OR value LIKE :srch2)',
                    [':srch1' => "%{$search}%", ':srch2' => "%{$search}%"]
                );
            }

            if ($status === 'translated') {
                $query = $query->whereRaw("value != ''");
            } elseif ($status === 'missing') {
                $query = $query->whereRaw("value = ''");
            }

            $result          = $query->orderBy('`group`')->orderBy('`key`')->paginate($perPage, $page);
            $translationKeys = $result['data'];
            $pagination      = array_diff_key($result, ['data' => null]);
        }

        return $this->adminView('admin/content/translations', [
            'title'               => 'UI Translations',
            'selectedStoreViewId' => $selectedStoreViewId,
            'selectedStoreView'   => $selectedStoreView,
            'masterStoreViewId'   => $masterStoreViewId,
            'isMasterSelected'    => $isMasterSelected,
            'stats'               => $stats,
            'groups'              => $groups,
            'group'               => $group,
            'search'              => $search,
            'status'              => $status,
            'translationKeys'     => $translationKeys,
            'pagination'          => $pagination,
        ]);
    }

    public function addTranslationKey(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect('/admin/content/translations');
        }

        $db = Database::getInstance();

        // Only the master store view may create new keys
        $masterStoreView = $db->table('store_views')->where('is_default', 1)->first();
        if (!$masterStoreView) {
            Session::flash('error', 'No default store view is configured.');
            return $this->redirect('/admin/content/translations');
        }
        $masterStoreViewId = (int) $masterStoreView['id'];

        $storeViewId = (int) $this->input('store_view_id');
        if ($storeViewId !== $masterStoreViewId) {
            Session::flash('error', 'New keys can only be added from the master store view.');
            return $this->redirect('/admin/content/translations?store_view_id=' . $storeViewId);
        }

        $key   = trim((string) $this->input('key', ''));
        $group = trim((string) $this->input('group', ''));
        $value = trim((string) $this->input('value', ''));

        if ($key === '' || $group === '') {
            Session::flash('error', 'Key and group are required.');
            return $this->redirect('/admin/content/translations?store_view_id=' . $storeViewId);
        }

        // Validate key format: only lowercase letters, digits and underscores
        if (!preg_match('/^[a-z0-9_]+$/', $key)) {
            Session::flash('error', 'Key may only contain lowercase letters, digits and underscores.');
            return $this->redirect('/admin/content/translations?store_view_id=' . $storeViewId);
        }

        // Check for duplicate in master
        $existing = $db->raw(
            'SELECT id FROM translations WHERE store_view_id = :svid AND `key` = :k AND `group` = :g',
            [':svid' => $masterStoreViewId, ':k' => $key, ':g' => $group]
        )->fetch();

        if ($existing) {
            Session::flash('error', "Key \"{$group}.{$key}\" already exists.");
            return $this->redirect('/admin/content/translations?store_view_id=' . $storeViewId);
        }

        // Insert into master with the provided value
        $db->raw(
            'INSERT INTO translations (store_view_id, `key`, value, `group`) VALUES (:svid, :k, :val, :g)',
            [':svid' => $masterStoreViewId, ':k' => $key, ':val' => $value, ':g' => $group]
        );

        // Propagate an empty row to every other store view
        $otherViews = $db->table('store_views')
            ->whereRaw('id != :mid', [':mid' => $masterStoreViewId])
            ->where('is_active', 1)
            ->get();

        foreach ($otherViews as $sv) {
            $db->raw(
                'INSERT IGNORE INTO translations (store_view_id, `key`, value, `group`) VALUES (:svid, :k, \'\', :g)',
                [':svid' => (int) $sv['id'], ':k' => $key, ':g' => $group]
            );
        }

        Session::flash('success', "Key \"{$group}.{$key}\" added and propagated to all store views.");
        return $this->redirect('/admin/content/translations?store_view_id=' . $storeViewId);
    }

    public function deleteTranslationKey(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect('/admin/content/translations');
        }

        $db = Database::getInstance();

        // Only master may delete keys
        $masterStoreView = $db->table('store_views')->where('is_default', 1)->first();
        if (!$masterStoreView) {
            Session::flash('error', 'No default store view is configured.');
            return $this->redirect('/admin/content/translations');
        }
        $masterStoreViewId = (int) $masterStoreView['id'];

        $storeViewId = (int) $this->input('store_view_id');
        if ($storeViewId !== $masterStoreViewId) {
            Session::flash('error', 'Keys can only be deleted from the master store view.');
            return $this->redirect('/admin/content/translations?store_view_id=' . $storeViewId);
        }

        // Find the master row to get key + group
        $row = $db->table('translations')
            ->where('id', $id)
            ->where('store_view_id', $masterStoreViewId)
            ->first();

        if (!$row) {
            Session::flash('error', 'Translation key not found.');
            return $this->redirect('/admin/content/translations?store_view_id=' . $storeViewId);
        }

        // Delete the key from ALL store views
        $db->raw(
            'DELETE FROM translations WHERE `key` = :k AND `group` = :g',
            [':k' => $row['key'], ':g' => $row['group']]
        );

        Session::flash('success', "Key \"{$row['group']}.{$row['key']}\" deleted from all store views.");
        return $this->redirect('/admin/content/translations?store_view_id=' . $storeViewId);
    }

    public function saveTranslations(): Response
    {
        if (!$this->verifyCsrf()) {
            return Response::json(['error' => 'Invalid CSRF token'], 403);
        }

        $keyId       = (int) $this->input('key_id');
        $storeViewId = (int) $this->input('store_view_id');
        $value       = (string) ($this->input('value') ?? '');

        if ($keyId <= 0 || $storeViewId <= 0) {
            return Response::json(['error' => 'Invalid data'], 422);
        }

        $db      = Database::getInstance();
        $updated = $db->table('translations')
            ->where('id', $keyId)
            ->where('store_view_id', $storeViewId)
            ->update(['value' => $value]);

        if ($updated === 0) {
            return Response::json(['error' => 'Translation not found'], 404);
        }

        return Response::json(['success' => true]);
    }

    // ??? Export ??????????????????????????????????????????????

    public function exportTranslations(): Response
    {
        $storeViewId = (int) $this->request->query('store_view_id', 0);

        if ($storeViewId <= 0) {
            Session::flash('error', 'No store view selected for export.');
            return $this->redirect('/admin/content/translations');
        }

        $db = Database::getInstance();

        $storeView = $db->table('store_views')->where('id', $storeViewId)->first();
        if (!$storeView) {
            Session::flash('error', 'Store view not found.');
            return $this->redirect('/admin/content/translations');
        }

        $rows = $db->table('translations')
            ->where('store_view_id', $storeViewId)
            ->orderBy('`group`')
            ->orderBy('`key`')
            ->get();

        // Build CSV in memory
        $buf = fopen('php://temp', 'r+');
        fputcsv($buf, ['group', 'key', 'value']);
        foreach ($rows as $row) {
            fputcsv($buf, [$row['group'], $row['key'], $row['value']]);
        }
        rewind($buf);
        $csv = stream_get_contents($buf);
        fclose($buf);

        $locale   = preg_replace('/[^a-zA-Z0-9_\-]/', '', $storeView['locale'] ?? 'locale');
        $filename = 'translations_' . $locale . '_' . date('Ymd') . '.csv';

        return Response::text($csv)
            ->header('Content-Type', 'text/csv; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    // ??? Import ??????????????????????????????????????????????

    public function importTranslations(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect('/admin/content/translations');
        }

        $storeViewId = (int) $this->input('store_view_id');

        if ($storeViewId <= 0) {
            Session::flash('error', 'No store view selected.');
            return $this->redirect('/admin/content/translations');
        }

        $file = $_FILES['csv_file'] ?? null;
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Please upload a valid CSV file.');
            return $this->redirect('/admin/content/translations?store_view_id=' . $storeViewId);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext !== 'csv') {
            Session::flash('error', 'Only CSV files are supported.');
            return $this->redirect('/admin/content/translations?store_view_id=' . $storeViewId);
        }

        $handle = fopen($file['tmp_name'], 'r');
        if ($handle === false) {
            Session::flash('error', 'Could not read the uploaded file.');
            return $this->redirect('/admin/content/translations?store_view_id=' . $storeViewId);
        }

        // Read and validate header row
        $header = fgetcsv($handle);
        if ($header === false || array_map('strtolower', array_map('trim', $header)) !== ['group', 'key', 'value']) {
            fclose($handle);
            Session::flash('error', 'Invalid CSV format. Expected columns: group, key, value.');
            return $this->redirect('/admin/content/translations?store_view_id=' . $storeViewId);
        }

        $db      = Database::getInstance();
        $updated = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 3) {
                $skipped++;
                continue;
            }
            [$group, $key, $value] = $row;
            $group = trim($group);
            $key   = trim($key);

            if ($group === '' || $key === '') {
                $skipped++;
                continue;
            }

            // Upsert: insert new row or update value of existing one
            $db->raw(
                'INSERT INTO translations (store_view_id, `key`, value, `group`)
                 VALUES (:svid, :key, :val, :grp)
                 ON DUPLICATE KEY UPDATE value = VALUES(value)',
                [
                    ':svid' => $storeViewId,
                    ':key'  => $key,
                    ':val'  => $value,
                    ':grp'  => $group,
                ]
            );
            $updated++;
        }

        fclose($handle);

        Session::flash('success', "Import complete: {$updated} translations updated, {$skipped} rows skipped.");
        return $this->redirect('/admin/content/translations?store_view_id=' . $storeViewId);
    }

    // ??? Private Helpers ?????????????????????????????????????

    /**
     * Load all canonical translation keys from the en_US locale folder.
     * Returns array of ['key' => string, 'group' => string].
     */
    private function loadLocaleKeys(): array
    {
        $localeDir = dirname(__DIR__, 2) . '/Locale/en_US';
        $keys      = [];

        if (!is_dir($localeDir)) {
            return $keys;
        }

        foreach (glob($localeDir . '/*.php') as $file) {
            $group = basename($file, '.php');
            $data  = require $file;
            if (is_array($data)) {
                foreach (array_keys($data) as $k) {
                    $keys[] = ['key' => $k, 'group' => $group];
                }
            }
        }

        return $keys;
    }

    /**
     * Insert missing translation rows (with empty value) for the given store view.
     * Uses INSERT IGNORE so existing translated values are never overwritten.
     */
    private function seedTranslations(Database $db, int $storeViewId, array $allKeys): void
    {
        $existing = $db->raw(
            'SELECT `key`, `group` FROM translations WHERE store_view_id = :svid',
            [':svid' => $storeViewId]
        )->fetchAll();

        $existingSet = [];
        foreach ($existing as $row) {
            $existingSet[$row['group'] . '.' . $row['key']] = true;
        }

        $toInsert = [];
        foreach ($allKeys as $k) {
            if (!isset($existingSet[$k['group'] . '.' . $k['key']])) {
                $toInsert[] = $k;
            }
        }

        if (empty($toInsert)) {
            return;
        }

        // Batch in chunks of 200 to avoid overly large queries
        foreach (array_chunk($toInsert, 200) as $chunk) {
            $placeholders = [];
            $bindings     = [];
            foreach ($chunk as $i => $k) {
                $placeholders[] = "(:sv{$i}, :key{$i}, '', :grp{$i})";
                $bindings[":sv{$i}"]  = $storeViewId;
                $bindings[":key{$i}"] = $k['key'];
                $bindings[":grp{$i}"] = $k['group'];
            }
            $sql = 'INSERT IGNORE INTO translations (store_view_id, `key`, value, `group`) VALUES '
                . implode(', ', $placeholders);
            $db->raw($sql, $bindings);
        }
    }
}
