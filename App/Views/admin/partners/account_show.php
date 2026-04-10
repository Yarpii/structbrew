<?php
$statusBadge = [
    'active'    => 'bg-green-50 text-green-700 border border-green-200',
    'paused'    => 'bg-amber-50 text-amber-700 border border-amber-200',
    'suspended' => 'bg-red-50 text-red-700 border border-red-200',
];
$badge = $statusBadge[$account['status'] ?? 'active'] ?? $statusBadge['active'];
$refStatusBadge = [
    'pending'  => 'bg-amber-50 text-amber-700 border border-amber-200',
    'approved' => 'bg-blue-50 text-blue-700 border border-blue-200',
    'paid'     => 'bg-green-50 text-green-700 border border-green-200',
    'rejected' => 'bg-red-50 text-red-700 border border-red-200',
];
?>

<!-- Breadcrumb -->
<div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
    <a href="/admin/partners/accounts" class="hover:text-gray-700">Partner Accounts</a>
    <span>/</span>
    <span class="text-gray-900"><?= htmlspecialchars(($account['first_name'] ?? '') . ' ' . ($account['last_name'] ?? '')) ?></span>
</div>

<!-- Flash -->
<?php if (!empty($flashSuccess)): ?>
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm"><?= htmlspecialchars((string) $flashSuccess) ?></div>
<?php endif; ?>
<?php if (!empty($flashError)): ?>
    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm"><?= htmlspecialchars((string) $flashError) ?></div>
<?php endif; ?>

