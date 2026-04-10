<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-lg font-medium text-gray-900">UI Translations</h1>
        <p class="text-sm text-gray-500 mt-1">Manage translations for <?= count($storeViews) ?> store views</p>
    </div>
    <?php if ($selectedStoreView): ?>
    <div class="flex items-center gap-2">
        <!-- Import -->
        <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            Import CSV
        </button>
        <!-- Export -->
        <a href="/admin/content/translations/export?store_view_id=<?= (int)$selectedStoreViewId ?>"
           class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Export CSV
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Import Modal -->
<?php if ($selectedStoreView): ?>
<div id="importModal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40" onclick="document.getElementById('importModal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-xl border border-gray-200 shadow-xl w-full max-w-md mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-900">Import Translations</h2>
            <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <p class="text-sm text-gray-500 mb-4">
            Upload a CSV file with columns <code class="bg-gray-100 px-1 rounded text-xs">group</code>,
            <code class="bg-gray-100 px-1 rounded text-xs">key</code>,
            <code class="bg-gray-100 px-1 rounded text-xs">value</code>.
            Existing translations will be overwritten.
        </p>
        <form method="POST" action="/admin/content/translations/import" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="_csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
            <input type="hidden" name="store_view_id" value="<?= (int)$selectedStoreViewId ?>">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    CSV File <span class="text-red-500">*</span>
                </label>
                <input type="file" name="csv_file" accept=".csv" required
                       class="block w-full text-sm text-gray-700 border border-gray-300 rounded-lg cursor-pointer bg-white file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                    Import
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Store View Selector -->
<div class="bg-white rounded-xl border border-gray-200 mb-6 p-4">
    <div class="flex items-center justify-between gap-6">
        <div class="flex-1 max-w-md">
            <label class="block text-xs font-medium text-gray-500 mb-2">Select Store View</label>
            <form method="GET" action="/admin/content/translations" class="flex gap-2">
                <select name="store_view_id" onchange="this.form.submit()"
                        class="flex-1 px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Select a store view --</option>
                    <?php foreach ($storeViews as $sv):
                        $stat  = $stats[(int)$sv['id']] ?? ['total' => 0, 'translated' => 0, 'missing' => 0, 'percentage' => 0];
                        $isMaster = (int)$sv['id'] === $masterStoreViewId;
                    ?>
                    <option value="<?= (int)$sv['id'] ?>"
                            <?= $selectedStoreViewId === (int)$sv['id'] ? 'selected' : '' ?>>
                        <?= $isMaster ? '★ ' : '' ?><?= htmlspecialchars($sv['name']) ?> (<?= htmlspecialchars($sv['locale'] ?? '') ?>)<?= $isMaster ? ' — Master' : ' — ' . $stat['percentage'] . '% translated' ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php foreach (['group' => $group, 'q' => $search, 'status' => $status] as $key => $val): ?>
                    <?php if ($val !== ''): ?>
                    <input type="hidden" name="<?= $key ?>" value="<?= htmlspecialchars($val) ?>">
                    <?php endif; ?>
                <?php endforeach; ?>
            </form>
        </div>

        <!-- Translation Stats -->
        <?php if ($selectedStoreView):
            $stat = $stats[$selectedStoreViewId] ?? ['total' => 0, 'translated' => 0, 'missing' => 0, 'percentage' => 0];
        ?>
        <div class="flex items-center gap-6">
            <?php if ($isMasterSelected): ?>
            <div class="flex items-center gap-2 px-3 py-1.5 bg-amber-50 border border-amber-200 rounded-lg">
                <span class="text-amber-600 text-base">★</span>
                <span class="text-xs font-medium text-amber-700">Master / Default language</span>
            </div>
            <?php else: ?>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600"><?= $stat['percentage'] ?>%</div>
                <div class="text-xs text-gray-500">Translated</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600"><?= $stat['translated'] ?></div>
                <div class="text-xs text-gray-500">Strings</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-red-600"><?= $stat['missing'] ?></div>
                <div class="text-xs text-gray-500">Missing</div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>


