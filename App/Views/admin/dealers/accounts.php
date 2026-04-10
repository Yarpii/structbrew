<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-4">
        <a href="/admin/dealers" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Applications
        </a>
        <span class="text-gray-300">|</span>
        <span class="text-sm font-semibold text-gray-800">Dealer Accounts</span>
    </div>
</div>

<!-- Search -->
<div class="bg-white rounded-xl border border-gray-200 mb-6">
    <form method="GET" action="/admin/dealers/accounts" class="p-4 flex gap-4 items-end flex-wrap">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="q" value="<?= htmlspecialchars($search ?? '') ?>"
                   placeholder="Search by company, contact, account number..."
                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <select name="status" class="px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500">
            <option value="">All statuses</option>
            <option value="active" <?= ($statusFilter ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
            <option value="paused" <?= ($statusFilter ?? '') === 'paused' ? 'selected' : '' ?>>Paused</option>
            <option value="suspended" <?= ($statusFilter ?? '') === 'suspended' ? 'selected' : '' ?>>Suspended</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-lg">Search</button>
    </form>
</div>

<!-- Flash -->
<?php if (!empty($flashSuccess)): ?>
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm"><?= htmlspecialchars((string) $flashSuccess) ?></div>
<?php endif; ?>

<!-- Table -->
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Company</th>
                <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Account #</th>
                <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Discount</th>
                <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Credit Limit</th>
                <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Terms</th>
                <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php
            $statusBadge = [
                'active'    => 'bg-green-50 text-green-700 border border-green-200',
                'paused'    => 'bg-amber-50 text-amber-700 border border-amber-200',
                'suspended' => 'bg-red-50 text-red-700 border border-red-200',
            ];
            if (!empty($accounts['data'])):
                foreach ($accounts['data'] as $acc):
                    $badge = $statusBadge[$acc['status'] ?? 'active'] ?? $statusBadge['active'];
            ?>
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-xs font-semibold text-blue-700">
                            <?= strtoupper(substr($acc['company_name'] ?? '', 0, 2)) ?>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900"><?= htmlspecialchars($acc['company_name'] ?? '') ?></p>
                            <p class="text-xs text-gray-400"><?= htmlspecialchars($acc['email'] ?? '') ?></p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-3">
                    <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded"><?= htmlspecialchars($acc['account_number'] ?? '') ?></span>
                </td>
                <td class="px-6 py-3 text-center text-gray-900"><?= number_format((float) ($acc['discount_rate'] ?? 0), 2) ?>%</td>
                <td class="px-6 py-3 text-center text-gray-900">€<?= number_format((float) ($acc['credit_limit'] ?? 0), 2) ?></td>
                <td class="px-6 py-3 text-center text-gray-500 uppercase text-xs"><?= htmlspecialchars($acc['payment_terms'] ?? '') ?></td>
                <td class="px-6 py-3 text-center">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium capitalize <?= $badge ?>">
                        <?= htmlspecialchars($acc['status'] ?? 'active') ?>
                    </span>
                </td>
                <td class="px-6 py-3 text-right">
                    <a href="/admin/dealers/accounts/<?= (int) $acc['id'] ?>" class="text-blue-600 hover:text-blue-700 text-xs font-medium">Manage</a>
                </td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="7" class="px-6 py-12 text-center text-gray-400">No dealer accounts found</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?php if (!empty($accounts['last_page']) && $accounts['last_page'] > 1): ?>
<div class="mt-4 flex items-center justify-between text-sm text-gray-500">
    <span>Page <?= $accounts['current_page'] ?> of <?= $accounts['last_page'] ?></span>
    <div class="flex gap-2">
        <?php if ($accounts['current_page'] > 1): ?>
            <a href="?page=<?= $accounts['current_page'] - 1 ?><?= !empty($search) ? '&q=' . urlencode($search) : '' ?>" class="px-3 py-1.5 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">Previous</a>
        <?php endif; ?>
        <?php if ($accounts['current_page'] < $accounts['last_page']): ?>
            <a href="?page=<?= $accounts['current_page'] + 1 ?><?= !empty($search) ? '&q=' . urlencode($search) : '' ?>" class="px-3 py-1.5 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">Next</a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
