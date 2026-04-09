<form method="POST" action="<?= $formAction ?? '/admin/attributes' ?>" class="space-y-6">
    <input type="hidden" name="_csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Attribute Details</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Label <span class="text-red-500">*</span></label>
                            <input type="text" name="label" required value="<?= htmlspecialchars($attribute['label'] ?? '') ?>"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Code <span class="text-red-500">*</span></label>
                            <input type="text" name="code" required value="<?= htmlspecialchars($attribute['code'] ?? '') ?>" placeholder="material-type"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm font-mono focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Input Type</label>
                            <?php $type = $attribute['input_type'] ?? 'text'; ?>
                            <select name="input_type" id="input_type"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="text" <?= $type === 'text' ? 'selected' : '' ?>>Text</option>
                                <option value="textarea" <?= $type === 'textarea' ? 'selected' : '' ?>>Textarea</option>
                                <option value="number" <?= $type === 'number' ? 'selected' : '' ?>>Number</option>
                                <option value="boolean" <?= $type === 'boolean' ? 'selected' : '' ?>>Yes / No</option>
                                <option value="select" <?= $type === 'select' ? 'selected' : '' ?>>Select</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                            <input type="number" name="sort_order" value="<?= (int) ($attribute['sort_order'] ?? 0) ?>"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Options (one per line)</label>
                        <textarea name="options_text" rows="5" id="options_text" placeholder="Only used for Select type"
                                  class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm font-mono focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($optionsText ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Settings</h3>
                <div class="space-y-4">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="is_required" value="1" <?= ((int) ($attribute['is_required'] ?? 0) === 1) ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Required on product</span>
                    </label>
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="is_filterable" value="1" <?= ((int) ($attribute['is_filterable'] ?? 0) === 1) ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Use as catalog filter</span>
                    </label>
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" value="1" <?= ((int) ($attribute['is_active'] ?? 1) === 1) ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Active</span>
                    </label>
                </div>
            </div>

            <?php if (!empty($attribute['id'])): ?>
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Information</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">ID</span>
                        <span class="text-gray-900"><?= (int) $attribute['id'] ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Updated</span>
                        <span class="text-gray-900"><?= htmlspecialchars($attribute['updated_at'] ?? '—') ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="flex items-center justify-between bg-white rounded-lg border border-gray-200 p-4 sticky bottom-4">
        <a href="/admin/attributes" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
            Save Attribute
        </button>
    </div>
</form>

<script>
(function () {
    var typeField = document.getElementById('input_type');
    var optionsField = document.getElementById('options_text');
    function syncOptionsVisibility() {
        if (!typeField || !optionsField) return;
        var wrapper = optionsField.closest('div');
        if (!wrapper) return;
        wrapper.style.display = typeField.value === 'select' ? '' : 'none';
    }
    if (typeField) {
        typeField.addEventListener('change', syncOptionsVisibility);
        syncOptionsVisibility();
    }
})();
</script>
