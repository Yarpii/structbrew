<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">Manage UI translations across store views</p>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl border border-gray-200 mb-6">
    <form method="GET" action="/admin/content/translations" class="p-4 flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
            <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                   placeholder="Translation key or value..."
                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="w-48">
            <label class="block text-xs font-medium text-gray-500 mb-1">Group</label>
            <select name="group" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Groups</option>
                <?php foreach ($groups ?? [] as $group): ?>
                <option value="<?= htmlspecialchars($group) ?>" <?= ($_GET['group'] ?? '') === $group ? 'selected' : '' ?>>
                    <?= htmlspecialchars($group) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="w-40">
            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All</option>
                <option value="translated" <?= ($_GET['status'] ?? '') === 'translated' ? 'selected' : '' ?>>Translated</option>
                <option value="missing" <?= ($_GET['status'] ?? '') === 'missing' ? 'selected' : '' ?>>Missing Translations</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-lg transition-colors">Filter</button>
    </form>
</div>

<!-- Translations Table -->
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase w-16">Group</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase w-1/4">Key</th>
                    <?php foreach ($storeViews ?? [] as $sv): ?>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">
                        <?= htmlspecialchars($sv['name']) ?>
                        <span class="block text-[10px] text-gray-400 font-normal"><?= $sv['locale'] ?></span>
                    </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (!empty($translationKeys)): ?>
                    <?php foreach ($translationKeys as $key): ?>
                    <tr class="hover:bg-gray-50" x-data="{ editing: null }">
                        <td class="px-4 py-3">
                            <span class="inline-block px-1.5 py-0.5 bg-gray-100 text-gray-600 text-[10px] rounded font-medium">
                                <?= htmlspecialchars($key['group'] ?? 'general') ?>
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <code class="text-xs text-gray-700 font-mono bg-gray-50 px-1.5 py-0.5 rounded"><?= htmlspecialchars($key['key']) ?></code>
                        </td>
                        <?php foreach ($storeViews ?? [] as $sv):
                            $value = $key['values'][$sv['id']] ?? '';
                            $fieldId = $key['id'] . '-' . $sv['id'];
                        ?>
                        <td class="px-4 py-2">
                            <!-- Display mode -->
                            <div x-show="editing !== '<?= $fieldId ?>'" @dblclick="editing = '<?= $fieldId ?>'; $nextTick(() => $refs['input_<?= $fieldId ?>']?.focus())"
                                 class="cursor-pointer min-h-[32px] flex items-center group">
                                <?php if ($value): ?>
                                    <span class="text-sm text-gray-700"><?= htmlspecialchars($value) ?></span>
                                <?php else: ?>
                                    <span class="text-xs text-red-400 italic">missing</span>
                                <?php endif; ?>
                                <svg class="w-3 h-3 text-gray-300 ml-2 opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </div>
                            <!-- Edit mode -->
                            <form x-show="editing === '<?= $fieldId ?>'" x-cloak
                                  method="POST" action="/admin/content/translations/update"
                                  class="flex gap-1">
                                <input type="hidden" name="_token" value="<?= \Brew\Core\Session::csrfToken() ?>">
                                <input type="hidden" name="key_id" value="<?= $key['id'] ?>">
                                <input type="hidden" name="store_view_id" value="<?= $sv['id'] ?>">
                                <input type="text" name="value" value="<?= htmlspecialchars($value) ?>"
                                       x-ref="input_<?= $fieldId ?>"
                                       @keydown.escape="editing = null"
                                       class="flex-1 px-2 py-1 rounded border border-blue-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <button type="submit" class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </button>
                                <button type="button" @click="editing = null" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded hover:bg-gray-200">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </form>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="<?= 2 + count($storeViews ?? []) ?>" class="px-6 py-12 text-center text-gray-400">No translation keys found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if (($pagination['last_page'] ?? 1) > 1): ?>
    <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
        <p class="text-sm text-gray-500">
            Showing <?= $pagination['from'] ?? 0 ?> to <?= $pagination['to'] ?? 0 ?> of <?= $pagination['total'] ?? 0 ?> keys
        </p>
        <div class="flex gap-1">
            <?php for ($i = 1; $i <= $pagination['last_page']; $i++): ?>
            <a href="?page=<?= $i ?>&<?= http_build_query(array_diff_key($_GET, ['page' => ''])) ?>"
               class="px-3 py-1.5 text-sm rounded-lg <?= $i === ($pagination['current_page'] ?? 1) ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<p class="mt-4 text-xs text-gray-400">Double-click a translation value to edit it inline.</p>
