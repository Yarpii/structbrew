<?php
$priorityClasses = [
    'low'      => 'bg-slate-100 text-slate-600',
    'normal'   => 'bg-blue-50 text-blue-700',
    'high'     => 'bg-amber-50 text-amber-700',
    'critical' => 'bg-orange-50 text-orange-700',
    'urgent'   => 'bg-red-50 text-red-700',
];
$statusClasses = [
    'open'                 => 'bg-green-50 text-green-700',
    'in_progress'          => 'bg-blue-50 text-blue-700',
    'waiting_customer'     => 'bg-amber-50 text-amber-700',
    'waiting_third_party'  => 'bg-purple-50 text-purple-700',
    'on_hold'              => 'bg-slate-100 text-slate-600',
    'resolved'             => 'bg-emerald-50 text-emerald-700',
    'closed'               => 'bg-gray-100 text-gray-500',
    'reopened'             => 'bg-rose-50 text-rose-700',
];
?>
<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">Manage all support tickets</p>
    <div class="flex gap-2">
        <a href="/admin/tickets?export=csv" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Export CSV
        </a>
        <a href="/admin/tickets/create" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Ticket
        </a>
    </div>
</div>

<?php if (!empty($flashSuccess)): ?>
<div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm"><?= htmlspecialchars($flashSuccess) ?></div>
<?php endif; ?>
<?php if (!empty($flashError)): ?>
<div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm"><?= htmlspecialchars($flashError) ?></div>
<?php endif; ?>

<!-- Filters -->
<div class="bg-white rounded-xl border border-gray-200 mb-6">
    <form method="GET" action="/admin/tickets" class="p-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
            <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Ticket #, subject, email..."
                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="w-44">
            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                <option value="">All Statuses</option>
                <?php foreach ($statuses as $s): ?>
                <option value="<?= $s ?>" <?= ($status ?? '') === $s ? 'selected' : '' ?>><?= ucwords(str_replace('_', ' ', $s)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="w-36">
            <label class="block text-xs font-medium text-gray-500 mb-1">Priority</label>
            <select name="priority" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                <option value="">All</option>
                <?php foreach ($priorities as $p): ?>
                <option value="<?= $p ?>" <?= ($priority ?? '') === $p ? 'selected' : '' ?>><?= ucfirst($p) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="w-44">
            <label class="block text-xs font-medium text-gray-500 mb-1">Department</label>
            <select name="department_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                <option value="">All Departments</option>
                <?php foreach ($departments as $d): ?>
                <option value="<?= $d['id'] ?>" <?= ($deptId ?? 0) == $d['id'] ? 'selected' : '' ?>><?= htmlspecialchars($d['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="w-44">
            <label class="block text-xs font-medium text-gray-500 mb-1">Agent</label>
            <select name="agent_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                <option value="">All Agents</option>
                <?php foreach ($agents as $a): ?>
                <option value="<?= $a['id'] ?>" <?= ($agentId ?? 0) == $a['id'] ? 'selected' : '' ?>><?= htmlspecialchars(trim($a['first_name'] . ' ' . $a['last_name'])) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition-colors">Filter</button>
        <a href="/admin/tickets" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-lg transition-colors">Reset</a>
    </form>
</div>

<!-- Ticket Table -->
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Ticket</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Requester</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Department</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Priority</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Agent</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Last Activity</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (!empty($tickets['data'])): ?>
                    <?php foreach ($tickets['data'] as $t): ?>
                    <?php
                        $slaBreached = !in_array($t['status'], ['resolved','closed']) && !empty($t['sla_resolution_due_at']) && strtotime($t['sla_resolution_due_at']) < time();
                    ?>
                    <tr class="hover:bg-gray-50 transition-colors <?= $slaBreached ? 'border-l-2 border-l-red-400' : '' ?>">
                        <td class="px-5 py-3">
                            <div class="flex items-start gap-2">
                                <?php if ((int)($t['is_escalated'] ?? 0)): ?>
                                <span title="Escalated" class="mt-0.5 inline-flex w-2 h-2 rounded-full bg-red-500 flex-shrink-0"></span>
                                <?php endif; ?>
                                <div>
                                    <a href="/admin/tickets/<?= $t['id'] ?>" class="font-semibold text-gray-900 hover:text-blue-600 transition-colors">
                                        <?= htmlspecialchars($t['subject']) ?>
                                    </a>
                                    <p class="text-xs text-gray-400 mt-0.5"><?= htmlspecialchars($t['ticket_number']) ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-gray-600">
                            <?php if (!empty($t['customer_first'])): ?>
                                <?= htmlspecialchars($t['customer_first'] . ' ' . $t['customer_last']) ?>
                            <?php elseif (!empty($t['guest_email'])): ?>
                                <span class="text-gray-400"><?= htmlspecialchars($t['guest_email']) ?></span>
                            <?php else: ?>
                                <span class="text-gray-400">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3">
                            <?php if (!empty($t['department_name'])): ?>
                            <span class="inline-flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full flex-shrink-0" style="background:<?= htmlspecialchars($t['department_color'] ?? '#94a3b8') ?>"></span>
                                <span class="text-gray-600 text-xs"><?= htmlspecialchars($t['department_name']) ?></span>
                            </span>
                            <?php else: ?>
                            <span class="text-gray-400 text-xs">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold <?= $statusClasses[$t['status']] ?? 'bg-gray-100 text-gray-600' ?>">
                                <?= ucwords(str_replace('_', ' ', $t['status'])) ?>
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold <?= $priorityClasses[$t['priority']] ?? 'bg-gray-100 text-gray-600' ?>">
                                <?= ucfirst($t['priority']) ?>
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-600 text-sm">
                            <?php $agentName = trim(($t['agent_first'] ?? '') . ' ' . ($t['agent_last'] ?? '')); ?>
                            <?= htmlspecialchars($agentName ?: 'Unassigned') ?>
                        </td>
                        <td class="px-5 py-3 text-right text-xs text-gray-400">
                            <?php $la = $t['last_activity_at'] ?? $t['created_at']; ?>
                            <?= $la ? date('d M Y H:i', strtotime($la)) : '—' ?>
                            <?php if ($slaBreached): ?>
                            <div class="text-red-500 font-medium">SLA Breached</div>
                            <?php elseif (!empty($t['sla_resolution_due_at']) && !in_array($t['status'], ['resolved','closed'])): ?>
                            <div class="text-amber-500">Due <?= date('d M', strtotime($t['sla_resolution_due_at'])) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="/admin/tickets/<?= $t['id'] ?>" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-medium rounded-md transition-colors">
                                View
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            No tickets found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if (!empty($tickets['total']) && $tickets['total'] > $tickets['per_page']): ?>
    <div class="px-5 py-4 border-t border-gray-200 flex items-center justify-between text-sm text-gray-500">
        <span><?= number_format($tickets['total']) ?> tickets</span>
        <div class="flex gap-1">
            <?php
            $totalPages = (int) ceil($tickets['total'] / $tickets['per_page']);
            $curPage    = $tickets['current_page'] ?? 1;
            $qs         = http_build_query(array_merge($_GET, ['page' => '__PAGE__']));
            for ($p = 1; $p <= $totalPages; $p++):
                $url = '/admin/tickets?' . str_replace('__PAGE__', (string) $p, $qs);
            ?>
            <a href="<?= htmlspecialchars($url) ?>" class="px-3 py-1 rounded <?= $p == $curPage ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-gray-200 text-gray-700' ?>"><?= $p ?></a>
            <?php endfor; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
