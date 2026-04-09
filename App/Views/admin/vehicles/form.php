<form method="POST" action="<?= $formAction ?? '/admin/vehicles' ?>" class="space-y-6">
    <input type="hidden" name="_csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Vehicle Information</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Brand <span class="text-red-500">*</span></label>
                            <select name="brand_id" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Brand</option>
                                <?php foreach ($vehicleBrands ?? [] as $vb): ?>
                                <option value="<?= $vb['id'] ?>" <?= ($vehicle['brand_id'] ?? '') == $vb['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($vb['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Model <span class="text-red-500">*</span></label>
                            <input type="text" name="model" required value="<?= htmlspecialchars($vehicle['model'] ?? '') ?>"
                                   placeholder="e.g. Civic, Corolla"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Year From</label>
                            <input type="number" name="year_from" value="<?= htmlspecialchars($vehicle['year_from'] ?? '') ?>"
                                   placeholder="2010" min="1900" max="2100"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Year To</label>
                            <input type="number" name="year_to" value="<?= htmlspecialchars($vehicle['year_to'] ?? '') ?>"
                                   placeholder="2024" min="1900" max="2100"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Engine CC</label>
                            <input type="number" name="engine_cc" value="<?= htmlspecialchars($vehicle['engine_cc'] ?? '') ?>"
                                   placeholder="1600"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                        <input type="text" name="slug" value="<?= htmlspecialchars($vehicle['slug'] ?? '') ?>"
                               placeholder="auto-generated from brand and model"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Status</h3>
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" value="1" <?= ($vehicle['is_active'] ?? 1) ? 'checked' : '' ?>
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Active</span>
                </label>
            </div>

            <!-- Info -->
            <?php if (!empty($vehicle['id'])): ?>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Information</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">ID</span>
                        <span class="text-gray-900"><?= $vehicle['id'] ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Compatible Products</span>
                        <span class="text-gray-900"><?= $vehicle['product_count'] ?? 0 ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Created</span>
                        <span class="text-gray-900"><?= $vehicle['created_at'] ?? '—' ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Submit -->
    <div class="flex items-center justify-between bg-white rounded-xl border border-gray-200 p-4 sticky bottom-4">
        <a href="/admin/vehicles" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
        <div class="flex gap-3">
            <button type="submit" name="action" value="save_continue"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                Save & Continue
            </button>
            <button type="submit" name="action" value="save"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Save Vehicle
            </button>
        </div>
    </div>
</form>
