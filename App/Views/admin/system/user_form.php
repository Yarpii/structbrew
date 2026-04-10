<form method="POST" action="<?= $formAction ?? '/admin/system/users' ?>" class="space-y-6">
    <input type="hidden" name="_csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="xl:col-span-2 space-y-6">
            <!-- Account Info -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Account Information</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                            <input type="text" name="first_name" required value="<?= htmlspecialchars($user['first_name'] ?? '') ?>"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                            <input type="text" name="last_name" required value="<?= htmlspecialchars($user['last_name'] ?? '') ?>"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" required value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Password
                            <?php if (empty($user['id'])): ?><span class="text-red-500">*</span><?php endif; ?>
                        </label>
                        <input type="password" name="password" <?= empty($user['id']) ? 'required' : '' ?>
                               placeholder="<?= !empty($user['id']) ? 'Leave blank to keep current password' : 'Enter password' ?>"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <?php if (!empty($user['id'])): ?>
                        <p class="text-xs text-gray-400 mt-1">Leave blank to keep the current password</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Role & Permissions -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Role & Permissions</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                        <select name="role_id" required class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Role</option>
                            <?php foreach ($roles ?? [] as $role): ?>
                            <option value="<?= (int) $role['id'] ?>" <?= ($user['role_id'] ?? '') == $role['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($role['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <label class="flex items-center gap-3">
                            <input type="checkbox" name="is_superadmin" value="1" <?= ($user['is_superadmin'] ?? 0) ? 'checked' : '' ?>
                                   class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                            <div>
                                <span class="text-sm font-medium text-yellow-800">Super Admin</span>
                                <p class="text-xs text-yellow-600 mt-0.5">Super admins bypass all permission checks and have full system access</p>
                            </div>
                        </label>
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
                    <input type="checkbox" name="is_active" value="1" <?= ($user['is_active'] ?? 1) ? 'checked' : '' ?>
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-gray-700">Active</span>
                </label>
            </div>

            <!-- Info -->
            <?php if (!empty($user['id'])): ?>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Information</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">ID</span>
                        <span class="text-gray-900"><?= (int) $user['id'] ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Last Login</span>
                        <span class="text-gray-900"><?= $user['last_login_at'] ?? 'Never' ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Created</span>
                        <span class="text-gray-900"><?= $user['created_at'] ?? '—' ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Submit -->
    <div class="flex items-center justify-between bg-white rounded-xl border border-gray-200 p-4 sticky bottom-4">
        <a href="/admin/system/users" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
        <div class="flex gap-3">
            <button type="submit" name="action" value="save_continue"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                Save & Continue
            </button>
            <button type="submit" name="action" value="save"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Save User
            </button>
        </div>
    </div>
</form>