<div class="grid gap-6 lg:grid-cols-3">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <p class="text-xs text-gray-500">Clicks</p>
                <p class="text-2xl font-bold text-gray-900 mt-1"><?= number_format((int) $account['total_clicks']) ?></p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <p class="text-xs text-gray-500">Conversions</p>
                <p class="text-2xl font-bold text-gray-900 mt-1"><?= number_format((int) $account['total_conversions']) ?></p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <p class="text-xs text-gray-500">Total Earned</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">$<?= number_format((float) $account['total_commission_earned'], 2) ?></p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <p class="text-xs text-gray-500">Balance (unpaid)</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">$<?= number_format((float) $account['balance'], 2) ?></p>
            </div>
        </div>

        <!-- Referral Link -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs font-medium text-gray-400 uppercase mb-2">Referral Link</p>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="font-mono text-sm bg-gray-100 px-3 py-1.5 rounded border border-gray-200 break-all">
                    <?= htmlspecialchars(rtrim($baseUrl, '/') . '/r/' . ($account['referral_code'] ?? '')) ?>
                </span>
                <span class="font-mono text-xs bg-blue-50 text-blue-700 border border-blue-200 px-2.5 py-1 rounded"><?= htmlspecialchars($account['referral_code'] ?? '') ?></span>
            </div>
        </div>

        <!-- Referrals Table -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900">Referral Conversions (<?= count($referrals) ?>)</h2>
            </div>
            <?php if (!empty($referrals)): ?>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 uppercase">Order</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-gray-500 uppercase">Order Total</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-gray-500 uppercase">Commission</th>
                        <th class="text-center px-5 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="text-right px-5 py-3 text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($referrals as $ref):
                        $rb = $refStatusBadge[$ref['status'] ?? 'pending'] ?? $refStatusBadge['pending'];
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 text-xs text-gray-400"><?= htmlspecialchars(substr((string) $ref['created_at'], 0, 10)) ?></td>
                        <td class="px-5 py-3 text-gray-700">
                            <?= $ref['order_id'] ? '<span class="font-mono text-xs">#' . (int) $ref['order_id'] . '</span>' : '—' ?>
                            <?php if (!empty($ref['note'])): ?><span class="ml-1 text-xs text-gray-400"><?= htmlspecialchars($ref['note']) ?></span><?php endif; ?>
                        </td>
                        <td class="px-5 py-3 text-right text-gray-700">$<?= number_format((float) $ref['order_total'], 2) ?></td>
                        <td class="px-5 py-3 text-right font-semibold text-gray-900">$<?= number_format((float) $ref['commission_amount'], 2) ?></td>
                        <td class="px-5 py-3 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium capitalize <?= $rb ?>">
                                <?= htmlspecialchars($ref['status'] ?? '') ?>
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <form method="POST" action="/admin/partners/accounts/<?= (int) $account['id'] ?>/referrals/<?= (int) $ref['id'] ?>/status" class="inline-flex items-center gap-1">
                                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                                <select name="status" class="text-xs border border-gray-200 rounded px-2 py-1 focus:ring-1 focus:ring-blue-500">
                                    <option value="pending"  <?= ($ref['status'] ?? '') === 'pending'  ? 'selected' : '' ?>>Pending</option>
                                    <option value="approved" <?= ($ref['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                                    <option value="paid"     <?= ($ref['status'] ?? '') === 'paid'     ? 'selected' : '' ?>>Paid</option>
                                    <option value="rejected" <?= ($ref['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                </select>
                                <button type="submit" class="text-xs text-blue-600 hover:text-blue-700 font-medium px-1">Save</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="px-6 py-10 text-center text-gray-400 text-sm">No referral conversions recorded yet.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-4">
        <!-- Partner Info -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Partner Info</h3>
            <div class="space-y-3 text-sm">
                <div><p class="text-xs text-gray-400">Name</p><p class="text-gray-900"><?= htmlspecialchars(($account['first_name'] ?? '') . ' ' . ($account['last_name'] ?? '')) ?></p></div>
                <div><p class="text-xs text-gray-400">Email</p><p class="text-gray-900"><?= htmlspecialchars($account['email'] ?? '') ?></p></div>
                <div><p class="text-xs text-gray-400">Company</p><p class="text-gray-900"><?= htmlspecialchars($account['company'] ?? '—') ?></p></div>
                <div><p class="text-xs text-gray-400">Status</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize <?= $badge ?>"><?= htmlspecialchars($account['status'] ?? '') ?></span>
                </div>
                <div><p class="text-xs text-gray-400">Member since</p><p class="text-gray-900"><?= htmlspecialchars(substr((string) ($account['created_at'] ?? ''), 0, 10)) ?></p></div>
            </div>
        </div>

        <!-- Edit Settings -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Settings</h3>
            <form method="POST" action="/admin/partners/accounts/<?= (int) $account['id'] ?>" class="space-y-3">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Commission Rate (%)</label>
                    <input type="number" name="commission_rate" value="<?= number_format((float) ($account['commission_rate'] ?? 10), 2) ?>" min="0" max="100" step="0.5"
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Account Status</label>
                    <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="active"    <?= ($account['status'] ?? '') === 'active'    ? 'selected' : '' ?>>Active</option>
                        <option value="paused"    <?= ($account['status'] ?? '') === 'paused'    ? 'selected' : '' ?>>Paused</option>
                        <option value="suspended" <?= ($account['status'] ?? '') === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                    </select>
                </div>
                <button type="submit" class="w-full h-9 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">Save Changes</button>
            </form>
        </div>

        <!-- Add Manual Referral -->
        <div class="bg-white rounded-xl border border-gray-200 p-5" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center justify-between w-full">
                <h3 class="text-sm font-semibold text-gray-900">Add Referral Conversion</h3>
                <svg class="w-4 h-4 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <form x-show="open" x-cloak method="POST" action="/admin/partners/accounts/<?= (int) $account['id'] ?>/referrals" class="mt-4 space-y-3">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Order ID (optional)</label>
                    <input type="number" name="order_id" min="0" placeholder="Leave empty if not order-based"
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Order Total ($)</label>
                    <input type="number" name="order_total" min="0" step="0.01" value="0.00" required
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Commission Amount ($)</label>
                    <input type="number" name="commission_amount" min="0" step="0.01" value="0.00" required
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Note (optional)</label>
                    <input type="text" name="note" maxlength="255" placeholder="e.g. Manual adjustment"
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button type="submit" class="w-full h-9 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition">Add Conversion</button>
            </form>
        </div>
    </div>
</div>
