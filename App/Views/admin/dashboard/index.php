<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl p-6 border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total Revenue</p>
                <p class="text-2xl font-bold text-gray-900 mt-1"><?= htmlspecialchars((string) ($stats['revenue'] ?? '0.00')) ?></p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-xs text-green-600 mt-3 flex items-center gap-1">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
            Last 30 days
        </p>
    </div>

    <div class="bg-white rounded-xl p-6 border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Orders</p>
                <p class="text-2xl font-bold text-gray-900 mt-1"><?= (int) ($stats['orders'] ?? 0) ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-3">Total orders</p>
    </div>

    <div class="bg-white rounded-xl p-6 border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Products</p>
                <p class="text-2xl font-bold text-gray-900 mt-1"><?= (int) ($stats['products'] ?? 0) ?></p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-3">Active products</p>
    </div>

    <div class="bg-white rounded-xl p-6 border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Customers</p>
                <p class="text-2xl font-bold text-gray-900 mt-1"><?= (int) ($stats['customers'] ?? 0) ?></p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-3">Registered customers</p>
    </div>
</div>

<!-- Recent Orders & Low Stock -->
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    <!-- Recent Orders -->
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="font-semibold text-gray-800">Recent Orders</h2>
            <a href="/admin/orders" class="text-sm text-blue-600 hover:text-blue-700">View all</a>
        </div>
        <div class="divide-y divide-gray-100">
            <?php if (!empty($recentOrders)): ?>
                <?php foreach ($recentOrders as $order): ?>
                <div class="px-6 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">#<?= htmlspecialchars($order['order_number']) ?></p>
                        <p class="text-xs text-gray-500"><?= htmlspecialchars($order['customer_email']) ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($order['grand_total']) ?></p>
                        <span class="inline-block px-2 py-0.5 text-xs rounded-full
                            <?php
                            echo match($order['status']) {
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'processing' => 'bg-blue-100 text-blue-700',
                                'shipped' => 'bg-purple-100 text-purple-700',
                                'delivered' => 'bg-green-100 text-green-700',
                                'cancelled' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-700',
                            };
                            ?>">
                            <?= htmlspecialchars(ucfirst((string) ($order['status'] ?? 'unknown'))) ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="px-6 py-8 text-center text-gray-400 text-sm">No orders yet</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Low Stock Alerts -->
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="font-semibold text-gray-800">Low Stock Alerts</h2>
            <a href="/admin/products?filter=low_stock" class="text-sm text-blue-600 hover:text-blue-700">View all</a>
        </div>
        <div class="divide-y divide-gray-100">
            <?php if (!empty($lowStock)): ?>
                <?php foreach ($lowStock as $product): ?>
                <div class="px-6 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($product['sku']) ?></p>
                        <p class="text-xs text-gray-500"><?= htmlspecialchars($product['name'] ?? $product['sku']) ?></p>
                    </div>
                    <span class="inline-block px-2 py-0.5 text-xs rounded-full <?= $product['stock_qty'] <= 0 ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' ?>">
                        <?= (int) ($product['stock_qty'] ?? 0) ?> in stock
                    </span>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="px-6 py-8 text-center text-gray-400 text-sm">All products sufficiently stocked</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Store Performance -->
<div class="mt-6 bg-white rounded-xl border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="font-semibold text-gray-800">Store Performance</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Store View</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Locale</th>
                    <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                    <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (!empty($storeStats)): ?>
                    <?php foreach ($storeStats as $store): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 font-medium text-gray-900"><?= htmlspecialchars($store['name']) ?></td>
                        <td class="px-6 py-3 text-gray-500"><?= htmlspecialchars($store['locale']) ?></td>
                        <td class="px-6 py-3 text-right text-gray-900"><?= (int) ($store['order_count'] ?? 0) ?></td>
                        <td class="px-6 py-3 text-right font-medium text-gray-900"><?= htmlspecialchars((string) ($store['revenue'] ?? '0.00')) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">No store data available</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
