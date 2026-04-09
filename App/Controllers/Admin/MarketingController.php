<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;

final class MarketingController extends BaseAdminController
{
    // ─── Price Rules ─────────────────────────────────────────

    /**
     * List all price rules.
     */
    public function priceRules(): Response
    {
        $db = Database::getInstance();

        $page    = $this->page();
        $perPage = 20;

        $priceRules = $db->table('price_rules')
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage, $page);

        // Add coupon count per rule
        foreach ($priceRules['data'] as &$rule) {
            $rule['coupon_count'] = $db->table('coupons')
                ->where('price_rule_id', $rule['id'])
                ->count();
            $rule['store_view_ids_list'] = json_decode($rule['store_view_ids'] ?? '[]', true) ?: [];
        }
        unset($rule);

        return $this->adminView('admin/marketing/price-rules', [
            'title'      => 'Price Rules',
            'priceRules' => $priceRules,
        ]);
    }

    /**
     * Show price rule creation form.
     */
    public function createPriceRule(): Response
    {
        return $this->adminView('admin/marketing/create-price-rule', [
            'title' => 'Create Price Rule',
        ]);
    }

    /**
     * Store a new price rule.
     */
    public function storePriceRule(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/marketing/price-rules/create');
        }

        $data = [
            'name'            => (string) $this->input('name', ''),
            'description'     => $this->input('description'),
            'type'            => (string) $this->input('type', ''),
            'value'           => (string) $this->input('value', ''),
            'min_order_total' => $this->input('min_order_total'),
            'starts_at'       => $this->input('starts_at'),
            'expires_at'      => $this->input('expires_at'),
            'is_active'       => $this->input('is_active', '0'),
            'usage_limit'     => $this->input('usage_limit'),
        ];

        $validator = Validator::make($data, [
            'name'  => 'required|max:255',
            'type'  => 'required|in:percentage,fixed',
            'value' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(fn($e) => $e[0], $validator->errors())));
            return $this->redirect('/admin/marketing/price-rules/create');
        }

        // Validate percentage range
        if ($data['type'] === 'percentage' && ((float) $data['value'] < 0 || (float) $data['value'] > 100)) {
            Session::flash('error', 'Percentage discount must be between 0 and 100.');
            return $this->redirect('/admin/marketing/price-rules/create');
        }

        $storeViewIds = $this->input('store_view_ids', []);
        if (!is_array($storeViewIds)) {
            $storeViewIds = [];
        }

        $db = Database::getInstance();
        $now = date('Y-m-d H:i:s');

        $ruleId = $db->table('price_rules')->insert([
            'name'            => $data['name'],
            'description'     => $data['description'] ?: null,
            'type'            => $data['type'],
            'value'           => (float) $data['value'],
            'min_order_total' => $data['min_order_total'] ? (float) $data['min_order_total'] : null,
            'starts_at'       => $data['starts_at'] ?: null,
            'expires_at'      => $data['expires_at'] ?: null,
            'is_active'       => (int) $data['is_active'],
            'usage_limit'     => $data['usage_limit'] ? (int) $data['usage_limit'] : null,
            'times_used'      => 0,
            'store_view_ids'  => !empty($storeViewIds) ? json_encode(array_map('intval', $storeViewIds)) : null,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);

        $this->logActivity('create', 'price_rule', $ruleId, null, $data);
        Session::flash('success', 'Price rule created successfully.');
        return $this->redirect('/admin/marketing/price-rules');
    }

    /**
     * Show price rule edit form.
     */
    public function editPriceRule(int $id): Response
    {
        $db = Database::getInstance();

        $rule = $db->table('price_rules')->where('id', $id)->first();
        if (!$rule) {
            Session::flash('error', 'Price rule not found.');
            return $this->redirect('/admin/marketing/price-rules');
        }

        $rule['store_view_ids_list'] = json_decode($rule['store_view_ids'] ?? '[]', true) ?: [];

        // Load associated coupons
        $coupons = $db->table('coupons')
            ->where('price_rule_id', $id)
            ->orderBy('created_at', 'DESC')
            ->get();

        return $this->adminView('admin/marketing/edit-price-rule', [
            'title'   => 'Edit Price Rule',
            'rule'    => $rule,
            'coupons' => $coupons,
        ]);
    }

    /**
     * Update an existing price rule.
     */
    public function updatePriceRule(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/marketing/price-rules/' . $id . '/edit');
        }

        $db = Database::getInstance();

        $rule = $db->table('price_rules')->where('id', $id)->first();
        if (!$rule) {
            Session::flash('error', 'Price rule not found.');
            return $this->redirect('/admin/marketing/price-rules');
        }

        $data = [
            'name'            => (string) $this->input('name', ''),
            'description'     => $this->input('description'),
            'type'            => (string) $this->input('type', ''),
            'value'           => (string) $this->input('value', ''),
            'min_order_total' => $this->input('min_order_total'),
            'starts_at'       => $this->input('starts_at'),
            'expires_at'      => $this->input('expires_at'),
            'is_active'       => $this->input('is_active', '0'),
            'usage_limit'     => $this->input('usage_limit'),
        ];

        $validator = Validator::make($data, [
            'name'  => 'required|max:255',
            'type'  => 'required|in:percentage,fixed',
            'value' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(fn($e) => $e[0], $validator->errors())));
            return $this->redirect('/admin/marketing/price-rules/' . $id . '/edit');
        }

        if ($data['type'] === 'percentage' && ((float) $data['value'] < 0 || (float) $data['value'] > 100)) {
            Session::flash('error', 'Percentage discount must be between 0 and 100.');
            return $this->redirect('/admin/marketing/price-rules/' . $id . '/edit');
        }

        $storeViewIds = $this->input('store_view_ids', []);
        if (!is_array($storeViewIds)) {
            $storeViewIds = [];
        }

        $db->table('price_rules')->where('id', $id)->update([
            'name'            => $data['name'],
            'description'     => $data['description'] ?: null,
            'type'            => $data['type'],
            'value'           => (float) $data['value'],
            'min_order_total' => $data['min_order_total'] ? (float) $data['min_order_total'] : null,
            'starts_at'       => $data['starts_at'] ?: null,
            'expires_at'      => $data['expires_at'] ?: null,
            'is_active'       => (int) $data['is_active'],
            'usage_limit'     => $data['usage_limit'] ? (int) $data['usage_limit'] : null,
            'store_view_ids'  => !empty($storeViewIds) ? json_encode(array_map('intval', $storeViewIds)) : null,
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);

        $this->logActivity('update', 'price_rule', $id, $rule, $data);
        Session::flash('success', 'Price rule updated successfully.');
        return $this->redirect('/admin/marketing/price-rules/' . $id . '/edit');
    }

    // ─── Coupons ─────────────────────────────────────────────

    /**
     * List all coupons.
     */
    public function coupons(): Response
    {
        $db = Database::getInstance();

        $page    = $this->page();
        $perPage = 20;
        $search  = (string) $this->request->query('search');

        $query = $db->table('coupons')
            ->select('coupons.*', 'price_rules.name as rule_name', 'price_rules.type as rule_type', 'price_rules.value as rule_value')
            ->leftJoin('price_rules', 'coupons.price_rule_id', '=', 'price_rules.id')
            ->orderBy('coupons.created_at', 'DESC');

        if ($search !== '') {
            $query->whereRaw(
                "(coupons.code LIKE :search_0 OR price_rules.name LIKE :search_1)",
                [':search_0' => "%{$search}%", ':search_1' => "%{$search}%"]
            );
        }

        $coupons = $query->paginate($perPage, $page);

        return $this->adminView('admin/marketing/coupons', [
            'title'   => 'Coupons',
            'coupons' => $coupons,
            'search'  => $search,
        ]);
    }

    /**
     * Show coupon creation form.
     */
    public function createCoupon(): Response
    {
        $db = Database::getInstance();

        $priceRules = $db->table('price_rules')
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->get();

        return $this->adminView('admin/marketing/create-coupon', [
            'title'      => 'Create Coupon',
            'priceRules' => $priceRules,
        ]);
    }

    /**
     * Store a new coupon.
     */
    public function storeCoupon(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/marketing/coupons/create');
        }

        $data = [
            'price_rule_id'     => (string) $this->input('price_rule_id', ''),
            'code'              => (string) $this->input('code', ''),
            'usage_limit'       => $this->input('usage_limit'),
            'usage_per_customer' => $this->input('usage_per_customer', '1'),
            'is_active'         => $this->input('is_active', '0'),
        ];

        $validator = Validator::make($data, [
            'price_rule_id' => 'required|integer',
            'code'          => 'required|max:50|unique:coupons,code',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(fn($e) => $e[0], $validator->errors())));
            return $this->redirect('/admin/marketing/coupons/create');
        }

        $db = Database::getInstance();

        // Verify price rule exists
        $rule = $db->table('price_rules')->where('id', (int) $data['price_rule_id'])->first();
        if (!$rule) {
            Session::flash('error', 'Selected price rule does not exist.');
            return $this->redirect('/admin/marketing/coupons/create');
        }

        $now = date('Y-m-d H:i:s');

        $couponId = $db->table('coupons')->insert([
            'price_rule_id'      => (int) $data['price_rule_id'],
            'code'               => strtoupper(trim($data['code'])),
            'usage_limit'        => $data['usage_limit'] ? (int) $data['usage_limit'] : null,
            'usage_per_customer' => (int) $data['usage_per_customer'],
            'times_used'         => 0,
            'is_active'          => (int) $data['is_active'],
            'created_at'         => $now,
            'updated_at'         => $now,
        ]);

        $this->logActivity('create', 'coupon', $couponId, null, $data);
        Session::flash('success', 'Coupon created successfully.');
        return $this->redirect('/admin/marketing/coupons');
    }

    /**
     * Show coupon edit form.
     */
    public function editCoupon(int $id): Response
    {
        $db = Database::getInstance();

        $coupon = $db->table('coupons')->where('id', $id)->first();
        if (!$coupon) {
            Session::flash('error', 'Coupon not found.');
            return $this->redirect('/admin/marketing/coupons');
        }

        $priceRules = $db->table('price_rules')
            ->orderBy('name', 'ASC')
            ->get();

        return $this->adminView('admin/marketing/edit-coupon', [
            'title'      => 'Edit Coupon',
            'coupon'     => $coupon,
            'priceRules' => $priceRules,
        ]);
    }

    /**
     * Update an existing coupon.
     */
    public function updateCoupon(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/marketing/coupons/' . $id . '/edit');
        }

        $db = Database::getInstance();

        $coupon = $db->table('coupons')->where('id', $id)->first();
        if (!$coupon) {
            Session::flash('error', 'Coupon not found.');
            return $this->redirect('/admin/marketing/coupons');
        }

        $data = [
            'price_rule_id'      => (string) $this->input('price_rule_id', ''),
            'code'               => (string) $this->input('code', ''),
            'usage_limit'        => $this->input('usage_limit'),
            'usage_per_customer' => $this->input('usage_per_customer', '1'),
            'is_active'          => $this->input('is_active', '0'),
        ];

        $validator = Validator::make($data, [
            'price_rule_id' => 'required|integer',
            'code'          => 'required|max:50|unique:coupons,code,' . $id,
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(fn($e) => $e[0], $validator->errors())));
            return $this->redirect('/admin/marketing/coupons/' . $id . '/edit');
        }

        // Verify price rule exists
        $rule = $db->table('price_rules')->where('id', (int) $data['price_rule_id'])->first();
        if (!$rule) {
            Session::flash('error', 'Selected price rule does not exist.');
            return $this->redirect('/admin/marketing/coupons/' . $id . '/edit');
        }

        $db->table('coupons')->where('id', $id)->update([
            'price_rule_id'      => (int) $data['price_rule_id'],
            'code'               => strtoupper(trim($data['code'])),
            'usage_limit'        => $data['usage_limit'] ? (int) $data['usage_limit'] : null,
            'usage_per_customer' => (int) $data['usage_per_customer'],
            'is_active'          => (int) $data['is_active'],
            'updated_at'         => date('Y-m-d H:i:s'),
        ]);

        $this->logActivity('update', 'coupon', $id, $coupon, $data);
        Session::flash('success', 'Coupon updated successfully.');
        return $this->redirect('/admin/marketing/coupons/' . $id . '/edit');
    }
}
