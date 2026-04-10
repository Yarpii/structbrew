<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">Manage store views — each view represents a locale/language version</p>
    <button onclick="document.getElementById('add-view-form').classList.toggle('hidden')"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Store View
    </button>
</div>

<!-- Add Form -->
<div id="add-view-form" class="hidden bg-white rounded-xl border border-gray-200 p-6 mb-6">
    <h3 class="font-semibold text-gray-800 mb-4">New Store View</h3>
    <form method="POST" action="/admin/stores/views" class="space-y-4">
        <input type="hidden" name="_csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Store</label>
                <select name="store_id" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                    <?php foreach ($stores ?? [] as $store): ?>
                    <option value="<?= (int) $store['id'] ?>"><?= htmlspecialchars($store['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                <input type="text" name="code" required placeholder="nl_nl" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
            </div>
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" required placeholder="Netherlands (Dutch)" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Locale</label>
                <select name="locale" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                    <?php foreach (['en_US','en_GB','nl_NL','de_DE','fr_FR','es_ES','it_IT','pt_PT','pl_PL','sv_SE','da_DK','nb_NO','fi_FI','cs_CZ','hu_HU','ro_RO','bg_BG','hr_HR','sk_SK','sl_SI','el_GR','tr_TR'] as $loc): ?>
                    <option value="<?= htmlspecialchars($loc) ?>"><?= htmlspecialchars($loc) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                <select name="currency_code" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                    <?php foreach (['EUR','USD','GBP','CHF','SEK','NOK','DKK','PLN','CZK','HUF','RON','BGN','HRK','TRY'] as $cur): ?>
                    <option value="<?= htmlspecialchars($cur) ?>"><?= htmlspecialchars($cur) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Theme</label>
                <input type="text" name="theme" value="default" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                <input type="number" name="sort_order" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
            </div>
        </div>
        <div class="flex gap-4">
            <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-blue-600"> <span class="text-sm">Active</span></label>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_default" value="1" class="rounded border-gray-300 text-blue-600"> <span class="text-sm">Default</span></label>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">Create Store View</button>
    </form>
</div>

<!-- Store Views Table -->
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Name</th>
                <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Code</th>
                <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Store</th>
                <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Locale</th>
                <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Currency</th>
                <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Theme</th>
                <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Default</th>
                <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Active</th>
                <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php if (!empty($views)): ?>
                <?php foreach ($views as $view): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-3 font-medium text-gray-900"><?= htmlspecialchars($view['name']) ?></td>
                    <td class="px-6 py-3 text-gray-500 font-mono text-xs"><?= htmlspecialchars($view['code']) ?></td>
                    <td class="px-6 py-3 text-gray-500"><?= htmlspecialchars($view['store_name'] ?? '—') ?></td>
                    <td class="px-6 py-3"><span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded"><?= htmlspecialchars($view['locale'] ?? '') ?></span></td>
                    <td class="px-6 py-3"><span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded"><?= htmlspecialchars($view['currency_code'] ?? '') ?></span></td>
                    <td class="px-6 py-3 text-gray-500"><?= htmlspecialchars($view['theme']) ?></td>
                    <td class="px-6 py-3 text-center"><?= !empty($view['is_default']) ? '<span class="text-blue-600">Yes</span>' : '—' ?></td>
                    <td class="px-6 py-3 text-center"><span class="w-2 h-2 rounded-full inline-block <?= !empty($view['is_active']) ? 'bg-green-500' : 'bg-gray-300' ?>"></span></td>
                    <td class="px-6 py-3 text-right">
                        <a href="/admin/stores/views/<?= (int) $view['id'] ?>/edit" class="text-blue-600 hover:text-blue-700 text-xs">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="9" class="px-6 py-12 text-center text-gray-400">No store views configured</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
