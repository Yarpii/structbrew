<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">Manage discount coupons</p>
    <a href="/admin/marketing/coupons/create"
       class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Coupon
    </a>
</div>

<!-- Search -->
<div class="bg-white rounded-xl border border-gray-200 mb-6">
    <form method="GET" action="/admin/marketing/coupons" class="p-4 flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
            <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                   placeholder="Coupon code..."
                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="w-48">
            <label class="block text-xs font-medium text-gray-500 mb-1">Price Rule</label>
            <select name="price_rule" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Rules</option>
                <?php foreach ($priceRules ?? [] as $pr): ?>
                <option value="<?= $pr['id'] ?>" <?= ($_GET['price_rule'] ?? '') == $pr['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($pr['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="w-32">
            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All</option>
                <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= ($_GET['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-lg transition-colors">Filter</button>
    </form>
</div>

<!-- Coupons Table -->
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Code</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Price Rule</th>
                    <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Usage</th>
                    <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Per Customer</th>
                    <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (!empty($coupons['data'])): ?>
                    <?php foreach ($coupons['data'] as $coupon): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <code class="px-2 py-1 bg-gray-100 text-gray-800 text-sm font-mono rounded font-medium"><?= htmlspecialchars($coupon['code']) ?></code>
                            </div>
                        </td>
                        <td class="px-6 py-3">
                            <?php if (!empty($coupon['rule_name'])): ?>
                                <a href="/admin/marketing/price-rules/<?= $coupon['price_rule_id'] ?>/edit" class="text-blue-600 hover:text-blue-700 text-sm">
                                    <?= htmlspecialchars($coupon['rule_name']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-gray-400">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <span class="text-gray-900 font-medium"><?= $coupon['times_used'] ?? 0 ?></span>
                            <?php if (!empty($coupon['usage_limit'])): ?>
                                <span class="text-gray-400">/ <?= $coupon['usage_limit'] ?></span>
                            <?php else: ?>
                                <span class="text-gray-400">/ &infin;</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-3 text-center text-gray-500">
                            <?= $coupon['usage_per_customer'] ?? '—' ?>
                        </td>
                        <td class="px-6 py-3 text-center">
                            <span class="inline-block w-2 h-2 rounded-full <?= ($coupon['is_active'] ?? 0) ? 'bg-green-500' : 'bg-gray-300' ?>"></span>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="/admin/marketing/coupons/<?= $coupon['id'] ?>/edit" class="p-1.5 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form method="POST" action="/admin/marketing/coupons/<?= $coupon['id'] ?>/delete" onsubmit="return confirm('Delete this coupon?')">
                                    <input type="hidden" name="_csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">No coupons found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if (($coupons['last_page'] ?? 1) > 1): ?>
    <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
        <p class="text-sm text-gray-500">
            Showing <?= $coupons['from'] ?? 0 ?> to <?= $coupons['to'] ?? 0 ?> of <?= $coupons['total'] ?? 0 ?> coupons
        </p>
        <div class="flex gap-1">
            <?php for ($i = 1; $i <= $coupons['last_page']; $i++): ?>
            <a href="?page=<?= $i ?>&<?= htmlspecialchars(http_build_query(array_diff_key($_GET, ['page' => '']))) ?>"
               class="px-3 py-1.5 text-sm rounded-lg <?= $i === ($coupons['current_page'] ?? 1) ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
