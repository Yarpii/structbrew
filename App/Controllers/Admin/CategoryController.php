<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;

final class CategoryController extends BaseAdminController
{
    /**
     * List all categories with parent hierarchy.
     */
    public function index(): Response
    {
        $db = Database::getInstance();

        $categories = $db->table('categories')
            ->orderBy('position', 'ASC')
            ->get();

        // Build a lookup of parent names and translations
        $categoryMap = [];
        foreach ($categories as $cat) {
            $categoryMap[(int) $cat['id']] = $cat;
        }

        foreach ($categories as &$category) {
            $category['translation'] = $db->table('category_translations')
                ->where('category_id', $category['id'])
                ->first();

            $category['parent_name'] = null;
            if ($category['parent_id']) {
                $parent = $categoryMap[(int) $category['parent_id']] ?? null;
                if ($parent) {
                    $parentTrans = $db->table('category_translations')
                        ->where('category_id', $parent['id'])
                        ->first();
                    $category['parent_name'] = $parentTrans['name'] ?? ('ID: ' . $parent['id']);
                }
            }

            $productCount = $db->table('product_categories')
                ->where('category_id', $category['id'])
                ->count();
            $category['product_count'] = $productCount;
        }
        unset($category);

        return $this->adminView('admin/categories/index', [
            'title'      => 'Categories',
            'categories' => $categories,
        ]);
    }

    /**
     * Show category creation form.
     */
    public function create(): Response
    {
        $db = Database::getInstance();

        $parentCategories = $db->table('categories')
            ->whereNull('parent_id')
            ->orderBy('position', 'ASC')
            ->get();

        foreach ($parentCategories as &$cat) {
            $cat['translation'] = $db->table('category_translations')
                ->where('category_id', $cat['id'])
                ->first();
        }
        unset($cat);

        return $this->adminView('admin/categories/create', [
            'title'            => 'Create Category',
            'parentCategories' => $parentCategories,
        ]);
    }

    /**
     * Store a new category with translations.
     */
    public function store(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/categories/create');
        }

        $data = [
            'slug'      => (string) $this->input('slug', ''),
            'parent_id' => $this->input('parent_id'),
            'position'  => $this->input('position', '0'),
            'is_active' => $this->input('is_active', '0'),
            'image'     => $this->input('image'),
        ];

