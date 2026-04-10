<?php
$statusBadge = [
    'active'    => 'bg-green-50 text-green-700 border border-green-200',
    'paused'    => 'bg-amber-50 text-amber-700 border border-amber-200',
    'suspended' => 'bg-red-50 text-red-700 border border-red-200',
];
$badge = $statusBadge[$account['status'] ?? 'active'] ?? $statusBadge['active'];
?>

<!-- Breadcrumb -->
<div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
    <a href="/admin/dealers/accounts" class="hover:text-gray-700">Dealer Accounts</a>
    <span>/</span>
    <span class="text-gray-900"><?= htmlspecialchars($account['company_name'] ?? '') ?></span>
</div>

<!-- Flash -->
<?php if (!empty($flashSuccess)): ?>
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm"><?= htmlspecialchars((string) $flashSuccess) ?></div>
<?php endif; ?>
<?php if (!empty($flashError)): ?>
    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm"><?= htmlspecialchars((string) $flashError) ?></div>
<?php endif; ?>

<div class="grid gap-6 lg:grid-cols-3">
    <!-- Account Details -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-gray-900"><?= htmlspecialchars($account['company_name'] ?? '') ?></h2>
                    <p class="text-xs text-gray-400 font-mono mt-0.5"><?= htmlspecialchars($account['account_number'] ?? '') ?></p>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium capitalize <?= $badge ?>">
                    <?= htmlspecialchars($account['status'] ?? 'active') ?>
                </span>
            </div>
            <div class="p-6 grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase mb-1">Contact Name</p>
                    <p class="text-sm text-gray-900"><?= htmlspecialchars($account['contact_name'] ?? '—') ?></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase mb-1">Email</p>
                    <p class="text-sm text-gray-900"><?= htmlspecialchars($account['email'] ?? '—') ?></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase mb-1">Phone</p>
                    <p class="text-sm text-gray-900"><?= htmlspecialchars($account['phone'] ?? '—') ?></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase mb-1">Payment Terms</p>
                    <p class="text-sm text-gray-900 uppercase"><?= htmlspecialchars($account['payment_terms'] ?? '—') ?></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase mb-1">Discount Rate</p>
                    <p class="text-sm text-gray-900"><?= number_format((float) ($account['discount_rate'] ?? 0), 2) ?>%</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase mb-1">Credit Limit</p>
                    <p class="text-sm text-gray-900">€<?= number_format((float) ($account['credit_limit'] ?? 0), 2) ?></p>
                </div>
                <?php if (!empty($account['notes'])): ?>
                <div class="sm:col-span-2">
                    <p class="text-xs font-medium text-gray-400 uppercase mb-1">Internal Notes</p>
                    <p class="text-sm text-gray-700 whitespace-pre-wrap"><?= htmlspecialchars($account['notes']) ?></p>
                </div>
                <?php endif; ?>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase mb-1">Created</p>
                    <p class="text-sm text-gray-900"><?= htmlspecialchars($account['created_at'] ?? '') ?></p>
                </div>
            </div>
        </div>

        <?php if ($application): ?>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-900">Originating Application</h3>
                <a href="/admin/dealers/<?= (int) $application['id'] ?>" class="text-xs text-blue-600 hover:text-blue-700">View application →</a>
            </div>
            <div class="grid gap-3 sm:grid-cols-3 text-sm text-gray-600">
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Business Type</p>
                    <p class="capitalize"><?= htmlspecialchars($application['business_type'] ?? '—') ?></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Country</p>
                    <p><?= htmlspecialchars($application['country'] ?? '—') ?></p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-0.5">Est. Annual Volume</p>
                    <p><?= htmlspecialchars($application['annual_volume'] ?? '—') ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Edit Sidebar -->
    <div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Edit Account</h3>
            <form method="POST" action="/admin/dealers/accounts/<?= (int) $account['id'] ?>">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Discount Rate (%)</label>
                    <input type="number" name="discount_rate" value="<?= number_format((float) ($account['discount_rate'] ?? 0), 2) ?>" min="0" max="100" step="0.5"
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Credit Limit (€)</label>
                    <input type="number" name="credit_limit" value="<?= number_format((float) ($account['credit_limit'] ?? 0), 2) ?>" min="0" step="100"
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Payment Terms</label>
                    <select name="payment_terms" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500">
                        <?php foreach (['prepaid' => 'Prepaid', 'net15' => 'Net 15', 'net30' => 'Net 30', 'net60' => 'Net 60'] as $val => $label): ?>
                        <option value="<?= $val ?>" <?= ($account['payment_terms'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Status</label>
                    <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="active" <?= ($account['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="paused" <?= ($account['status'] ?? '') === 'paused' ? 'selected' : '' ?>>Paused</option>
                        <option value="suspended" <?= ($account['status'] ?? '') === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Internal Notes</label>
                    <textarea name="notes" rows="3" placeholder="Internal team notes..."
                              class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($account['notes'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="w-full inline-flex justify-center items-center h-9 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                    Save Changes
                </button>
            </form>
        </div>
    </div>
</div>
