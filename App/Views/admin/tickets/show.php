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
$statuses  = ['open','in_progress','waiting_customer','waiting_third_party','on_hold','resolved','closed','reopened'];
$priorities = ['low','normal','high','critical','urgent'];
$types     = ['order_support','product_inquiry','technical','billing','shipping','returns','general','partnership','advertising'];
?>

<?php if (!empty($flashSuccess)): ?>
<div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm"><?= htmlspecialchars($flashSuccess) ?></div>
<?php endif; ?>
<?php if (!empty($flashError)): ?>
<div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm"><?= htmlspecialchars($flashError) ?></div>
<?php endif; ?>

<div class="flex items-center gap-3 mb-6">
    <a href="/admin/tickets" class="text-gray-400 hover:text-gray-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div class="flex-1">
        <div class="flex items-center gap-3 flex-wrap">
            <h1 class="text-lg font-bold text-gray-900"><?= htmlspecialchars($ticket['subject']) ?></h1>
            <span class="text-xs font-mono text-gray-400"><?= htmlspecialchars($ticket['ticket_number']) ?></span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold <?= $statusClasses[$ticket['status']] ?? 'bg-gray-100 text-gray-600' ?>">
                <?= ucwords(str_replace('_', ' ', $ticket['status'])) ?>
            </span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold <?= $priorityClasses[$ticket['priority']] ?? 'bg-gray-100 text-gray-600' ?>">
                <?= ucfirst($ticket['priority']) ?>
            </span>
            <?php if ((int)($ticket['is_escalated'] ?? 0)): ?>
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-red-50 text-red-700">🔺 Escalated</span>
            <?php endif; ?>
            <?php if ($slaBreached ?? false): ?>
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-red-100 text-red-800">⚠ SLA Breached</span>
            <?php endif; ?>
        </div>
        <p class="text-xs text-gray-400 mt-1">Opened <?= date('d M Y H:i', strtotime($ticket['created_at'])) ?></p>
    </div>
    <div class="flex items-center gap-2 flex-shrink-0">
        <!-- Escalate -->
        <?php if (!(int)($ticket['is_escalated'] ?? 0)): ?>
        <form method="POST" action="/admin/tickets/<?= $ticket['id'] ?>/escalate" onsubmit="return confirm('Escalate this ticket to critical?')">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
            <button type="submit" class="px-3 py-2 bg-orange-50 hover:bg-orange-100 text-orange-700 text-xs font-medium rounded-lg border border-orange-200 transition-colors">
                Escalate
            </button>
        </form>
        <?php endif; ?>
        <!-- Merge -->
        <button onclick="document.getElementById('mergeModal').classList.remove('hidden')"
                class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors">
            Merge
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="{ tab: 'replies' }">

    <!-- Left: Replies + Reply Form -->
    <div class="lg:col-span-2 space-y-4">

        <!-- Tabs -->
        <div class="flex gap-1 bg-gray-100 p-1 rounded-lg w-fit">
            <button @click="tab = 'replies'" :class="tab === 'replies' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-1.5 text-sm font-medium rounded-md transition-colors">
                Replies <span class="ml-1 text-xs text-gray-400">(<?= count($replies) ?>)</span>
            </button>
            <button @click="tab = 'internal'" :class="tab === 'internal' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700'"
                    class="px-4 py-1.5 text-sm font-medium rounded-md transition-colors">
                Internal Notes
            </button>
        </div>

        <!-- Reply Thread -->
        <div id="replies" class="space-y-3">
            <?php foreach ($replies as $r): ?>
            <?php $isInternal = (int)($r['is_internal'] ?? 0); ?>
            <div x-show="(tab === 'replies' && !<?= $isInternal ?>) || (tab === 'internal' && <?= $isInternal ?>)"
                 class="bg-white rounded-xl border <?= $isInternal ? 'border-amber-200 bg-amber-50/30' : 'border-gray-200' ?> p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                            <?= $r['author_type'] === 'admin' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' ?>">
                            <?= strtoupper(substr($r['author_name'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800"><?= htmlspecialchars($r['author_name'] ?? 'Unknown') ?></p>
                            <p class="text-xs text-gray-400"><?= htmlspecialchars($r['author_type'] ?? '') ?></p>
                        </div>
                        <?php if ($isInternal): ?>
                        <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-medium rounded">Internal Note</span>
                        <?php endif; ?>
                    </div>
                    <span class="text-xs text-gray-400"><?= date('d M Y H:i', strtotime($r['created_at'])) ?></span>
                </div>
                <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap"><?= htmlspecialchars($r['body'] ?? '') ?></div>
            </div>
            <?php endforeach; ?>

            <?php if (empty($replies)): ?>
            <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-400 text-sm">
                No replies yet.
            </div>
            <?php endif; ?>
        </div>

        <!-- Reply Form -->
        <?php if (!in_array($ticket['status'], ['closed', 'resolved'])): ?>
        <div class="bg-white rounded-xl border border-gray-200 p-5" x-data="{ internal: false, body: '', cannedId: '' }">
            <h3 class="text-sm font-semibold text-gray-800 mb-4">Add Reply</h3>
            <form method="POST" action="/admin/tickets/<?= $ticket['id'] ?>/reply">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                <input type="hidden" name="is_internal" :value="internal ? '1' : '0'">

                <!-- Canned Responses -->
                <?php if (!empty($cannedAll)): ?>
                <div class="mb-3">
                    <select x-model="cannedId" @change="if (cannedId) { const c = <?= htmlspecialchars(json_encode(array_column($cannedAll, null, 'id')), ENT_QUOTES) ?>[cannedId]; if(c) body = c.body; cannedId=''; }"
                            class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm text-gray-600">
                        <option value="">— Insert canned response —</option>
                        <?php foreach ($cannedAll as $cr): ?>
                        <option value="<?= $cr['id'] ?>"><?= htmlspecialchars($cr['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <textarea name="body" x-model="body" rows="6" placeholder="Type your reply here..."
                          class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-y"
                          required></textarea>

                <div class="flex items-center justify-between mt-3">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <div @click="internal = !internal"
                             :class="internal ? 'bg-amber-500' : 'bg-gray-300'"
                             class="relative w-9 h-5 rounded-full transition-colors">
                            <div :class="internal ? 'translate-x-4' : 'translate-x-0'"
                                 class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform"></div>
                        </div>
                        <span class="text-sm text-gray-600">Internal note (not visible to customer)</span>
                    </label>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Send Reply
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <!-- Right: Sidebar Details -->
    <div class="space-y-4">

        <!-- Requester -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Requester</h3>
            <?php if (!empty($ticket['customer_first'])): ?>
            <p class="text-sm font-semibold text-gray-800"><?= htmlspecialchars($ticket['customer_first'] . ' ' . $ticket['customer_last']) ?></p>
            <p class="text-xs text-gray-400"><?= htmlspecialchars($ticket['customer_email_addr'] ?? '') ?></p>
            <a href="/admin/customers/<?= $ticket['customer_id'] ?>" class="mt-2 inline-block text-xs text-blue-600 hover:underline">View customer →</a>
            <?php elseif (!empty($ticket['guest_email'])): ?>
            <p class="text-sm font-semibold text-gray-800"><?= htmlspecialchars($ticket['guest_name'] ?? 'Guest') ?></p>
            <p class="text-xs text-gray-400"><?= htmlspecialchars($ticket['guest_email']) ?></p>
            <?php else: ?>
            <p class="text-sm text-gray-400">Unknown</p>
            <?php endif; ?>
        </div>

        <!-- SLA -->
        <?php if (!empty($ticket['sla_resolution_due_at'])): ?>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">SLA</h3>
            <div class="space-y-2 text-xs">
                <?php if (!empty($ticket['sla_first_response_due_at'])): ?>
                <div class="flex justify-between">
                    <span class="text-gray-500">First Response</span>
                    <span class="<?= (int)($ticket['sla_first_response_met'] ?? 1) ? 'text-green-600' : 'text-red-600' ?> font-medium">
                        <?= date('d M H:i', strtotime($ticket['sla_first_response_due_at'])) ?>
                    </span>
                </div>
                <?php endif; ?>
                <div class="flex justify-between">
                    <span class="text-gray-500">Resolution</span>
                    <span class="<?= ($slaBreached ?? false) ? 'text-red-600 font-bold' : 'text-amber-600' ?> font-medium">
                        <?= date('d M H:i', strtotime($ticket['sla_resolution_due_at'])) ?>
                    </span>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Edit Properties -->
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Properties</h3>
            <form method="POST" action="/admin/tickets/<?= $ticket['id'] ?>/update" class="space-y-3">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                <input type="hidden" name="subject" value="<?= htmlspecialchars($ticket['subject']) ?>">

                <div>
                    <label class="block text-xs text-gray-500 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <?php foreach ($statuses as $s): ?>
                        <option value="<?= $s ?>" <?= $ticket['status'] === $s ? 'selected' : '' ?>><?= ucwords(str_replace('_',' ',$s)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Priority</label>
                    <select name="priority" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <?php foreach ($priorities as $p): ?>
                        <option value="<?= $p ?>" <?= $ticket['priority'] === $p ? 'selected' : '' ?>><?= ucfirst($p) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Type</label>
                    <select name="type" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <?php foreach ($types as $tp): ?>
                        <option value="<?= $tp ?>" <?= $ticket['type'] === $tp ? 'selected' : '' ?>><?= ucwords(str_replace('_',' ',$tp)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Department</label>
                    <select name="department_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <option value="">None</option>
                        <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= ($ticket['department_id'] ?? '') == $d['id'] ? 'selected' : '' ?>><?= htmlspecialchars($d['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Category</label>
                    <select name="category_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <option value="">None</option>
                        <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($ticket['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Assigned Agent</label>
                    <select name="assigned_agent_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <option value="">Unassigned</option>
                        <?php foreach ($agents as $a): ?>
                        <option value="<?= $a['id'] ?>" <?= ($ticket['assigned_agent_id'] ?? '') == $a['id'] ? 'selected' : '' ?>><?= htmlspecialchars(trim($a['first_name'] . ' ' . $a['last_name'])) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="w-full py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition-colors">
                    Save Changes
                </button>
            </form>
        </div>

        <!-- Tags -->
        <?php if (!empty($tags)): ?>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Tags</h3>
            <div class="flex flex-wrap gap-1">
                <?php foreach ($tags as $tag): ?>
                <span class="px-2 py-0.5 rounded-full text-xs font-medium" style="background:<?= htmlspecialchars($tag['color'] ?? '#e2e8f0') ?>20; color:<?= htmlspecialchars($tag['color'] ?? '#64748b') ?>">
                    <?= htmlspecialchars($tag['name']) ?>
                </span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Linked Order -->
        <?php if (!empty($ticket['order_id'])): ?>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Linked Order</h3>
            <a href="/admin/orders/<?= $ticket['order_id'] ?>" class="text-sm text-blue-600 hover:underline">
                View Order #<?= $ticket['order_id'] ?>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Merge Modal -->
<div id="mergeModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900">Merge Ticket</h3>
            <button onclick="document.getElementById('mergeModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <p class="text-sm text-gray-600 mb-4">All replies from this ticket will be moved to the target ticket, and this ticket will be closed.</p>
        <form method="POST" action="/admin/tickets/<?= $ticket['id'] ?>/merge">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-700 mb-1">Target Ticket ID</label>
                <input type="number" name="merge_into_id" min="1" placeholder="Enter ticket ID to merge into"
                       class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm" required>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="document.getElementById('mergeModal').classList.add('hidden')"
                        class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">Cancel</button>
                <button type="submit" class="flex-1 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">Merge & Close</button>
            </div>
        </form>
    </div>
</div>
