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
            'title'       => 'Create Attribute',
            'formAction'  => '/admin/attributes',
            'attribute'   => [
                'input_type'   => 'text',
                'is_required'  => 0,
                'is_filterable'=> 0,
                'is_active'    => 1,
                'sort_order'   => 0,
            ],
            'optionsText'   => '',
            'swatchOptions' => [],
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
            'code'       => 'required|slug|unique:attributes,code',
            'label'      => 'required|max:255',
            'input_type' => 'required|in:text,textarea,number,boolean,select,swatch_color,swatch_image,multi_select',
            'sort_order' => 'integer',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(static fn($e) => $e[0], $validator->errors())));
            return $this->redirect('/admin/attributes/create');
        }

        $db  = Database::getInstance();
        $now = date('Y-m-d H:i:s');

        $db->beginTransaction();
        try {
            $attributeId = $db->table('attributes')->insert([
                'code'         => $data['code'],
                'label'        => $data['label'],
                'input_type'   => $data['input_type'],
                'options_json' => $this->optionsToJson($data['input_type'], $data['options_text']),
                'is_required'  => (int) $data['is_required'],
                'is_filterable'=> (int) $data['is_filterable'],
                'is_active'    => (int) $data['is_active'],
                'sort_order'   => (int) $data['sort_order'],
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);

            $this->syncSwatchOptions($db, (int) $attributeId, $data['input_type']);
            $db->commit();
        } catch (\Throwable $e) {
            $db->rollback();
            Session::flash('error', 'Failed to create attribute: ' . $e->getMessage());
            return $this->redirect('/admin/attributes/create');
        }

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

        $swatchOptions = Database::getInstance()
            ->table('attribute_swatch_options')
            ->where('attribute_id', $id)
            ->orderBy('sort_order', 'ASC')
            ->get();

        return $this->adminView('admin/attributes/form', [
            'title'         => 'Edit Attribute',
            'formAction'    => '/admin/attributes/' . $id,
            'attribute'     => $attribute,
            'optionsText'   => implode("\n", $options),
            'swatchOptions' => $swatchOptions,
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
            'code'       => 'required|slug|unique:attributes,code,' . $id,
            'label'      => 'required|max:255',
            'input_type' => 'required|in:text,textarea,number,boolean,select,swatch_color,swatch_image,multi_select',
            'sort_order' => 'integer',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(static fn($e) => $e[0], $validator->errors())));
            return $this->redirect('/admin/attributes/' . $id . '/edit');
        }

        $db->beginTransaction();

        try {
            $db->table('attributes')->where('id', $id)->update([
                'code'         => $data['code'],
                'label'        => $data['label'],
                'input_type'   => $data['input_type'],
                'options_json' => $this->optionsToJson($data['input_type'], $data['options_text']),
                'is_required'  => (int) $data['is_required'],
                'is_filterable'=> (int) $data['is_filterable'],
                'is_active'    => (int) $data['is_active'],
                'sort_order'   => (int) $data['sort_order'],
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);

            if ($attribute['code'] !== $data['code']) {
                $db->table('product_attributes')
                    ->where('attribute_key', $attribute['code'])
                    ->update(['attribute_key' => $data['code']]);
            }

            $this->syncSwatchOptions($db, $id, $data['input_type']);
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

        $rows    = preg_split('/\r\n|\r|\n/', $optionsText) ?: [];
        $options = [];
        foreach ($rows as $row) {
            $option = trim($row);
            if ($option !== '') {
                $options[] = $option;
            }
        }

        return empty($options) ? null : json_encode(array_values(array_unique($options)));
    }

    private function syncSwatchOptions(Database $db, int $attributeId, string $inputType): void
    {
        if (!in_array($inputType, ['swatch_color', 'swatch_image', 'multi_select'], true)) {
            return;
        }

        $labels     = (array) ($this->input('swatch_labels', []));
        $values     = (array) ($this->input('swatch_values', []));
        $existingIds= (array) ($this->input('swatch_ids', []));
        $files      = $_FILES['swatch_images'] ?? [];

        // Delete options that were removed (not in submitted swatch_ids)
        $keepIds = array_filter(array_map('intval', $existingIds));
        if (!empty($keepIds)) {
            $placeholders = implode(',', array_fill(0, count($keepIds), '?'));
            $db->statement(
                "DELETE FROM attribute_swatch_options WHERE attribute_id = ? AND id NOT IN ({$placeholders})",
                array_merge([$attributeId], $keepIds)
            );
        } else {
            $db->table('attribute_swatch_options')->where('attribute_id', $attributeId)->delete();
        }

        foreach ($labels as $idx => $label) {
            $label = trim((string) $label);
            if ($label === '') {
                continue;
            }

            $value    = trim((string) ($values[$idx] ?? ''));
            $existId  = (int) ($existingIds[$idx] ?? 0);

            // Handle image upload for swatch_image type
            if ($inputType === 'swatch_image') {
                $uploadedName = $files['name'][$idx] ?? '';
                $tmpPath      = $files['tmp_name'][$idx] ?? '';
                $uploadErr    = $files['error'][$idx] ?? UPLOAD_ERR_NO_FILE;

                if ($uploadErr === UPLOAD_ERR_OK && $tmpPath !== '') {
                    $ext      = strtolower(pathinfo((string) $uploadedName, PATHINFO_EXTENSION));
                    $allowed  = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                    if (in_array($ext, $allowed, true)) {
                        $filename = 'swatch_' . $attributeId . '_' . uniqid() . '.' . $ext;
                        $dest     = __DIR__ . '/../../../public/uploads/attributes/' . $filename;
                        if (move_uploaded_file($tmpPath, $dest)) {
                            // Remove old image file if replacing
                            if ($existId > 0) {
                                $old = $db->table('attribute_swatch_options')->where('id', $existId)->first();
                                if ($old && !empty($old['value'])) {
                                    $oldFile = __DIR__ . '/../../../public/uploads/attributes/' . $old['value'];
                                    if (is_file($oldFile)) {
                                        @unlink($oldFile);
                                    }
                                }
                            }
                            $value = $filename;
                        }
                    }
                }
                // Keep existing value if no new upload
                if ($value === '' && $existId > 0) {
                    $existing = $db->table('attribute_swatch_options')->where('id', $existId)->first();
                    $value    = $existing['value'] ?? '';
                }
            }

            if ($value === '') {
                continue;
            }

            if ($existId > 0) {
                $db->table('attribute_swatch_options')->where('id', $existId)->update([
                    'label'      => $label,
                    'value'      => $value,
                    'sort_order' => (int) $idx,
                ]);
            } else {
                $db->table('attribute_swatch_options')->insert([
                    'attribute_id' => $attributeId,
                    'label'        => $label,
                    'value'        => $value,
                    'sort_order'   => (int) $idx,
                ]);
            }
        }
    }
}
