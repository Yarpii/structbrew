<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;

final class AttributeController extends BaseAdminController
{
    public function index(): Response
    {
        $db = Database::getInstance();
        $search = trim((string) $this->request->query('search'));

        $query = $db->table('attributes')
            ->orderBy('sort_order', 'ASC')
            ->orderBy('label', 'ASC');

        if ($search !== '') {
            $query->whereRaw('(code LIKE :search_0 OR label LIKE :search_1)', [
                ':search_0' => "%{$search}%",
                ':search_1' => "%{$search}%",
            ]);
        }

        $attributes = $query->get();

        foreach ($attributes as &$attribute) {
            $attribute['category_count'] = $db->table('category_attributes')
                ->where('attribute_id', $attribute['id'])
                ->count();
            $attribute['product_count'] = $db->table('product_attributes')
                ->where('attribute_key', $attribute['code'])
                ->count();
        }
        unset($attribute);

        return $this->adminView('admin/attributes/index', [
            'title' => 'Attributes',
            'attributes' => $attributes,
            'search' => $search,
        ]);
    }

    public function create(): Response
    {
        return $this->adminView('admin/attributes/form', [
            'title' => 'Create Attribute',
            'formAction' => '/admin/attributes',
            'attribute' => [
                'input_type' => 'text',
                'is_required' => 0,
                'is_filterable' => 0,
                'is_active' => 1,
                'sort_order' => 0,
            ],
            'optionsText' => '',
        ]);
    }

    public function store(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/attributes/create');
        }

        $data = $this->validatedInput();

        $validator = Validator::make($data, [
            'code' => 'required|slug|unique:attributes,code',
            'label' => 'required|max:255',
            'input_type' => 'required|in:text,textarea,number,boolean,select',
            'sort_order' => 'integer',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(static fn($e) => $e[0], $validator->errors())));
            return $this->redirect('/admin/attributes/create');
        }

        $db = Database::getInstance();
        $now = date('Y-m-d H:i:s');

        $attributeId = $db->table('attributes')->insert([
            'code' => $data['code'],
            'label' => $data['label'],
            'input_type' => $data['input_type'],
            'options_json' => $this->optionsToJson($data['input_type'], $data['options_text']),
            'is_required' => (int) $data['is_required'],
            'is_filterable' => (int) $data['is_filterable'],
            'is_active' => (int) $data['is_active'],
            'sort_order' => (int) $data['sort_order'],
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $this->logActivity('create', 'attribute', $attributeId, null, $data);
        Session::flash('success', 'Attribute created successfully.');

        return $this->redirect('/admin/attributes/' . $attributeId . '/edit');
    }

    public function edit(int $id): Response
    {
        $db = Database::getInstance();

        $attribute = $db->table('attributes')->where('id', $id)->first();
        if (!$attribute) {
            Session::flash('error', 'Attribute not found.');
            return $this->redirect('/admin/attributes');
        }

        $options = [];
        if (!empty($attribute['options_json'])) {
            $decoded = json_decode((string) $attribute['options_json'], true);
            if (is_array($decoded)) {
                $options = $decoded;
            }
        }

        return $this->adminView('admin/attributes/form', [
            'title' => 'Edit Attribute',
            'formAction' => '/admin/attributes/' . $id,
            'attribute' => $attribute,
            'optionsText' => implode("\n", $options),
        ]);
    }

    public function update(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/attributes/' . $id . '/edit');
        }

        $db = Database::getInstance();
        $attribute = $db->table('attributes')->where('id', $id)->first();
        if (!$attribute) {
            Session::flash('error', 'Attribute not found.');
            return $this->redirect('/admin/attributes');
        }

        $data = $this->validatedInput();

        $validator = Validator::make($data, [
            'code' => 'required|slug|unique:attributes,code,' . $id,
            'label' => 'required|max:255',
            'input_type' => 'required|in:text,textarea,number,boolean,select',
            'sort_order' => 'integer',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(static fn($e) => $e[0], $validator->errors())));
            return $this->redirect('/admin/attributes/' . $id . '/edit');
        }

        $db->beginTransaction();

        try {
            $db->table('attributes')->where('id', $id)->update([
                'code' => $data['code'],
                'label' => $data['label'],
                'input_type' => $data['input_type'],
                'options_json' => $this->optionsToJson($data['input_type'], $data['options_text']),
                'is_required' => (int) $data['is_required'],
                'is_filterable' => (int) $data['is_filterable'],
                'is_active' => (int) $data['is_active'],
                'sort_order' => (int) $data['sort_order'],
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            if ($attribute['code'] !== $data['code']) {
                $db->table('product_attributes')
                    ->where('attribute_key', $attribute['code'])
                    ->update(['attribute_key' => $data['code']]);
            }

            $db->commit();

            $this->logActivity('update', 'attribute', $id, $attribute, $data);
            Session::flash('success', 'Attribute updated successfully.');

            return $this->redirect('/admin/attributes/' . $id . '/edit');
        } catch (\Throwable $e) {
            $db->rollback();
            Session::flash('error', 'Failed to update attribute: ' . $e->getMessage());
            return $this->redirect('/admin/attributes/' . $id . '/edit');
        }
    }

    public function delete(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/attributes');
        }

        $db = Database::getInstance();
        $attribute = $db->table('attributes')->where('id', $id)->first();
        if (!$attribute) {
            Session::flash('error', 'Attribute not found.');
            return $this->redirect('/admin/attributes');
        }

        $db->beginTransaction();

        try {
            $db->table('category_attributes')->where('attribute_id', $id)->delete();
            $db->table('product_attributes')->where('attribute_key', $attribute['code'])->delete();
            $db->table('attributes')->where('id', $id)->delete();

            $db->commit();

            $this->logActivity('delete', 'attribute', $id, $attribute);
            Session::flash('success', 'Attribute deleted successfully.');
        } catch (\Throwable $e) {
            $db->rollback();
            Session::flash('error', 'Failed to delete attribute: ' . $e->getMessage());
        }

        return $this->redirect('/admin/attributes');
    }

    private function validatedInput(): array
    {
        return [
            'code' => trim((string) $this->input('code', '')),
            'label' => trim((string) $this->input('label', '')),
            'input_type' => (string) $this->input('input_type', 'text'),
            'options_text' => trim((string) $this->input('options_text', '')),
            'is_required' => $this->input('is_required', '0'),
            'is_filterable' => $this->input('is_filterable', '0'),
            'is_active' => $this->input('is_active', '0'),
            'sort_order' => $this->input('sort_order', '0'),
        ];
    }

    private function optionsToJson(string $inputType, string $optionsText): ?string
    {
        if ($inputType !== 'select') {
            return null;
        }

        $rows = preg_split('/\r\n|\r|\n/', $optionsText) ?: [];
        $options = [];
        foreach ($rows as $row) {
            $option = trim($row);
            if ($option !== '') {
                $options[] = $option;
            }
        }

        return empty($options) ? null : json_encode(array_values(array_unique($options)));
    }
}
