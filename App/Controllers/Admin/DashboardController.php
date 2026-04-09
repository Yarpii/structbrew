<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Response;

final class DashboardController extends BaseAdminController
{
    /**
     * Show the admin dashboard with key metrics and recent activity.
     */
    public function index(): Response
    {
        $db = Database::getInstance();

        // Key counts
        $productCount  = $db->table('products')->count();
        $orderCount    = $db->table('orders')->count();
        $customerCount = $db->table('customers')->count();

        // Total revenue
        $revenue = $db->table('orders')
            ->whereIn('status', ['processing', 'shipped', 'delivered'])
            ->sum('grand_total');

        // Recent orders (latest 5)
        $recentOrders = $db->table('orders')
            ->select(
                'orders.id',
                'orders.order_number',
                'orders.customer_email',
                'orders.grand_total',
                'orders.currency_code',
                'orders.status',
                'orders.created_at'
            )
            ->orderBy('orders.created_at', 'DESC')
            ->limit(5)
            ->get();

        // Low stock products (stock_qty <= low_stock_threshold, managing stock)
        $lowStockProducts = $db->table('products')
            ->select('products.*')
            ->where('products.manage_stock', 1)
            ->whereRaw('products.stock_qty <= products.low_stock_threshold')
            ->orderBy('products.stock_qty', 'ASC')
            ->limit(5)
            ->get();

        // Attach a translation name to each low-stock product
        foreach ($lowStockProducts as &$product) {
            $translation = $db->table('product_translations')
                ->where('product_id', $product['id'])
                ->first();
            $product['name'] = $translation['name'] ?? $product['sku'];
        }
        unset($product);

        // Store performance: orders and revenue per store view
        $storePerformance = $db->table('orders')
            ->select(
                'store_views.name as store_view_name',
                'store_views.id as store_view_id'
            )
            ->leftJoin('store_views', 'orders.store_view_id', '=', 'store_views.id')
            ->groupBy('orders.store_view_id', 'store_views.name', 'store_views.id')
            ->get();

        // Manually calculate aggregates per store view since the builder
        // does not support mixing select() with aggregate functions easily.
        foreach ($storePerformance as &$row) {
            $svId = (int) $row['store_view_id'];
            $row['order_count'] = $db->table('orders')
                ->where('store_view_id', $svId)
                ->count();
            $row['total_revenue'] = $db->table('orders')
                ->where('store_view_id', $svId)
                ->whereIn('status', ['processing', 'shipped', 'delivered'])
                ->sum('grand_total');
        }
        unset($row);

        // Orders today
        $todayStart = date('Y-m-d 00:00:00');
        $ordersToday = $db->table('orders')
            ->where('created_at', '>=', $todayStart)
            ->count();

        // Revenue this month
        $monthStart = date('Y-m-01 00:00:00');
        $monthlyRevenue = $db->table('orders')
            ->where('created_at', '>=', $monthStart)
            ->whereIn('status', ['processing', 'shipped', 'delivered'])
            ->sum('grand_total');

        return $this->adminView('admin/dashboard/index', [
            'title'            => 'Dashboard',
            'productCount'     => $productCount,
            'orderCount'       => $orderCount,
            'customerCount'    => $customerCount,
            'revenue'          => $revenue,
            'recentOrders'     => $recentOrders,
            'lowStockProducts' => $lowStockProducts,
            'storePerformance' => $storePerformance,
            'ordersToday'      => $ordersToday,
            'monthlyRevenue'   => $monthlyRevenue,
        ]);
    }
}
