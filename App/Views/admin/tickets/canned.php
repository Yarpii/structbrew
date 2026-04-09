<?php ?>
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">Pre-written responses agents can insert instantly</p>
    <button onclick="document.getElementById('createCannedModal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Response
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
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Department</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Subject</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Preview</th>
                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Active</th>
                <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($canned as $cr): ?>
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 font-medium text-gray-800"><?= htmlspecialchars($cr['name']) ?></td>
                <td class="px-5 py-3 text-gray-500 text-sm"><?= htmlspecialchars($cr['department_name'] ?? '—') ?></td>
                <td class="px-5 py-3 text-gray-500 text-sm"><?= htmlspecialchars($cr['subject'] ?? '—') ?></td>
                <td class="px-5 py-3 text-gray-400 text-xs max-w-xs truncate"><?= htmlspecialchars(mb_strimwidth($cr['body'] ?? '', 0, 80, '…')) ?></td>
                <td class="px-5 py-3 text-center">
                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium <?= (int)$cr['is_active'] ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' ?>">
                        <?= (int)$cr['is_active'] ? 'Active' : 'Inactive' ?>
                    </span>
                </td>
                <td class="px-5 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <button onclick="openEditCanned(<?= htmlspecialchars(json_encode($cr), ENT_QUOTES) ?>)"
                                class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-md">Edit</button>
                        <form method="POST" action="/admin/tickets/canned/<?= $cr['id'] ?>/delete" onsubmit="return confirm('Delete canned response?')">
                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                            <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-medium rounded-md">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($canned)): ?>
            <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No canned responses yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Create Modal -->
<div id="createCannedModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900">Add Canned Response</h3>
            <button onclick="document.getElementById('createCannedModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="/admin/tickets/canned" class="space-y-3">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Name *</label>
                    <input type="text" name="name" required placeholder="Response name" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Department</label>
                    <select name="department_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <option value="">All departments</option>
                        <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Subject</label>
                <input type="text" name="subject" placeholder="Optional email subject" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Body *</label>
                <textarea name="body" rows="5" required placeholder="Response body..." class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm resize-y"></textarea>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('createCannedModal').classList.add('hidden')"
                        class="flex-1 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg">Cancel</button>
                <button type="submit" class="flex-1 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editCannedModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900">Edit Canned Response</h3>
            <button onclick="document.getElementById('editCannedModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="editCannedForm" method="POST" class="space-y-3">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Name *</label>
                    <input type="text" name="name" id="editCannedName" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Department</label>
                    <select name="department_id" id="editCannedDept" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <option value="">All departments</option>
                        <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Subject</label>
                <input type="text" name="subject" id="editCannedSubj" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Body *</label>
                <textarea name="body" id="editCannedBody" rows="5" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm resize-y"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" id="editCannedOrder" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Active</label>
                    <select name="is_active" id="editCannedActive" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('editCannedModal').classList.add('hidden')"
                        class="flex-1 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg">Cancel</button>
                <button type="submit" class="flex-1 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditCanned(cr) {
    document.getElementById('editCannedName').value   = cr.name;
    document.getElementById('editCannedDept').value   = cr.department_id || '';
    document.getElementById('editCannedSubj').value   = cr.subject || '';
    document.getElementById('editCannedBody').value   = cr.body;
    document.getElementById('editCannedOrder').value  = cr.sort_order;
    document.getElementById('editCannedActive').value = cr.is_active;
    document.getElementById('editCannedForm').action  = '/admin/tickets/canned/' + cr.id;
    document.getElementById('editCannedModal').classList.remove('hidden');
}
</script>
