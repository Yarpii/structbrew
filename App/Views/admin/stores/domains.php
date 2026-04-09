<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">Map domains to store views — each domain resolves to a specific store view</p>
    <button onclick="document.getElementById('add-domain-form').classList.toggle('hidden')"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Domain
    </button>
</div>

<!-- Add Domain Form -->
<div id="add-domain-form" class="hidden bg-white rounded-xl border border-gray-200 p-6 mb-6">
    <h3 class="font-semibold text-gray-800 mb-4">Add Domain Mapping</h3>
    <form method="POST" action="/admin/stores/domains" class="space-y-4">
        <input type="hidden" name="_token" value="<?= \App\Core\Session::csrfToken() ?>">
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Domain</label>
                <input type="text" name="domain" required placeholder="www.scooterdynamics.nl" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Store View</label>
                <select name="store_view_id" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                    <?php foreach ($allStoreViews ?? [] as $sv): ?>
                    <option value="<?= $sv['id'] ?>"><?= htmlspecialchars($sv['name']) ?> (<?= $sv['locale'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex items-end gap-4">
                <label class="flex items-center gap-2 pb-2"><input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-blue-600"> <span class="text-sm">Active</span></label>
                <label class="flex items-center gap-2 pb-2"><input type="checkbox" name="is_primary" value="1" class="rounded border-gray-300 text-blue-600"> <span class="text-sm">Primary</span></label>
            </div>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">Add Domain</button>
    </form>
</div>

<!-- Domains Table -->
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Domain</th>
                <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Store View</th>
                <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Locale</th>
                <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Primary</th>
                <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Active</th>
                <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php if (!empty($domains)): ?>
                <?php foreach ($domains as $domain): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 font-mono text-sm text-gray-900"><?= htmlspecialchars($domain['domain']) ?></td>
                    <td class="px-6 py-3 text-gray-700"><?= htmlspecialchars($domain['store_view_name'] ?? '—') ?></td>
                    <td class="px-6 py-3"><span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded"><?= $domain['locale'] ?? '—' ?></span></td>
                    <td class="px-6 py-3 text-center"><?= $domain['is_primary'] ? '<span class="text-blue-600 text-xs font-medium">Primary</span>' : '—' ?></td>
                    <td class="px-6 py-3 text-center"><span class="w-2 h-2 rounded-full inline-block <?= $domain['is_active'] ? 'bg-green-500' : 'bg-gray-300' ?>"></span></td>
                    <td class="px-6 py-3 text-right">
                        <form method="POST" action="/admin/stores/domains/<?= $domain['id'] ?>/delete" class="inline" onsubmit="return confirm('Remove this domain?')">
                            <input type="hidden" name="_token" value="<?= \App\Core\Session::csrfToken() ?>">
                            <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Remove</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">No domains configured</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