<!-- Filters -->
<?php if ($selectedStoreView): ?>
<div class="bg-white rounded-xl border border-gray-200 mb-6">
    <form method="GET" action="/admin/content/translations" class="p-4 flex flex-wrap gap-4 items-end">
        <input type="hidden" name="store_view_id" value="<?= (int)$selectedStoreViewId ?>">

        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
            <input type="text" name="q" value="<?= htmlspecialchars($search) ?>"
                   placeholder="Translation key or value..."
                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="w-48">
            <label class="block text-xs font-medium text-gray-500 mb-1">Group</label>
            <select name="group" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Groups</option>
                <?php foreach ($groups as $g): ?>
                <option value="<?= htmlspecialchars($g) ?>" <?= $group === $g ? 'selected' : '' ?>>
                    <?= htmlspecialchars($g) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="w-40">
            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">All</option>
                <option value="translated" <?= $status === 'translated' ? 'selected' : '' ?>>Translated</option>
                <option value="missing" <?= $status === 'missing' ? 'selected' : '' ?>>Missing</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-lg transition-colors">Filter</button>
    </form>
</div>

<!-- Add New Key (master only) -->
<?php if ($isMasterSelected): ?>
<div class="mb-6" x-data="{ open: false }">
    <button type="button" @click="open = !open"
            class="w-full flex items-center justify-between px-4 py-3 bg-amber-50 border border-amber-200 rounded-xl text-sm font-medium text-amber-800 hover:bg-amber-100 transition-colors">
        <span class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add New Translation Key
        </span>
        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-show="open" x-cloak class="mt-2 bg-white border border-amber-200 rounded-xl p-5">
        <p class="text-xs text-gray-500 mb-4">
            The new key will be created in this master store view and automatically added (empty) to all other store views for translation.
        </p>
        <form method="POST" action="/admin/content/translations/key" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <input type="hidden" name="_csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
            <input type="hidden" name="store_view_id" value="<?= (int)$selectedStoreViewId ?>">

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Group <span class="text-red-500">*</span></label>
                <div class="flex gap-2" x-data="{ useNew: false }">
                    <div class="flex-1" x-show="!useNew">
                        <select name="group" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400">
                            <?php foreach ($groups as $g): ?>
                            <option value="<?= htmlspecialchars($g) ?>"><?= htmlspecialchars($g) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="flex-1" x-show="useNew" x-cloak>
                        <input type="text" name="group" placeholder="new_group"
                               pattern="[a-z0-9_]+" title="Lowercase letters, digits and underscores only"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400">
                    </div>
                    <button type="button" @click="useNew = !useNew"
                            class="px-2 py-1.5 text-xs text-gray-500 border border-gray-300 rounded-lg hover:bg-gray-50 whitespace-nowrap"
                            x-text="useNew ? '← Pick' : '+ New'"></button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Key <span class="text-red-500">*</span></label>
                <input type="text" name="key" required placeholder="e.g. my_button_label"
                       pattern="[a-z0-9_]+" title="Lowercase letters, digits and underscores only"
                       class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Default value (master)</label>
                <input type="text" name="value" placeholder="English text…"
                       class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400">
            </div>

            <div class="sm:col-span-3 flex justify-end">
                <button type="submit"
                        class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition-colors">
                    Create Key &amp; Propagate
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Translations Table -->
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase w-20">Group</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase w-1/3">Key</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase flex-1">
                        Translation
                        <span class="block text-[10px] text-gray-400 font-normal">
                            <?= htmlspecialchars($selectedStoreView['locale'] ?? '') ?>
                        </span>
                    </th>
                    <?php if ($isMasterSelected): ?>
                    <th class="w-10"></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (!empty($translationKeys)): ?>
                    <?php foreach ($translationKeys as $key): ?>
                    <tr class="hover:bg-gray-50 group" x-data="{ editing: false }">
                        <td class="px-4 py-3">
                            <span class="inline-block px-1.5 py-0.5 bg-gray-100 text-gray-600 text-[10px] rounded font-medium">
                                <?= htmlspecialchars($key['group'] ?? 'general') ?>
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <code class="text-xs text-gray-700 font-mono bg-gray-50 px-1.5 py-0.5 rounded"><?= htmlspecialchars($key['key']) ?></code>
                        </td>
                        <td class="px-4 py-3">
                            <!-- Display mode -->
                            <div x-show="!editing" class="min-h-[32px] flex items-center justify-between group">
                                <span class="<?= $key['value'] ? 'text-sm text-gray-700' : 'text-xs text-red-400 italic' ?>">
                                    <?= $key['value'] ? htmlspecialchars($key['value']) : 'missing' ?>
                                </span>
                                <button type="button"
                                        @click="editing = true; $nextTick(() => $refs.translateInput?.focus())"
                                        title="Edit"
                                        class="ml-2 p-1 text-gray-300 hover:text-blue-500 opacity-0 group-hover:opacity-100 transition-opacity rounded flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </button>
                            </div>
                            <!-- Edit mode -->
                            <form x-show="editing" x-cloak method="POST" action="/admin/content/translations"
                                  @submit.prevent="submitTranslation($el)" class="flex gap-2">
                                <input type="hidden" name="_csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
                                <input type="hidden" name="key_id" value="<?= (int)$key['id'] ?>">
                                <input type="hidden" name="store_view_id" value="<?= (int)$selectedStoreViewId ?>">
                                <textarea name="value" 
                                       x-ref="translateInput"
                                       @keydown.escape="editing = false"
                                       class="flex-1 px-2 py-1.5 rounded border border-blue-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                       rows="3" maxlength="2000"><?= htmlspecialchars($key['value']) ?></textarea>
                                <div class="flex flex-col gap-1">
                                    <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors whitespace-nowrap">
                                        Save
                                    </button>
                                    <button type="button" @click="editing = false" class="px-3 py-1.5 bg-gray-100 text-gray-600 text-xs rounded hover:bg-gray-200 transition-colors">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </td>
                        <?php if ($isMasterSelected): ?>
                        <td class="px-2 py-3">
                            <form method="POST" action="/admin/content/translations/key/<?= (int)$key['id'] ?>/delete"
                                  onsubmit="return confirm('Delete key &quot;<?= htmlspecialchars($key['key']) ?>&quot; from ALL store views?')">
                                <input type="hidden" name="_csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
                                <input type="hidden" name="store_view_id" value="<?= (int)$selectedStoreViewId ?>">
                                <button type="submit" title="Delete key from all store views"
                                        class="p-1.5 text-gray-300 hover:text-red-500 rounded transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="<?= $isMasterSelected ? 4 : 3 ?>" class="px-6 py-12 text-center text-gray-400">No translation keys found</td></tr>
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
            <a href="?store_view_id=<?= (int)$selectedStoreViewId ?>&page=<?= $i ?><?php 
                foreach (['group' => $group, 'q' => $search, 'status' => $status] as $key => $val) {
                    if ($val !== '') echo '&' . $key . '=' . urlencode($val);
                }
            ?>"
               class="px-3 py-1.5 text-sm rounded-lg <?= $i === ($pagination['current_page'] ?? 1) ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<p class="mt-4 text-xs text-gray-400">
    💡 <strong>Tip:</strong> Hover a row and click the ✏️ pencil icon to edit. Press <kbd>Escape</kbd> to cancel.
