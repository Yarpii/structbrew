<!-- Header -->
<div class="mb-6">
    <p class="text-sm text-gray-500">Manage system configuration settings</p>
</div>

<!-- Scope Selector -->
<div class="bg-white rounded-lg border border-gray-200 mb-6 p-4" x-data="{ scope: '<?= htmlspecialchars((string) ($scope ?? 'global')) ?>' }">
    <form method="GET" action="/admin/config" class="flex flex-wrap gap-4 items-end">
        <div class="w-48">
            <label class="block text-xs font-medium text-gray-500 mb-1">Configuration Scope</label>
            <select name="scope" x-model="scope" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="global">Global</option>
                <option value="website" <?= ($scope ?? 'global') === 'website' ? 'selected' : '' ?>>Website</option>
                <option value="store" <?= ($scope ?? 'global') === 'store' ? 'selected' : '' ?>>Store</option>
                <option value="store_view" <?= ($scope ?? 'global') === 'store_view' ? 'selected' : '' ?>>Store View</option>
            </select>
        </div>

        <div class="w-64" x-show="scope === 'website'" x-cloak>
            <label class="block text-xs font-medium text-gray-500 mb-1">Website</label>
            <select name="scope_id" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <?php foreach (($websites ?? []) as $ws): ?>
                    <option value="<?= (int) $ws['id'] ?>" <?= (int) ($scopeId ?? 0) === (int) $ws['id'] ? 'selected' : '' ?>><?= htmlspecialchars((string) $ws['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="w-64" x-show="scope === 'store'" x-cloak>
            <label class="block text-xs font-medium text-gray-500 mb-1">Store</label>
            <select name="scope_id" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <?php foreach (($stores ?? []) as $st): ?>
                    <option value="<?= (int) $st['id'] ?>" <?= (int) ($scopeId ?? 0) === (int) $st['id'] ? 'selected' : '' ?>><?= htmlspecialchars((string) $st['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="w-64" x-show="scope === 'store_view'" x-cloak>
            <label class="block text-xs font-medium text-gray-500 mb-1">Store View</label>
            <select name="scope_id" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <?php foreach (($storeViewsList ?? []) as $sv): ?>
                    <option value="<?= (int) $sv['id'] ?>" <?= (int) ($scopeId ?? 0) === (int) $sv['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars((string) $sv['name']) ?> (<?= htmlspecialchars((string) ($sv['locale'] ?? '')) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
            Switch Scope
        </button>
    </form>
</div>

<!-- Configuration Groups -->
<form method="POST" action="/admin/config" class="space-y-6">
    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">
    <input type="hidden" name="scope" value="<?= htmlspecialchars((string) ($scope ?? 'global')) ?>">
    <input type="hidden" name="scope_id" value="<?= (int) ($scopeId ?? 0) ?>">

    <?php foreach (($groups ?? []) as $groupKey => $group): ?>
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="font-semibold text-gray-900"><?= htmlspecialchars((string) ($group['label'] ?? ucfirst((string) $groupKey))) ?></h3>
            </div>

            <div class="divide-y divide-gray-100">
                <?php foreach (($group['fields'] ?? []) as $path => $field): ?>
                    <div class="px-5 py-4 grid grid-cols-1 md:grid-cols-[320px_1fr_auto] gap-3 items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars((string) ($field['label'] ?? $path)) ?></p>
                            <p class="text-xs text-gray-400 font-mono"><?= htmlspecialchars((string) $path) ?></p>
                        </div>

                        <div>
                            <?php $type = (string) ($field['type'] ?? 'text'); $value = (string) ($field['value'] ?? ''); ?>
                            <?php if ($type === 'textarea'): ?>
                                <textarea name="config[<?= htmlspecialchars((string) $path) ?>]" rows="2" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($value) ?></textarea>
                            <?php elseif ($type === 'boolean'): ?>
                                <select name="config[<?= htmlspecialchars((string) $path) ?>]" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="0" <?= in_array(strtolower($value), ['', '0', 'false', 'off', 'no'], true) ? 'selected' : '' ?>>No</option>
                                    <option value="1" <?= in_array(strtolower($value), ['1', 'true', 'on', 'yes'], true) ? 'selected' : '' ?>>Yes</option>
                                </select>
                            <?php elseif ($type === 'password'): ?>
                                <input type="password"
                                       name="config[<?= htmlspecialchars((string) $path) ?>]"
                                       value="<?= htmlspecialchars($value) ?>"
                                       autocomplete="off"
                                       class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <?php else: ?>
                                <input type="<?= $type === 'email' ? 'email' : ($type === 'number' ? 'number' : 'text') ?>"
                                       <?= $type === 'number' ? 'step="0.01"' : '' ?>
                                       name="config[<?= htmlspecialchars((string) $path) ?>]"
                                       value="<?= htmlspecialchars($value) ?>"
                                       class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <?php endif; ?>
                        </div>

                        <div class="text-right">
                            <?php if (!empty($field['inherited'])): ?>
                                <span class="inline-flex items-center px-2 py-1 text-[11px] font-medium rounded bg-amber-100 text-amber-700">Inherited</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="flex justify-end">
        <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-md transition-colors">
            Save Configuration
        </button>
    </div>
</form>
