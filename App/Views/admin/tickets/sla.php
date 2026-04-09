<?php
$priorityOptions = ['low','normal','high','critical','urgent'];
?>
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">Define SLA targets per priority level</p>
    <button onclick="document.getElementById('createSlaModal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add SLA Policy
    </button>
</div>

<?php if (!empty($flashSuccess)): ?>
<div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm"><?= htmlspecialchars($flashSuccess) ?></div>
<?php endif; ?>
<?php if (!empty($flashError)): ?>
<div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm"><?= htmlspecialchars($flashError) ?></div>
<?php endif; ?>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Name</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Priority</th>
                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase">First Response (h)</th>
                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Resolution (h)</th>
                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Biz Hrs Only</th>
                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Active</th>
                <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($policies as $pol): ?>
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3">
                    <p class="font-medium text-gray-800"><?= htmlspecialchars($pol['name']) ?></p>
                    <?php if (!empty($pol['description'])): ?>
                    <p class="text-xs text-gray-400"><?= htmlspecialchars($pol['description']) ?></p>
                    <?php endif; ?>
                </td>
                <td class="px-5 py-3">
                    <span class="px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700"><?= ucfirst($pol['applies_to_priority']) ?></span>
                </td>
                <td class="px-5 py-3 text-center text-gray-600"><?= (int)$pol['first_response_hours'] ?>h</td>
                <td class="px-5 py-3 text-center text-gray-600"><?= (int)$pol['resolution_hours'] ?>h</td>
                <td class="px-5 py-3 text-center">
                    <?= (int)$pol['business_hours_only'] ? '<span class="text-green-600">✓</span>' : '<span class="text-gray-300">—</span>' ?>
                </td>
                <td class="px-5 py-3 text-center">
                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium <?= (int)$pol['is_active'] ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' ?>">
                        <?= (int)$pol['is_active'] ? 'Active' : 'Inactive' ?>
                    </span>
                </td>
                <td class="px-5 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <button onclick="openEditSla(<?= htmlspecialchars(json_encode($pol), ENT_QUOTES) ?>)"
                                class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-md">Edit</button>
                        <form method="POST" action="/admin/tickets/sla/<?= $pol['id'] ?>/delete" onsubmit="return confirm('Delete SLA policy?')">
                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                            <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-medium rounded-md">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($policies)): ?>
            <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400">No SLA policies defined.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Create Modal -->
<div id="createSlaModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900">Add SLA Policy</h3>
            <button onclick="document.getElementById('createSlaModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="/admin/tickets/sla" class="space-y-3">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Name *</label>
                    <input type="text" name="name" required placeholder="e.g. Urgent SLA" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                    <input type="text" name="description" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Applies to Priority</label>
                    <select name="applies_to_priority" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <?php foreach ($priorityOptions as $p): ?>
                        <option value="<?= $p ?>"><?= ucfirst($p) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Business Hours Only</label>
                    <select name="business_hours_only" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <option value="0">No (24/7)</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">First Response (hours)</label>
                    <input type="number" name="first_response_hours" value="8" min="1" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Resolution (hours)</label>
                    <input type="number" name="resolution_hours" value="48" min="1" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('createSlaModal').classList.add('hidden')"
                        class="flex-1 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg">Cancel</button>
                <button type="submit" class="flex-1 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editSlaModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900">Edit SLA Policy</h3>
            <button onclick="document.getElementById('editSlaModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="editSlaForm" method="POST" class="space-y-3">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
            <div class="grid grid-cols-2 gap-3">
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Name *</label>
                    <input type="text" name="name" id="editSlaName" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                    <input type="text" name="description" id="editSlaDesc" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Priority</label>
                    <select name="applies_to_priority" id="editSlaPriority" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <?php foreach ($priorityOptions as $p): ?>
                        <option value="<?= $p ?>"><?= ucfirst($p) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Business Hrs Only</label>
                    <select name="business_hours_only" id="editSlaBiz" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <option value="0">No (24/7)</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">First Response (h)</label>
                    <input type="number" name="first_response_hours" id="editSlaFR" min="1" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Resolution (h)</label>
                    <input type="number" name="resolution_hours" id="editSlaRes" min="1" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Active</label>
                    <select name="is_active" id="editSlaActive" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('editSlaModal').classList.add('hidden')"
                        class="flex-1 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg">Cancel</button>
                <button type="submit" class="flex-1 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditSla(pol) {
    document.getElementById('editSlaName').value     = pol.name;
    document.getElementById('editSlaDesc').value     = pol.description || '';
    document.getElementById('editSlaPriority').value = pol.applies_to_priority;
    document.getElementById('editSlaBiz').value      = pol.business_hours_only;
    document.getElementById('editSlaFR').value       = pol.first_response_hours;
    document.getElementById('editSlaRes').value      = pol.resolution_hours;
    document.getElementById('editSlaActive').value   = pol.is_active;
    document.getElementById('editSlaForm').action    = '/admin/tickets/sla/' + pol.id;
    document.getElementById('editSlaModal').classList.remove('hidden');
}
</script>
