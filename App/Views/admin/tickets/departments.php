<?php ?>
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">Manage support departments</p>
    <button onclick="document.getElementById('createDeptModal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Department
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
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Color</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Name</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Code</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Description</th>
                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Order</th>
                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Active</th>
                <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($departments as $dept): ?>
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3">
                    <span class="inline-block w-6 h-6 rounded-full border border-gray-200" style="background:<?= htmlspecialchars($dept['color']) ?>"></span>
                </td>
                <td class="px-5 py-3 font-medium text-gray-800"><?= htmlspecialchars($dept['name']) ?></td>
                <td class="px-5 py-3 font-mono text-gray-500 text-xs"><?= htmlspecialchars($dept['code']) ?></td>
                <td class="px-5 py-3 text-gray-500 text-xs max-w-xs truncate"><?= htmlspecialchars($dept['description'] ?? '') ?></td>
                <td class="px-5 py-3 text-center text-gray-500"><?= (int)$dept['sort_order'] ?></td>
                <td class="px-5 py-3 text-center">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium <?= (int)$dept['is_active'] ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' ?>">
                        <?= (int)$dept['is_active'] ? 'Active' : 'Inactive' ?>
                    </span>
                </td>
                <td class="px-5 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <button onclick="openEditDept(<?= htmlspecialchars(json_encode($dept), ENT_QUOTES) ?>)"
                                class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-md transition-colors">Edit</button>
                        <form method="POST" action="/admin/tickets/departments/<?= (int) $dept['id'] ?>/delete" onsubmit="return confirm('Delete this department?')">
                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                            <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-medium rounded-md transition-colors">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($departments)): ?>
            <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400">No departments yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Create Modal -->
<div id="createDeptModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900">Add Department</h3>
            <button onclick="document.getElementById('createDeptModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="/admin/tickets/departments" class="space-y-3">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Name *</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Code *</label>
                    <input type="text" name="code" required placeholder="support" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                <input type="text" name="description" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Color</label>
                    <input type="color" name="color" value="#3b82f6" class="w-full h-10 rounded-lg border border-gray-300 cursor-pointer">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('createDeptModal').classList.add('hidden')"
                        class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg">Cancel</button>
                <button type="submit" class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editDeptModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900">Edit Department</h3>
            <button onclick="document.getElementById('editDeptModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="editDeptForm" method="POST" class="space-y-3">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Name *</label>
                <input type="text" name="name" id="editDeptName" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                <input type="text" name="description" id="editDeptDesc" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Color</label>
                    <input type="color" name="color" id="editDeptColor" class="w-full h-10 rounded-lg border border-gray-300 cursor-pointer">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" id="editDeptOrder" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Active</label>
                    <select name="is_active" id="editDeptActive" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('editDeptModal').classList.add('hidden')"
                        class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg">Cancel</button>
                <button type="submit" class="flex-1 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditDept(dept) {
    document.getElementById('editDeptName').value  = dept.name;
    document.getElementById('editDeptDesc').value  = dept.description || '';
    document.getElementById('editDeptColor').value = dept.color;
    document.getElementById('editDeptOrder').value = dept.sort_order;
    document.getElementById('editDeptActive').value = dept.is_active;
    document.getElementById('editDeptForm').action = '/admin/tickets/departments/' + dept.id;
    document.getElementById('editDeptModal').classList.remove('hidden');
}
</script>
