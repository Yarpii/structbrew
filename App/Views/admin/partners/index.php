<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-4">
        <div class="flex rounded-lg border border-gray-200 overflow-hidden text-sm">
            <a href="/admin/partners" class="px-4 py-2 font-medium <?= !isset($_GET['status']) || $_GET['status'] === '' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' ?>">All</a>
            <a href="/admin/partners?status=pending" class="px-4 py-2 font-medium border-l border-gray-200 <?= ($_GET['status'] ?? '') === 'pending' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' ?>">
                Pending <?php if (!empty($counts['pending'])): ?><span class="ml-1.5 inline-flex items-center justify-center h-5 min-w-5 px-1 rounded-full bg-amber-500 text-white text-xs"><?= (int) $counts['pending'] ?></span><?php endif; ?>
            </a>
            <a href="/admin/partners?status=approved" class="px-4 py-2 font-medium border-l border-gray-200 <?= ($_GET['status'] ?? '') === 'approved' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' ?>">Approved</a>
            <a href="/admin/partners?status=rejected" class="px-4 py-2 font-medium border-l border-gray-200 <?= ($_GET['status'] ?? '') === 'rejected' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' ?>">Rejected</a>
        </div>
        <a href="/admin/partners/accounts" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
            Partner Accounts
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>
</div>

<!-- Search -->
<div class="bg-white rounded-xl border border-gray-200 mb-6">
    <form method="GET" action="/admin/partners" class="p-4 flex gap-4 items-end flex-wrap">
        <?php if (!empty($_GET['status'])): ?>
            <input type="hidden" name="status" value="<?= htmlspecialchars($_GET['status']) ?>">
        <?php endif; ?>
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="q" value="<?= htmlspecialchars($search ?? '') ?>"
                   placeholder="Search by name, email, company..."
                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-lg">Search</button>
    </form>
</div>

<!-- Flash -->
<?php if (!empty($flashSuccess)): ?>
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm"><?= htmlspecialchars((string) $flashSuccess) ?></div>
<?php endif; ?>
<?php if (!empty($flashError)): ?>
    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm"><?= htmlspecialchars((string) $flashError) ?></div>
<?php endif; ?>

<!-- Applications Table -->
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Applicant</th>
                <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Company</th>
                <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Country</th>
                <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php
            $statusBadge = [
                'pending'  => 'bg-amber-50 text-amber-700 border border-amber-200',
                'approved' => 'bg-green-50 text-green-700 border border-green-200',
                'rejected' => 'bg-red-50 text-red-700 border border-red-200',
            ];
            if (!empty($applications['data'])):
                foreach ($applications['data'] as $app):
                    $badge = $statusBadge[$app['status'] ?? 'pending'] ?? $statusBadge['pending'];
            ?>
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-xs font-medium text-gray-600">
                            <?= strtoupper(substr($app['first_name'] ?? '', 0, 1) . substr($app['last_name'] ?? '', 0, 1)) ?>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900"><?= htmlspecialchars(($app['first_name'] ?? '') . ' ' . ($app['last_name'] ?? '')) ?></p>
                            <p class="text-xs text-gray-400"><?= htmlspecialchars($app['email'] ?? '') ?></p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-3 text-gray-500"><?= htmlspecialchars($app['company'] ?? '—') ?></td>
                <td class="px-6 py-3 text-gray-500"><?= htmlspecialchars($app['country'] ?? '—') ?></td>
                <td class="px-6 py-3 text-center">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium capitalize <?= $badge ?>">
                        <?= htmlspecialchars($app['status'] ?? 'pending') ?>
                    </span>
                </td>
                <td class="px-6 py-3 text-right text-xs text-gray-400"><?= htmlspecialchars(substr((string) ($app['created_at'] ?? ''), 0, 10)) ?></td>
                <td class="px-6 py-3 text-right">
                    <a href="/admin/partners/<?= (int) $app['id'] ?>" class="text-blue-600 hover:text-blue-700 text-xs font-medium">Review</a>
                </td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">No applications found</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<?php if (!empty($applications['last_page']) && $applications['last_page'] > 1): ?>
<div class="mt-4 flex items-center justify-between text-sm text-gray-500">
    <span>Page <?= $applications['current_page'] ?> of <?= $applications['last_page'] ?></span>
    <div class="flex gap-2">
        <?php if ($applications['current_page'] > 1): ?>
            <a href="?page=<?= $applications['current_page'] - 1 ?><?= !empty($_GET['status']) ? '&status=' . htmlspecialchars($_GET['status']) : '' ?><?= !empty($search) ? '&q=' . urlencode($search) : '' ?>" class="px-3 py-1.5 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">Previous</a>
        <?php endif; ?>
        <?php if ($applications['current_page'] < $applications['last_page']): ?>
            <a href="?page=<?= $applications['current_page'] + 1 ?><?= !empty($_GET['status']) ? '&status=' . htmlspecialchars($_GET['status']) : '' ?><?= !empty($search) ? '&q=' . urlencode($search) : '' ?>" class="px-3 py-1.5 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">Next</a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
