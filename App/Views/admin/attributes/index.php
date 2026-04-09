<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">Manage reusable product attributes</p>
    <a href="/admin/attributes/create"
       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Attribute
    </a>
</div>

<div class="bg-white rounded-lg border border-gray-200 mb-6">
    <form method="GET" action="/admin/attributes" class="p-4 flex gap-3 items-end">
        <div class="flex-1">
            <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
            <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Code or label..."
                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-lg transition-colors">Filter</button>
    </form>
</div>

<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 uppercase">Label</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 uppercase">Code</th>
                    <th class="text-left px-5 py-3 text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="text-center px-5 py-3 text-xs font-medium text-gray-500 uppercase">Required</th>
                    <th class="text-center px-5 py-3 text-xs font-medium text-gray-500 uppercase">Categories</th>
                    <th class="text-center px-5 py-3 text-xs font-medium text-gray-500 uppercase">Products</th>
                    <th class="text-center px-5 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="text-right px-5 py-3 text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (!empty($attributes)): ?>
                    <?php foreach ($attributes as $attribute): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-900"><?= htmlspecialchars($attribute['label']) ?></td>
                        <td class="px-5 py-3 text-gray-500 font-mono text-xs"><?= htmlspecialchars($attribute['code']) ?></td>
                        <td class="px-5 py-3 text-gray-600"><?= htmlspecialchars(ucfirst($attribute['input_type'])) ?></td>
                        <td class="px-5 py-3 text-center text-gray-600"><?= ((int) ($attribute['is_required'] ?? 0) === 1) ? 'Yes' : 'No' ?></td>
                        <td class="px-5 py-3 text-center text-gray-600"><?= (int) ($attribute['category_count'] ?? 0) ?></td>
                        <td class="px-5 py-3 text-center text-gray-600"><?= (int) ($attribute['product_count'] ?? 0) ?></td>
                        <td class="px-5 py-3 text-center">
                            <span class="inline-block w-2 h-2 rounded-full <?= ((int) ($attribute['is_active'] ?? 0) === 1) ? 'bg-green-500' : 'bg-gray-300' ?>"></span>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="/admin/attributes/<?= (int) $attribute['id'] ?>/edit" class="p-1.5 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-blue-50" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form method="POST" action="/admin/attributes/<?= (int) $attribute['id'] ?>/delete" onsubmit="return confirm('Delete this attribute?')">
                                    <input type="hidden" name="_csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="px-6 py-10 text-center text-gray-400">No attributes found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
