<?php
$statusBadge = [
    'pending'  => 'bg-amber-50 text-amber-700 border border-amber-200',
    'approved' => 'bg-green-50 text-green-700 border border-green-200',
    'rejected' => 'bg-red-50 text-red-700 border border-red-200',
];
$badge = $statusBadge[$application['status'] ?? 'pending'] ?? $statusBadge['pending'];

$businessTypeLabels = [
    'retailer'    => 'Retailer',
    'webshop'     => 'Webshop',
    'workshop'    => 'Workshop',
    'distributor' => 'Distributor',
    'other'       => 'Other',
];
?>

<!-- Breadcrumb -->
<div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
    <a href="/admin/dealers" class="hover:text-gray-700">Dealer Applications</a>
    <span>/</span>
    <span class="text-gray-900"><?= htmlspecialchars($application['company_name'] ?? '') ?></span>
</div>

<!-- Flash -->
<?php if (!empty($flashSuccess)): ?>
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm"><?= htmlspecialchars((string) $flashSuccess) ?></div>
<?php endif; ?>
<?php if (!empty($flashError)): ?>
    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm"><?= htmlspecialchars((string) $flashError) ?></div>
<?php endif; ?>

<div class="grid gap-6 lg:grid-cols-3">
    <!-- Application Details -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-900">Application Details</h2>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium capitalize <?= $badge ?>">
                    <?= htmlspecialchars($application['status'] ?? 'pending') ?>
                </span>
            </div>
            <div class="p-6 grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase mb-1">Company</p>
                    <p class="text-sm text-gray-900"><?= htmlspecialchars($application['company_name'] ?? '') ?></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase mb-1">Contact Name</p>
                    <p class="text-sm text-gray-900"><?= htmlspecialchars($application['contact_name'] ?? '') ?></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase mb-1">Email</p>
                    <p class="text-sm text-gray-900"><?= htmlspecialchars($application['email'] ?? '') ?></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase mb-1">Phone</p>
                    <p class="text-sm text-gray-900"><?= htmlspecialchars($application['phone'] ?? '—') ?></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase mb-1">Country</p>
                    <p class="text-sm text-gray-900"><?= htmlspecialchars($application['country'] ?? '—') ?></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase mb-1">Business Type</p>
                    <p class="text-sm text-gray-900"><?= htmlspecialchars($businessTypeLabels[$application['business_type'] ?? 'other'] ?? 'Other') ?></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase mb-1">VAT / Tax Number</p>
                    <p class="text-sm text-gray-900"><?= htmlspecialchars($application['vat_number'] ?? '—') ?></p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase mb-1">Est. Annual Volume</p>
                    <p class="text-sm text-gray-900"><?= htmlspecialchars($application['annual_volume'] ?? '—') ?></p>
                </div>
                <?php if (!empty($application['website'])): ?>
                <div class="sm:col-span-2">
                    <p class="text-xs font-medium text-gray-400 uppercase mb-1">Website</p>
                    <a href="<?= htmlspecialchars($application['website']) ?>" target="_blank" rel="noopener noreferrer" class="text-sm text-blue-600 hover:underline break-all">
                        <?= htmlspecialchars($application['website']) ?>
                    </a>
                </div>
                <?php endif; ?>
                <?php if (!empty($application['message'])): ?>
                <div class="sm:col-span-2">
                    <p class="text-xs font-medium text-gray-400 uppercase mb-1">Message</p>
                    <p class="text-sm text-gray-700 whitespace-pre-wrap"><?= htmlspecialchars($application['message']) ?></p>
                </div>
                <?php endif; ?>
                <?php if (!empty($application['admin_notes'])): ?>
                <div class="sm:col-span-2">
                    <p class="text-xs font-medium text-gray-400 uppercase mb-1">Admin Notes</p>
                    <p class="text-sm text-gray-700"><?= htmlspecialchars($application['admin_notes']) ?></p>
                </div>
                <?php endif; ?>
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase mb-1">Submitted</p>
                    <p class="text-sm text-gray-900"><?= htmlspecialchars($application['created_at'] ?? '') ?></p>
                </div>
            </div>
        </div>

        <?php if ($dealerAccount): ?>
        <div class="bg-green-50 rounded-xl border border-green-200 p-5">
            <p class="text-sm font-semibold text-green-800">Dealer account created</p>
            <p class="text-xs text-green-700 mt-1">Account number: <strong class="font-mono"><?= htmlspecialchars($dealerAccount['account_number'] ?? '') ?></strong> — Discount: <?= number_format((float) ($dealerAccount['discount_rate'] ?? 0), 2) ?>% — Terms: <?= htmlspecialchars($dealerAccount['payment_terms'] ?? '') ?></p>
            <a href="/admin/dealers/accounts/<?= (int) $dealerAccount['id'] ?>" class="mt-3 inline-flex items-center gap-1.5 text-sm font-medium text-green-700 hover:text-green-900">
                View Dealer Account
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Actions Sidebar -->
    <div class="space-y-4">
        <?php if (($application['status'] ?? '') === 'pending'): ?>

        <!-- Approve -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Approve Application</h3>
            <form method="POST" action="/admin/dealers/<?= (int) $application['id'] ?>/approve">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Discount Rate (%)</label>
                    <input type="number" name="discount_rate" value="0" min="0" max="100" step="0.5"
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Credit Limit (€)</label>
                    <input type="number" name="credit_limit" value="0" min="0" step="100"
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Payment Terms</label>
                    <select name="payment_terms" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="prepaid">Prepaid</option>
                        <option value="net15">Net 15</option>
                        <option value="net30">Net 30</option>
                        <option value="net60">Net 60</option>
                    </select>
                </div>
                <button type="submit" class="w-full inline-flex justify-center items-center gap-2 h-9 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Approve & Create Account
                </button>
            </form>
        </div>

        <!-- Reject -->
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-3">Reject Application</h3>
            <form method="POST" action="/admin/dealers/<?= (int) $application['id'] ?>/reject">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Reason / Notes</label>
                    <textarea name="admin_notes" rows="3" placeholder="Optional reason shown to team..."
                              class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm resize-none focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
                </div>
                <button type="submit" class="w-full inline-flex justify-center items-center gap-2 h-9 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition">
                    Reject Application
                </button>
            </form>
        </div>

        <?php else: ?>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-sm text-gray-500">This application has been <strong><?= htmlspecialchars($application['status'] ?? '') ?></strong>.</p>
        </div>
        <?php endif; ?>
    </div>
</div>
