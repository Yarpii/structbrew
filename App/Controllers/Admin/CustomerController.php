<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;

final class CustomerController extends BaseAdminController
{
    /**
     * List customers with search and pagination.
     */
    public function index(): Response
    {
        $db = Database::getInstance();

        $page    = $this->page();
        $perPage = 20;
        $search  = (string) $this->request->query('search');

        $query = $db->table('customers')
            ->select('customers.*')
            ->orderBy('customers.created_at', 'DESC');

        if ($search !== '') {
            $query->whereRaw(
                "(customers.email LIKE :search_0 OR customers.first_name LIKE :search_1 OR customers.last_name LIKE :search_2)",
                [
                    ':search_0' => "%{$search}%",
                    ':search_1' => "%{$search}%",
                    ':search_2' => "%{$search}%",
                ]
            );
        }

        $customers = $query->paginate($perPage, $page);

        // Enrich with order count and total spent
        foreach ($customers['data'] as &$customer) {
            $customer['order_count'] = $db->table('orders')
                ->where('customer_id', $customer['id'])
                ->count();
            $customer['total_spent'] = $db->table('orders')
                ->where('customer_id', $customer['id'])
                ->whereIn('status', ['processing', 'shipped', 'delivered'])
                ->sum('grand_total');
        }
        unset($customer);

        return $this->adminView('admin/customers/index', [
            'title'     => 'Customers',
            'customers' => $customers,
            'search'    => $search,
        ]);
    }

    /**
     * Show customer detail with orders and addresses.
     */
    public function show(int $id): Response
    {
        $db = Database::getInstance();

        $customer = $db->table('customers')->where('id', $id)->first();
        if (!$customer) {
            Session::flash('error', 'Customer not found.');
            return $this->redirect('/admin/customers');
        }

        // Customer orders
        $orders = $db->table('orders')
            ->where('customer_id', $id)
            ->orderBy('created_at', 'DESC')
            ->get();

        // Customer addresses
        $addresses = $db->table('addresses')
            ->where('customer_id', $id)
            ->get();

        // Store view name
        $storeView = null;
        if ($customer['store_view_id']) {
            $storeView = $db->table('store_views')
                ->where('id', $customer['store_view_id'])
                ->first();
        }

        // Aggregates
        $orderCount = count($orders);
        $totalSpent = 0.0;
        foreach ($orders as $order) {
            if (in_array($order['status'], ['processing', 'shipped', 'delivered'])) {
                $totalSpent += (float) $order['grand_total'];
            }
        }

        return $this->adminView('admin/customers/show', [
            'title'      => 'Customer: ' . trim(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? '')),
            'customer'   => $customer,
            'orders'     => $orders,
            'addresses'  => $addresses,
            'storeView'  => $storeView,
            'orderCount' => $orderCount,
            'totalSpent' => $totalSpent,
        ]);
    }

    /**
     * Show customer edit form.
     */
    public function edit(int $id): Response
    {
        $db = Database::getInstance();

        $customer = $db->table('customers')->where('id', $id)->first();
        if (!$customer) {
            Session::flash('error', 'Customer not found.');
            return $this->redirect('/admin/customers');
        }

        return $this->adminView('admin/customers/edit', [
            'title'    => 'Edit Customer',
            'customer' => $customer,
        ]);
    }

    /**
     * Update customer details.
     */
    public function update(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/customers/' . $id . '/edit');
        }

        $db = Database::getInstance();

        $customer = $db->table('customers')->where('id', $id)->first();
        if (!$customer) {
            Session::flash('error', 'Customer not found.');
            return $this->redirect('/admin/customers');
        }

        $data = [
            'first_name'    => (string) $this->input('first_name', ''),
            'last_name'     => (string) $this->input('last_name', ''),
            'email'         => (string) $this->input('email', ''),
            'phone'         => $this->input('phone'),
            'date_of_birth' => $this->input('date_of_birth'),
            'gender'        => $this->input('gender'),
            'is_active'     => $this->input('is_active', '0'),
        ];

        $validator = Validator::make($data, [
            'first_name' => 'required|max:255',
            'last_name'  => 'required|max:255',
            'email'      => 'required|email|unique:customers,email,' . $id,
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(fn($e) => $e[0], $validator->errors())));
            return $this->redirect('/admin/customers/' . $id . '/edit');
        }

        $updateData = [
            'first_name'    => $data['first_name'],
            'last_name'     => $data['last_name'],
            'email'         => $data['email'],
            'phone'         => $data['phone'] ?: null,
            'date_of_birth' => $data['date_of_birth'] ?: null,
            'gender'        => $data['gender'] ?: null,
            'is_active'     => (int) $data['is_active'],
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        // If a new password was provided, hash it
        $newPassword = (string) $this->input('password', '');
        if ($newPassword !== '') {
            $updateData['password_hash'] = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        }

        $db->table('customers')->where('id', $id)->update($updateData);

        $this->logActivity('update', 'customer', $id, $customer, $data);
        Session::flash('success', 'Customer updated successfully.');
        return $this->redirect('/admin/customers/' . $id);
    }
}
