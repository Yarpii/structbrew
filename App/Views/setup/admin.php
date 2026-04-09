<?php /** @var string|null $error */ /** @var string $csrfToken */ ?>
<?php ob_start(); ?>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-6 border-b border-gray-100">
        <h2 class="text-xl font-bold text-gray-900">Create Admin Account</h2>
        <p class="text-sm text-gray-500 mt-1">Set up your administrator account to manage the store.</p>
    </div>

    <?php if (!empty($error)): ?>
    <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
        <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm text-red-700"><?= htmlspecialchars($error) ?></p>
    </div>
    <?php endif; ?>

    <form method="POST" action="/setup/install" class="px-6 py-6 space-y-5">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5" for="admin_first_name">First Name <span class="text-red-500">*</span></label>
                <input type="text" id="admin_first_name" name="admin_first_name" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-accent/20 focus:border-accent"
                       placeholder="John">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5" for="admin_last_name">Last Name</label>
                <input type="text" id="admin_last_name" name="admin_last_name"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-accent/20 focus:border-accent"
                       placeholder="Doe">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5" for="admin_email">Email Address <span class="text-red-500">*</span></label>
            <input type="email" id="admin_email" name="admin_email" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-accent/20 focus:border-accent"
                   placeholder="admin@yourdomain.com">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5" for="admin_password">Password <span class="text-red-500">*</span></label>
                <input type="password" id="admin_password" name="admin_password" required minlength="8"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-accent/20 focus:border-accent"
                       placeholder="Min. 8 characters">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5" for="admin_password_confirm">Confirm Password <span class="text-red-500">*</span></label>
                <input type="password" id="admin_password_confirm" name="admin_password_confirm" required minlength="8"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-accent/20 focus:border-accent"
                       placeholder="Repeat password">
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm text-blue-800">
                    <p class="font-medium">What happens next?</p>
                    <p class="mt-1 text-blue-700">Clicking "Install" will write your <code class="bg-blue-100 px-1 py-0.5 rounded text-xs">.env</code> configuration file, run all database migrations, seed the database with initial data, and create your admin account.</p>
                </div>
            </div>
        </div>

        <div class="pt-2 flex items-center justify-between border-t border-gray-100">
            <a href="/setup/application"
               class="text-sm text-gray-500 hover:text-gray-700 transition flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-accent hover:bg-accent-hover text-white text-sm font-semibold rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Install StructBrew
            </button>
        </div>
    </form>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/layout.php'; ?>
