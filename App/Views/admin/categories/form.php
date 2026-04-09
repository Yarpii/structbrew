<form method="POST" action="<?= $formAction ?? '/admin/categories' ?>" enctype="multipart/form-data" class="space-y-6">
    <input type="hidden" name="_csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
    <input type="hidden" name="_csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="xl:col-span-2 space-y-6">
            <!-- General Info -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">General Information</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug <span class="text-red-500">*</span></label>
                        <input type="text" name="slug" required value="<?= htmlspecialchars($category['slug'] ?? '') ?>"
                               placeholder="e.g. brake-pads"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Parent Category</label>
                            <select name="parent_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">None (Root Category)</option>
                                <?php foreach ($parentCategories ?? [] as $parent): ?>
                                <option value="<?= $parent['id'] ?>" <?= ($category['parent_id'] ?? '') == $parent['id'] ? 'selected' : '' ?>>
                                    <?= str_repeat('— ', $parent['depth'] ?? 0) ?><?= htmlspecialchars($parent['name'] ?? $parent['slug']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                            <input type="number" name="position" value="<?= htmlspecialchars($category['position'] ?? 0) ?>"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Translations (per store view) -->
            <div class="bg-white rounded-xl border border-gray-200 p-6" x-data="{ activeTab: '<?= $storeViews[0]['id'] ?? 0 ?>' }">
                <h3 class="font-semibold text-gray-800 mb-4">Category Details (per Store View)</h3>

                <!-- Store View Tabs -->
                <div class="flex gap-1 border-b border-gray-200 mb-4 overflow-x-auto">
                    <?php foreach ($storeViews ?? [] as $sv): ?>
                    <button type="button" @click="activeTab = '<?= $sv['id'] ?>'"
                            :class="activeTab === '<?= $sv['id'] ?>' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                        <?= htmlspecialchars($sv['name']) ?>
                        <span class="text-xs text-gray-400">(<?= $sv['locale'] ?>)</span>
                    </button>
                    <?php endforeach; ?>
                </div>

                <?php foreach ($storeViews ?? [] as $sv):
                    $trans = $translations[$sv['id']] ?? [];
                ?>
                <div x-show="activeTab === '<?= $sv['id'] ?>'" x-cloak class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="translations[<?= $sv['id'] ?>][name]"
                               value="<?= htmlspecialchars($trans['name'] ?? '') ?>"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="translations[<?= $sv['id'] ?>][description]" rows="4"
                                  class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($trans['description'] ?? '') ?></textarea>
                    </div>
                    <!-- SEO -->
                    <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                            <input type="text" name="translations[<?= $sv['id'] ?>][meta_title]"
                                   value="<?= htmlspecialchars($trans['meta_title'] ?? '') ?>"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                            <input type="text" name="translations[<?= $sv['id'] ?>][meta_description]"
                                   value="<?= htmlspecialchars($trans['meta_description'] ?? '') ?>"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Image -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Category Image</h3>
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-400 transition-colors">
                    <input type="file" name="image" accept="image/*" class="hidden" id="category-image">
                    <label for="category-image" class="cursor-pointer">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm text-gray-500">Drop image here or <span class="text-blue-600 font-medium">browse</span></p>
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP (max 2MB)</p>
                    </label>
                </div>
                <?php if (!empty($category['image'])): ?>
                <div class="mt-4 flex items-center gap-4">
                    <img src="<?= htmlspecialchars($category['image']) ?>" alt="" class="w-20 h-20 rounded-lg object-cover">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remove_image" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="text-sm text-gray-600">Remove current image</span>
                    </label>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Status</h3>
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" value="1" <?= ($category['is_active'] ?? 1) ? 'checked' : '' ?>
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Active</span>
                </label>
            </div>

            <!-- Attributes -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Attributes</h3>
                <?php if (!empty($attributes)): ?>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    <?php foreach ($attributes as $attribute): ?>
                    <label class="flex items-start gap-2">
                        <input type="checkbox" name="attribute_ids[]" value="<?= $attribute['id'] ?>"
                               <?= in_array((int) $attribute['id'], $selectedAttributes ?? [], true) ? 'checked' : '' ?>
                               class="mt-0.5 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">
                            <?= htmlspecialchars($attribute['label']) ?>
                            <span class="text-xs text-gray-400">(<?= htmlspecialchars($attribute['code']) ?>)</span>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                    <p class="text-sm text-gray-500">No active attributes yet. Create them first in Catalog → Attributes.</p>
                <?php endif; ?>
            </div>

            <!-- Info -->
            <?php if (!empty($category['id'])): ?>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Information</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">ID</span>
                        <span class="text-gray-900"><?= $category['id'] ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Products</span>
                        <span class="text-gray-900"><?= $category['product_count'] ?? 0 ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Created</span>
                        <span class="text-gray-900"><?= $category['created_at'] ?? '—' ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Submit -->
    <div class="flex items-center justify-between bg-white rounded-xl border border-gray-200 p-4 sticky bottom-4">
        <a href="/admin/categories" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
        <div class="flex gap-3">
            <button type="submit" name="action" value="save_continue"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                Save & Continue
            </button>
            <button type="submit" name="action" value="save"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Save Category
            </button>
        </div>
    </div>
</form>
