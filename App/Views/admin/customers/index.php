<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">Manage customer accounts</p>
</div>

<!-- Search -->
<div class="bg-white rounded-xl border border-gray-200 mb-6">
    <form method="GET" action="/admin/customers" class="p-4 flex gap-4 items-end">
        <div class="flex-1">
            <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? ($search ?? '')) ?>"
                   placeholder="Search by name, email..."
                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-lg">Search</button>
    </form>
</div>

<!-- Customers Table -->
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Customer</th>
                <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Group</th>
                <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Orders</th>
                <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Registered</th>
                <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php if (!empty($customers['data'])): ?>
                <?php foreach ($customers['data'] as $customer): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-xs font-medium text-gray-600">
                                <?= strtoupper(substr($customer['first_name'] ?? '', 0, 1) . substr($customer['last_name'] ?? '', 0, 1)) ?>
                            </div>
                            <span class="font-medium text-gray-900"><?= htmlspecialchars(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? '')) ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-3 text-gray-500"><?= htmlspecialchars($customer['email']) ?></td>
                    <td class="px-6 py-3">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-medium">
                            <?= htmlspecialchars((string) ($customer['customer_group_label'] ?? 'Normal Client')) ?>
                        </span>
                    </td>
                    <td class="px-6 py-3 text-center text-gray-900"><?= $customer['order_count'] ?? 0 ?></td>
                    <td class="px-6 py-3 text-center">
                        <span class="inline-block w-2 h-2 rounded-full <?= ($customer['is_active'] ?? 1) ? 'bg-green-500' : 'bg-gray-300' ?>"></span>
                    </td>
                    <td class="px-6 py-3 text-right text-gray-400 text-xs"><?= $customer['created_at'] ?? '' ?></td>
                    <td class="px-6 py-3 text-right">
                        <a href="/admin/customers/<?= (int) $customer['id'] ?>" class="text-blue-600 hover:text-blue-700 text-xs font-medium">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" class="px-6 py-12 text-center text-gray-400">No customers found</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
