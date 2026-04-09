<?php
declare(strict_types=1);

namespace Brew\Controllers\Admin;

use Brew\Core\Auth;
use Brew\Core\Database;
use Brew\Core\Response;
use Brew\Core\Session;
use Brew\Core\Validator;

final class OrderController extends BaseAdminController
{
    /**
     * List orders with filters (status, store_view, search) and pagination.
     */
    public function index(): Response
    {
        $db = Database::getInstance();

        $page        = $this->page();
        $perPage     = 20;
        $search      = (string) $this->request->query('search');
        $status      = $this->request->query('status');
        $storeViewId = $this->request->query('store_view_id');

        $query = $db->table('orders')
            ->select('orders.*')
            ->orderBy('orders.created_at', 'DESC');

        if ($search !== '') {
            $query->whereRaw(
                "(orders.order_number LIKE :search_0 OR orders.customer_email LIKE :search_1)",
                [':search_0' => "%{$search}%", ':search_1' => "%{$search}%"]
            );
        }

        if ($status && $status !== '') {
            $query->where('orders.status', $status);
        }

        if ($storeViewId) {
            $query->where('orders.store_view_id', (int) $storeViewId);
        }

        $orders = $query->paginate($perPage, $page);

        // Enrich each order with store view name
        foreach ($orders['data'] as &$order) {
            $sv = $db->table('store_views')
                ->where('id', $order['store_view_id'])
                ->first();
            $order['store_view_name'] = $sv['name'] ?? 'N/A';
        }
        unset($order);

        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'];

        return $this->adminView('admin/orders/index', [
            'title'       => 'Orders',
            'orders'      => $orders,
            'statuses'    => $statuses,
            'search'      => $search,
            'status'      => $status,
            'storeViewId' => $storeViewId,
        ]);
    }

    /**
     * Show order detail with items, status history, and addresses.
     */
    public function show(int $id): Response
    {
        $db = Database::getInstance();

        $order = $db->table('orders')->where('id', $id)->first();
        if (!$order) {
            Session::flash('error', 'Order not found.');
            return $this->redirect('/admin/orders');
        }

        // Decode JSON address fields
        $order['billing_address_data'] = is_string($order['billing_address'])
            ? json_decode($order['billing_address'], true)
            : $order['billing_address'];
        $order['shipping_address_data'] = is_string($order['shipping_address'])
            ? json_decode($order['shipping_address'], true)
            : $order['shipping_address'];

        // Order items with product info
        $items = $db->table('order_items')
            ->where('order_id', $id)
            ->get();

        foreach ($items as &$item) {
            $item['product'] = $db->table('products')
                ->where('id', $item['product_id'])
                ->first();
            if ($item['product']) {
                $item['translation'] = $db->table('product_translations')
                    ->where('product_id', $item['product_id'])
                    ->first();
            }
        }
        unset($item);

        // Status history
        $statusHistory = $db->table('order_status_history')
            ->where('order_id', $id)
            ->orderBy('created_at', 'DESC')
            ->get();

        // Customer info
        $customer = null;
        if ($order['customer_id']) {
            $customer = $db->table('customers')
                ->where('id', $order['customer_id'])
                ->first();
        }

        // Store view info
        $storeView = $db->table('store_views')
            ->where('id', $order['store_view_id'])
            ->first();

        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'];

        return $this->adminView('admin/orders/show', [
            'title'         => 'Order ' . $order['order_number'],
            'order'         => $order,
            'items'         => $items,
            'statusHistory' => $statusHistory,
            'customer'      => $customer,
            'storeView'     => $storeView,
            'statuses'      => $statuses,
        ]);
    }

    /**
     * Update order status and add a comment to the status history.
     */
    public function updateStatus(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/orders/' . $id);
        }

        $db = Database::getInstance();

        $order = $db->table('orders')->where('id', $id)->first();
        if (!$order) {
            Session::flash('error', 'Order not found.');
            return $this->redirect('/admin/orders');
        }

        $data = [
            'status'  => (string) $this->input('status', ''),
            'comment' => (string) $this->input('comment', ''),
            'notify'  => $this->input('notify_customer', '0'),
        ];

        $validator = Validator::make($data, [
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
        ]);

        if ($validator->fails()) {
            Session::flash('error', 'Please select a valid status.');
            return $this->redirect('/admin/orders/' . $id);
        }

        $admin = Auth::admin();
        $createdBy = $admin ? ($admin['first_name'] . ' ' . $admin['last_name']) : 'System';

        $db->beginTransaction();

        try {
            // Update order status
            $db->table('orders')->where('id', $id)->update([
                'status'     => $data['status'],
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // Add status history entry
            $db->table('order_status_history')->insert([
                'order_id'              => $id,
                'status'                => $data['status'],
                'comment'               => $data['comment'],
                'is_customer_notified'  => (int) $data['notify'],
                'created_by'            => $createdBy,
                'created_at'            => date('Y-m-d H:i:s'),
            ]);

            $db->commit();

            $this->logActivity('update_status', 'order', $id, [
                'old_status' => $order['status'],
            ], [
                'new_status' => $data['status'],
                'comment'    => $data['comment'],
            ]);

            Session::flash('success', 'Order status updated to "' . $data['status'] . '".');

        } catch (\Throwable $e) {
            $db->rollback();
            Session::flash('error', 'Failed to update order status: ' . $e->getMessage());
        }

        return $this->redirect('/admin/orders/' . $id);
    }
}
