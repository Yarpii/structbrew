<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">Manage customer orders</p>
    <div class="flex gap-3">
        <a href="/admin/orders?export=csv" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export CSV
        </a>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl border border-gray-200 mb-6">
    <form method="GET" action="/admin/orders" class="p-4 flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
            <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                   placeholder="Order number, email..."
                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="w-40">
            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                <option value="">All</option>
                <?php foreach (['pending','processing','shipped','delivered','cancelled','refunded'] as $s): ?>
                <option value="<?= $s ?>" <?= ($_GET['status'] ?? '') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="w-40">
            <label class="block text-xs font-medium text-gray-500 mb-1">Store View</label>
            <select name="store_view" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                <option value="">All</option>
                <?php foreach ($storeViews ?? [] as $sv): ?>
                <option value="<?= $sv['id'] ?>" <?= ($_GET['store_view'] ?? '') == $sv['id'] ? 'selected' : '' ?>><?= htmlspecialchars($sv['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-lg transition-colors">Filter</button>
    </form>
</div>

<!-- Orders Table -->
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Order</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Store</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Payment</th>
                    <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Items</th>
                    <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (!empty($orders['data'])): ?>
                    <?php foreach ($orders['data'] as $order): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <a href="/admin/orders/<?= $order['id'] ?>" class="font-medium text-blue-600 hover:text-blue-700">
                                #<?= htmlspecialchars($order['order_number']) ?>
                            </a>
                        </td>
                        <td class="px-6 py-3">
                            <p class="text-gray-900"><?= htmlspecialchars($order['customer_email']) ?></p>
                        </td>
                        <td class="px-6 py-3 text-gray-500"><?= htmlspecialchars($order['store_name'] ?? '—') ?></td>
                        <td class="px-6 py-3 text-gray-500"><?= htmlspecialchars($order['payment_method_label'] ?? $order['payment_method'] ?? '—') ?></td>
                        <td class="px-6 py-3 text-center">
                            <span class="inline-block px-2.5 py-1 text-xs font-medium rounded-full
                                <?= match($order['status']) {
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'processing' => 'bg-blue-100 text-blue-700',
                                    'shipped' => 'bg-purple-100 text-purple-700',
                                    'delivered' => 'bg-green-100 text-green-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    'refunded' => 'bg-gray-100 text-gray-700',
                                    default => 'bg-gray-100 text-gray-700',
                                } ?>">
                                <?= ucfirst($order['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-3 text-center text-gray-500"><?= $order['item_count'] ?? 0 ?></td>
                        <td class="px-6 py-3 text-right font-medium"><?= htmlspecialchars($order['currency_code'] ?? '') ?> <?= number_format((float)($order['grand_total'] ?? 0), 2) ?></td>
                        <td class="px-6 py-3 text-right text-gray-500 text-xs"><?= $order['created_at'] ?? '' ?></td>
                        <td class="px-6 py-3 text-right">
                            <a href="/admin/orders/<?= $order['id'] ?>" class="p-1.5 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50 inline-block">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="9" class="px-6 py-12 text-center text-gray-400">No orders found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (($orders['last_page'] ?? 1) > 1): ?>
    <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
        <p class="text-sm text-gray-500">Showing <?= $orders['from'] ?> to <?= $orders['to'] ?> of <?= $orders['total'] ?></p>
        <div class="flex gap-1">
            <?php for ($i = 1; $i <= $orders['last_page']; $i++): ?>
            <a href="?page=<?= $i ?>&<?= htmlspecialchars(http_build_query(array_diff_key($_GET, ['page' => '']))) ?>"
               class="px-3 py-1.5 text-sm rounded-lg <?= $i === $orders['current_page'] ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
