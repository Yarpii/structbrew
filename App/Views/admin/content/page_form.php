<form method="POST" action="<?= $formAction ?? '/admin/content/pages' ?>" class="space-y-6">
    <input type="hidden" name="_csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="xl:col-span-2 space-y-6">
            <!-- General Info -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Page Settings</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-400">/</span>
                            <input type="text" name="slug" required value="<?= htmlspecialchars($page['slug'] ?? '') ?>"
                                   placeholder="about-us"
                                   class="flex-1 px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Translations (per store view) -->
            <div class="bg-white rounded-xl border border-gray-200 p-6" x-data="{ activeTab: '<?= $storeViews[0]['id'] ?? 0 ?>' }">
                <h3 class="font-semibold text-gray-800 mb-4">Page Content (per Store View)</h3>

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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="translations[<?= $sv['id'] ?>][title]"
                               value="<?= htmlspecialchars($trans['title'] ?? '') ?>"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                        <textarea name="translations[<?= $sv['id'] ?>][content]" rows="12"
                                  class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono"><?= htmlspecialchars($trans['content'] ?? '') ?></textarea>
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
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Status</h3>
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" value="1" <?= ($page['is_active'] ?? 1) ? 'checked' : '' ?>
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Active</span>
                </label>
            </div>

            <!-- Info -->
            <?php if (!empty($page['id'])): ?>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Information</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">ID</span>
                        <span class="text-gray-900"><?= $page['id'] ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Created</span>
                        <span class="text-gray-900"><?= $page['created_at'] ?? '—' ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Updated</span>
                        <span class="text-gray-900"><?= $page['updated_at'] ?? '—' ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Submit -->
    <div class="flex items-center justify-between bg-white rounded-xl border border-gray-200 p-4 sticky bottom-4">
        <a href="/admin/content/pages" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
        <div class="flex gap-3">
            <button type="submit" name="action" value="save_continue"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                Save & Continue
            </button>
            <button type="submit" name="action" value="save"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Save Page
            </button>
        </div>
    </div>
</form>
