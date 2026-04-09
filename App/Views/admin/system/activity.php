<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">System activity log</p>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl border border-gray-200 mb-6">
    <form method="GET" action="/admin/system/activity" class="p-4 flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
            <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                   placeholder="Action, entity type..."
                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="w-48">
            <label class="block text-xs font-medium text-gray-500 mb-1">Admin User</label>
            <select name="admin_user" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Users</option>
                <?php foreach ($adminUsers ?? [] as $au): ?>
                <option value="<?= $au['id'] ?>" <?= ($_GET['admin_user'] ?? '') == $au['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars(($au['first_name'] ?? '') . ' ' . ($au['last_name'] ?? '')) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="w-40">
            <label class="block text-xs font-medium text-gray-500 mb-1">Entity Type</label>
            <select name="entity_type" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All</option>
                <?php foreach ($entityTypes ?? [] as $et): ?>
                <option value="<?= htmlspecialchars($et) ?>" <?= ($_GET['entity_type'] ?? '') === $et ? 'selected' : '' ?>>
                    <?= htmlspecialchars(ucfirst($et)) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-lg transition-colors">Filter</button>
    </form>
</div>

<!-- Activity Log Table -->
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Admin User</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Action</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Entity Type</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Entity ID</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Details</th>
                    <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Timestamp</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (!empty($activities['data'])): ?>
                    <?php foreach ($activities['data'] as $activity): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center text-[10px] font-medium text-gray-600">
                                    <?= strtoupper(substr($activity['admin_first_name'] ?? '', 0, 1) . substr($activity['admin_last_name'] ?? '', 0, 1)) ?>
                                </div>
                                <span class="text-sm text-gray-900"><?= htmlspecialchars(($activity['admin_first_name'] ?? '') . ' ' . ($activity['admin_last_name'] ?? '')) ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-3">
                            <?php
                            $actionColors = [
                                'create' => 'bg-green-100 text-green-700',
                                'update' => 'bg-blue-100 text-blue-700',
                                'delete' => 'bg-red-100 text-red-700',
                                'login'  => 'bg-purple-100 text-purple-700',
                                'logout' => 'bg-gray-100 text-gray-700',
                            ];
                            $colorClass = $actionColors[$activity['action'] ?? ''] ?? 'bg-gray-100 text-gray-700';
                            ?>
                            <span class="inline-block px-2 py-0.5 text-xs rounded-full font-medium <?= $colorClass ?>">
                                <?= htmlspecialchars(ucfirst($activity['action'] ?? '')) ?>
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-500 text-sm">
                            <?= htmlspecialchars(ucfirst($activity['entity_type'] ?? '—')) ?>
                        </td>
                        <td class="px-6 py-3 text-gray-500 font-mono text-xs">
                            <?= htmlspecialchars($activity['entity_id'] ?? '—') ?>
                        </td>
                        <td class="px-6 py-3 text-gray-400 text-xs max-w-xs truncate">
                            <?= htmlspecialchars($activity['details'] ?? '') ?>
                        </td>
                        <td class="px-6 py-3 text-right text-gray-500 text-xs whitespace-nowrap">
                            <?= $activity['created_at'] ?? '' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">No activity recorded</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if (($activities['last_page'] ?? 1) > 1): ?>
    <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
        <p class="text-sm text-gray-500">
            Showing <?= $activities['from'] ?? 0 ?> to <?= $activities['to'] ?? 0 ?> of <?= $activities['total'] ?? 0 ?> entries
        </p>
        <div class="flex gap-1">
            <?php for ($i = 1; $i <= min($activities['last_page'], 10); $i++): ?>
            <a href="?page=<?= $i ?>&<?= htmlspecialchars(http_build_query(array_diff_key($_GET, ['page' => '']))) ?>"
               class="px-3 py-1.5 text-sm rounded-lg <?= $i === ($activities['current_page'] ?? 1) ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
            <?php if (($activities['last_page'] ?? 1) > 10): ?>
            <span class="px-2 py-1.5 text-sm text-gray-400">...</span>
            <a href="?page=<?= $activities['last_page'] ?>&<?= htmlspecialchars(http_build_query(array_diff_key($_GET, ['page' => '']))) ?>"
               class="px-3 py-1.5 text-sm rounded-lg text-gray-600 hover:bg-gray-100">
                <?= $activities['last_page'] ?>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
