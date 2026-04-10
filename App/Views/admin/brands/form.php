<form method="POST" action="<?= $formAction ?? '/admin/brands' ?>" enctype="multipart/form-data" class="space-y-6">
    <input type="hidden" name="_csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Brand Information</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required value="<?= htmlspecialchars($brand['name'] ?? '') ?>"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                            <input type="text" name="slug" value="<?= htmlspecialchars($brand['slug'] ?? '') ?>"
                                   placeholder="auto-generated from name"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Website URL</label>
                        <input type="url" name="website_url" value="<?= htmlspecialchars($brand['website_url'] ?? '') ?>"
                               placeholder="https://www.example.com"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Logo Upload -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Logo</h3>
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-400 transition-colors">
                    <input type="file" name="logo" accept="image/*" class="hidden" id="brand-logo">
                    <label for="brand-logo" class="cursor-pointer">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm text-gray-500">Drop logo here or <span class="text-blue-600 font-medium">browse</span></p>
                        <p class="text-xs text-gray-400 mt-1">PNG, SVG recommended (max 2MB)</p>
                    </label>
                </div>
                <?php if (!empty($brand['logo'])): ?>
                <div class="mt-4 flex items-center gap-4">
                    <img src="<?= htmlspecialchars($brand['logo']) ?>" alt="" class="w-20 h-20 rounded-lg object-contain bg-gray-50 p-2">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remove_logo" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="text-sm text-gray-600">Remove current logo</span>
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
                <div class="space-y-4">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" value="1" <?= ($brand['is_active'] ?? 1) ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Active</span>
                    </label>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" value="<?= htmlspecialchars($brand['sort_order'] ?? 0) ?>"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Info -->
            <?php if (!empty($brand['id'])): ?>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Information</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">ID</span>
                        <span class="text-gray-900"><?= (int) $brand['id'] ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Products</span>
                        <span class="text-gray-900"><?= $brand['product_count'] ?? 0 ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Created</span>
                        <span class="text-gray-900"><?= $brand['created_at'] ?? '—' ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Submit -->
    <div class="flex items-center justify-between bg-white rounded-xl border border-gray-200 p-4 sticky bottom-4">
        <a href="/admin/brands" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
        <div class="flex gap-3">
            <button type="submit" name="action" value="save_continue"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                Save & Continue
            </button>
            <button type="submit" name="action" value="save"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Save Brand
            </button>
        </div>
    </div>
</form>