</p>

<script>
async function submitTranslation(formElement) {
    const formData = new FormData(formElement);
    const data = Object.fromEntries(formData);

    try {
        const response = await fetch('/admin/content/translations', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            const newValue = (data.value || '').trim();
            const row = formElement.closest('[x-data]');
            const valueSpan = row.querySelector('[x-show="!editing"] > span');

            if (valueSpan) {
                valueSpan.textContent = newValue || 'missing';
                valueSpan.className = newValue ? 'text-sm text-gray-700' : 'text-xs text-red-400 italic';
            }

            Alpine.$data(row).editing = false;
            showNotification('✓ Translation saved', 'success');
        } else {
            showNotification(result.error || 'Failed to save translation', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Failed to save translation', 'error');
    }
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-3 rounded-lg text-white text-sm ${
        type === 'success' ? 'bg-green-600' : 'bg-red-600'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => notification.remove(), 3000);
}
</script>

<?php else: ?>
<!-- No store view selected -->
<div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
    <svg class="w-12 h-12 text-blue-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 00.948-.684l1.498-4.493a1 1 0 011.502-.684l1.498 4.493a1 1 0 00.948.684H17a2 2 0 012 2v2a2 2 0 01-2 2H5a2 2 0 01-2-2V5z"/>
    </svg>
    <h3 class="text-lg font-medium text-gray-900 mb-1">Select a Store View</h3>
    <p class="text-sm text-gray-600">Choose a store view from the dropdown above to start managing translations.</p>
</div>
<?php endif; ?>
