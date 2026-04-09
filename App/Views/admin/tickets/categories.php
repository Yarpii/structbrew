<?php ?>
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">Manage ticket categories</p>
    <button onclick="document.getElementById('createCatModal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Category
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
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Description</th>
                <th class="text-center px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Active</th>
                <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($categories as $cat): ?>
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 font-medium text-gray-800"><?= htmlspecialchars($cat['name']) ?></td>
                <td class="px-5 py-3 text-gray-500 text-sm"><?= htmlspecialchars($cat['department_name'] ?? '—') ?></td>
                <td class="px-5 py-3 text-gray-400 text-xs max-w-xs truncate"><?= htmlspecialchars($cat['description'] ?? '') ?></td>
                <td class="px-5 py-3 text-center">
                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium <?= (int)$cat['is_active'] ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' ?>">
                        <?= (int)$cat['is_active'] ? 'Active' : 'Inactive' ?>
                    </span>
                </td>
                <td class="px-5 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <button onclick="openEditCat(<?= htmlspecialchars(json_encode($cat), ENT_QUOTES) ?>)"
                                class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-md">Edit</button>
                        <form method="POST" action="/admin/tickets/categories/<?= $cat['id'] ?>/delete" onsubmit="return confirm('Delete category?')">
                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                            <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-medium rounded-md">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($categories)): ?>
            <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No categories yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Create Modal -->
<div id="createCatModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900">Add Category</h3>
            <button onclick="document.getElementById('createCatModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="/admin/tickets/categories" class="space-y-3">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Name *</label>
                <input type="text" name="name" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Department</label>
                <select name="department_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                    <option value="">None</option>
                    <?php foreach ($departments as $d): ?>
                    <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                <input type="text" name="description" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('createCatModal').classList.add('hidden')"
                        class="flex-1 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg">Cancel</button>
                <button type="submit" class="flex-1 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg">Create</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editCatModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900">Edit Category</h3>
            <button onclick="document.getElementById('editCatModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="editCatForm" method="POST" class="space-y-3">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Name *</label>
                <input type="text" name="name" id="editCatName" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Department</label>
                <select name="department_id" id="editCatDept" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                    <option value="">None</option>
                    <?php foreach ($departments as $d): ?>
                    <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                <input type="text" name="description" id="editCatDesc" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Sort Order</label>
                    <input type="number" name="sort_order" id="editCatOrder" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Active</label>
                    <select name="is_active" id="editCatActive" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('editCatModal').classList.add('hidden')"
                        class="flex-1 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg">Cancel</button>
                <button type="submit" class="flex-1 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditCat(cat) {
    document.getElementById('editCatName').value  = cat.name;
    document.getElementById('editCatDept').value  = cat.department_id || '';
    document.getElementById('editCatDesc').value  = cat.description || '';
    document.getElementById('editCatOrder').value = cat.sort_order;
    document.getElementById('editCatActive').value = cat.is_active;
    document.getElementById('editCatForm').action = '/admin/tickets/categories/' + cat.id;
    document.getElementById('editCatModal').classList.remove('hidden');
}
</script>
