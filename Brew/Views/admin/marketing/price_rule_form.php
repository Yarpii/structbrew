<form method="POST" action="<?= $formAction ?? '/admin/marketing/price-rules' ?>" class="space-y-6">
    <input type="hidden" name="_token" value="<?= \Brew\Core\Session::csrfToken() ?>">

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="xl:col-span-2 space-y-6">
            <!-- General Info -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Rule Information</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required value="<?= htmlspecialchars($rule['name'] ?? '') ?>"
                               placeholder="e.g. Summer Sale 20%"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3"
                                  class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($rule['description'] ?? '') ?></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                            <select name="type" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="percentage" <?= ($rule['type'] ?? '') === 'percentage' ? 'selected' : '' ?>>Percentage Discount</option>
                                <option value="fixed" <?= ($rule['type'] ?? '') === 'fixed' ? 'selected' : '' ?>>Fixed Amount</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Value <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" name="value" required value="<?= htmlspecialchars($rule['value'] ?? '') ?>"
                                   placeholder="e.g. 10"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Order Total</label>
                        <input type="number" step="0.01" name="min_order_total" value="<?= htmlspecialchars($rule['min_order_total'] ?? '') ?>"
                               placeholder="0.00"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Date Range -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Schedule</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Starts At</label>
                        <input type="datetime-local" name="starts_at" value="<?= htmlspecialchars($rule['starts_at'] ?? '') ?>"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Expires At</label>
                        <input type="datetime-local" name="expires_at" value="<?= htmlspecialchars($rule['expires_at'] ?? '') ?>"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Store Views -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Store Views</h3>
                <p class="text-xs text-gray-500 mb-3">Select which store views this rule applies to. Leave empty for all.</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    <?php foreach ($storeViews ?? [] as $sv): ?>
                    <label class="flex items-center gap-2 p-3 rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50/50 transition-colors cursor-pointer">
                        <input type="checkbox" name="store_view_ids[]" value="<?= $sv['id'] ?>"
                               <?= in_array($sv['id'], $selectedStoreViews ?? []) ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <div>
                            <span class="text-sm text-gray-700"><?= htmlspecialchars($sv['name']) ?></span>
                            <span class="block text-[10px] text-gray-400"><?= $sv['locale'] ?> / <?= $sv['currency_code'] ?></span>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Status</h3>
                <div class="space-y-4">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" value="1" <?= ($rule['is_active'] ?? 1) ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Active</span>
                    </label>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Usage Limit</label>
                        <input type="number" name="usage_limit" value="<?= htmlspecialchars($rule['usage_limit'] ?? '') ?>"
                               placeholder="Unlimited"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-400 mt-1">Leave empty for unlimited usage</p>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <?php if (!empty($rule['id'])): ?>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Statistics</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">ID</span>
                        <span class="text-gray-900"><?= $rule['id'] ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Times Used</span>
                        <span class="text-gray-900"><?= $rule['times_used'] ?? 0 ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Coupons</span>
                        <span class="text-gray-900"><?= $rule['coupon_count'] ?? 0 ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Created</span>
                        <span class="text-gray-900"><?= $rule['created_at'] ?? '—' ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Submit -->
    <div class="flex items-center justify-between bg-white rounded-xl border border-gray-200 p-4 sticky bottom-4">
        <a href="/admin/marketing/price-rules" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
        <div class="flex gap-3">
            <button type="submit" name="action" value="save_continue"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                Save & Continue
            </button>
            <button type="submit" name="action" value="save"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Save Price Rule
            </button>
        </div>
    </div>
</form>
