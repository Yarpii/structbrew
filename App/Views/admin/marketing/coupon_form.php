<form method="POST" action="<?= $formAction ?? '/admin/marketing/coupons' ?>" class="space-y-6">
    <input type="hidden" name="_csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="xl:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Coupon Information</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price Rule <span class="text-red-500">*</span></label>
                        <select name="price_rule_id" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Price Rule</option>
                            <?php foreach ($priceRules ?? [] as $pr): ?>
                            <option value="<?= $pr['id'] ?>" <?= ($coupon['price_rule_id'] ?? '') == $pr['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($pr['name']) ?>
                                (<?= $pr['type'] === 'percentage' ? $pr['value'] . '%' : number_format((float)$pr['value'], 2) ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Coupon Code <span class="text-red-500">*</span></label>
                        <div class="flex gap-2" x-data="{ generating: false }">
                            <input type="text" name="code" required id="coupon-code" value="<?= htmlspecialchars($coupon['code'] ?? '') ?>"
                                   placeholder="e.g. SUMMER2024"
                                   class="flex-1 px-3 py-2 rounded-lg border border-gray-300 text-sm font-mono uppercase focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <button type="button" @click="
                                const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                                let code = '';
                                for (let i = 0; i < 8; i++) code += chars.charAt(Math.floor(Math.random() * chars.length));
                                document.getElementById('coupon-code').value = code;
                            " class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors whitespace-nowrap">
                                Generate Code
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Usage Limit (Total)</label>
                            <input type="number" name="usage_limit" value="<?= htmlspecialchars($coupon['usage_limit'] ?? '') ?>"
                                   placeholder="Unlimited"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-400 mt-1">Leave empty for unlimited</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Usage Per Customer</label>
                            <input type="number" name="usage_per_customer" value="<?= htmlspecialchars($coupon['usage_per_customer'] ?? '') ?>"
                                   placeholder="Unlimited"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-400 mt-1">Leave empty for unlimited</p>
                        </div>
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
                    <input type="checkbox" name="is_active" value="1" <?= ($coupon['is_active'] ?? 1) ? 'checked' : '' ?>
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Active</span>
                </label>
            </div>

            <!-- Stats -->
            <?php if (!empty($coupon['id'])): ?>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Statistics</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">ID</span>
                        <span class="text-gray-900"><?= $coupon['id'] ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Times Used</span>
                        <span class="text-gray-900 font-medium"><?= $coupon['times_used'] ?? 0 ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Created</span>
                        <span class="text-gray-900"><?= $coupon['created_at'] ?? '—' ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Submit -->
    <div class="flex items-center justify-between bg-white rounded-xl border border-gray-200 p-4 sticky bottom-4">
        <a href="/admin/marketing/coupons" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
        <div class="flex gap-3">
            <button type="submit" name="action" value="save_continue"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                Save & Continue
            </button>
            <button type="submit" name="action" value="save"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Save Coupon
            </button>
        </div>
    </div>
</form>
