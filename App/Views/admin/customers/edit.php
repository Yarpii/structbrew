<div class="mb-6">
    <a href="/admin/customers/<?= (int) $customer['id'] ?>" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Customer
    </a>
</div>

<div class="max-w-3xl bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Edit Customer</h2>
        <p class="text-sm text-gray-500 mt-1">Update profile, status, and account type.</p>
    </div>

    <form method="POST" action="/admin/customers/<?= (int) $customer['id'] ?>" class="p-6 space-y-5">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                <input type="text" name="first_name" value="<?= htmlspecialchars((string) ($customer['first_name'] ?? '')) ?>" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                <input type="text" name="last_name" value="<?= htmlspecialchars((string) ($customer['last_name'] ?? '')) ?>" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars((string) ($customer['email'] ?? '')) ?>" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars((string) ($customer['phone'] ?? '')) ?>" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <?php if (!empty($supportsCustomerGroups) && !empty($customerGroups)): ?>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Customer Group</label>
                <select name="customer_group" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <?php foreach ($customerGroups as $groupKey => $group): ?>
                        <option value="<?= htmlspecialchars((string) $groupKey) ?>" <?= (($customer['customer_group'] ?? 'retail') === $groupKey) ? 'selected' : '' ?>><?= htmlspecialchars((string) $group['label']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                <input type="date" name="date_of_birth" value="<?= htmlspecialchars((string) ($customer['date_of_birth'] ?? '')) ?>" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                <select name="gender" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Not set</option>
                    <option value="male" <?= ($customer['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Male</option>
                    <option value="female" <?= ($customer['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                    <option value="other" <?= ($customer['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
            <input type="password" name="password" placeholder="Leave empty to keep the current password" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>

        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
            <input type="checkbox" name="is_active" value="1" <?= !empty($customer['is_active']) ? 'checked' : '' ?> class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span>Customer account is active</span>
        </label>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="/admin/customers/<?= (int) $customer['id'] ?>" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Cancel</a>
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Save Customer
            </button>
        </div>
    </form>
</div>