        $validator = Validator::make($data, [
            'slug' => 'required|slug|unique:categories,slug',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(fn($e) => $e[0], $validator->errors())));
            return $this->redirect('/admin/categories/create');
        }

        $db = Database::getInstance();
        $db->beginTransaction();

        try {
            $now = date('Y-m-d H:i:s');
            $categoryId = $db->table('categories')->insert([
                'slug'       => $data['slug'],
                'parent_id'  => $data['parent_id'] ?: null,
                'position'   => (int) $data['position'],
                'is_active'  => (int) $data['is_active'],
                'image'      => $data['image'] ?: null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Save translations per store view
            $translations = $this->input('translations', []);
            if (is_array($translations)) {
                foreach ($translations as $svId => $trans) {
                    if (empty($trans['name'])) {
                        continue;
                    }
                    $db->table('category_translations')->insert([
                        'category_id'    => $categoryId,
                        'store_view_id'  => (int) $svId,
                        'name'           => $trans['name'],
                        'description'    => $trans['description'] ?? null,
                        'meta_title'     => $trans['meta_title'] ?? null,
                        'meta_description' => $trans['meta_description'] ?? null,
                    ]);
                }
            }

            $db->commit();

            $this->logActivity('create', 'category', $categoryId, null, $data);
            Session::flash('success', 'Category created successfully.');
            return $this->redirect('/admin/categories');

        } catch (\Throwable $e) {
            $db->rollback();
            Session::flash('error', 'Failed to create category: ' . $e->getMessage());
            return $this->redirect('/admin/categories/create');
        }
    }

    /**
     * Show category edit form.
     */
    public function edit(int $id): Response
    {
        $db = Database::getInstance();

        $category = $db->table('categories')->where('id', $id)->first();
        if (!$category) {
            Session::flash('error', 'Category not found.');
            return $this->redirect('/admin/categories');
        }

        $parentCategories = $db->table('categories')
            ->whereNull('parent_id')
            ->where('id', '!=', $id)
            ->orderBy('position', 'ASC')
            ->get();

        foreach ($parentCategories as &$cat) {
            $cat['translation'] = $db->table('category_translations')
                ->where('category_id', $cat['id'])
                ->first();
        }
        unset($cat);

        // Load translations keyed by store_view_id
        $translationRows = $db->table('category_translations')
            ->where('category_id', $id)
            ->get();
        $translations = [];
        foreach ($translationRows as $row) {
            $translations[(int) $row['store_view_id']] = $row;
        }

        return $this->adminView('admin/categories/edit', [
            'title'            => 'Edit Category',
            'category'         => $category,
            'parentCategories' => $parentCategories,
            'translations'     => $translations,
        ]);
    }

    /**
     * Update an existing category and its translations.
     */
    public function update(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/categories/' . $id . '/edit');
        }

        $db = Database::getInstance();

        $category = $db->table('categories')->where('id', $id)->first();
        if (!$category) {
            Session::flash('error', 'Category not found.');
            return $this->redirect('/admin/categories');
        }

        $data = [
            'slug'      => (string) $this->input('slug', ''),
            'parent_id' => $this->input('parent_id'),
            'position'  => $this->input('position', '0'),
            'is_active' => $this->input('is_active', '0'),
            'image'     => $this->input('image'),
        ];

        $validator = Validator::make($data, [
            'slug' => 'required|slug|unique:categories,slug,' . $id,
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(fn($e) => $e[0], $validator->errors())));
            return $this->redirect('/admin/categories/' . $id . '/edit');
        }

        $db->beginTransaction();

        try {
            $db->table('categories')->where('id', $id)->update([
                'slug'       => $data['slug'],
                'parent_id'  => $data['parent_id'] ?: null,
                'position'   => (int) $data['position'],
                'is_active'  => (int) $data['is_active'],
                'image'      => $data['image'] ?: null,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // Upsert translations
            $translations = $this->input('translations', []);
            if (is_array($translations)) {
                foreach ($translations as $svId => $trans) {
                    if (empty($trans['name'])) {
                        continue;
                    }

                    $existing = $db->table('category_translations')
                        ->where('category_id', $id)
                        ->where('store_view_id', (int) $svId)
                        ->first();

                    $transData = [
                        'name'             => $trans['name'],
                        'description'      => $trans['description'] ?? null,
                        'meta_title'       => $trans['meta_title'] ?? null,
                        'meta_description' => $trans['meta_description'] ?? null,
                    ];

                    if ($existing) {
                        $db->table('category_translations')
                            ->where('id', $existing['id'])
                            ->update($transData);
                    } else {
                        $db->table('category_translations')->insert(array_merge($transData, [
                            'category_id'   => $id,
                            'store_view_id' => (int) $svId,
                        ]));
                    }
                }
            }

            $db->commit();

            $this->logActivity('update', 'category', $id, $category, $data);
            Session::flash('success', 'Category updated successfully.');
            return $this->redirect('/admin/categories/' . $id . '/edit');

        } catch (\Throwable $e) {
            $db->rollback();
            Session::flash('error', 'Failed to update category: ' . $e->getMessage());
            return $this->redirect('/admin/categories/' . $id . '/edit');
        }
    }

    /**
     * Delete a category and its translations.
     */
    public function delete(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/categories');
        }

        $db = Database::getInstance();

        $category = $db->table('categories')->where('id', $id)->first();
        if (!$category) {
            Session::flash('error', 'Category not found.');
            return $this->redirect('/admin/categories');
        }

        // Check for child categories
        $childCount = $db->table('categories')->where('parent_id', $id)->count();
        if ($childCount > 0) {
            Session::flash('error', 'Cannot delete a category that has child categories. Remove children first.');
            return $this->redirect('/admin/categories');
        }

        $db->beginTransaction();

        try {
            $db->table('product_categories')->where('category_id', $id)->delete();
            $db->table('category_translations')->where('category_id', $id)->delete();
            $db->table('categories')->where('id', $id)->delete();

            $db->commit();

            $this->logActivity('delete', 'category', $id, $category);
            Session::flash('success', 'Category deleted successfully.');

        } catch (\Throwable $e) {
            $db->rollback();
            Session::flash('error', 'Failed to delete category: ' . $e->getMessage());
        }

        return $this->redirect('/admin/categories');
    }
}
