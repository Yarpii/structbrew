<?php /** @var array $db */ /** @var string|null $error */ /** @var string $csrfToken */ ?>
<?php ob_start(); ?>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-6 border-b border-gray-100">
        <h2 class="text-xl font-bold text-gray-900">Database Configuration</h2>
        <p class="text-sm text-gray-500 mt-1">Enter your MySQL database credentials. The database will be created automatically if it doesn't exist.</p>
    </div>

    <?php if (!empty($error)): ?>
    <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
        <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm text-red-700"><?= htmlspecialchars($error) ?></p>
    </div>
    <?php endif; ?>

    <form method="POST" action="/setup/database" class="px-6 py-6 space-y-5">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5" for="db_host">Database Host</label>
                <input type="text" id="db_host" name="db_host" value="<?= htmlspecialchars($db['host']) ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-accent/20 focus:border-accent"
                       placeholder="127.0.0.1">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5" for="db_port">Port</label>
                <input type="text" id="db_port" name="db_port" value="<?= htmlspecialchars($db['port']) ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-accent/20 focus:border-accent"
                       placeholder="3306">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5" for="db_name">Database Name</label>
            <input type="text" id="db_name" name="db_name" value="<?= htmlspecialchars($db['name']) ?>"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-accent/20 focus:border-accent"
                   placeholder="structbrew">
            <p class="text-xs text-gray-400 mt-1">Will be created automatically if it doesn't exist.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5" for="db_user">Username</label>
                <input type="text" id="db_user" name="db_user" value="<?= htmlspecialchars($db['user']) ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-accent/20 focus:border-accent"
                       placeholder="root">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5" for="db_pass">Password</label>
                <input type="password" id="db_pass" name="db_pass" value="<?= htmlspecialchars($db['pass']) ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-accent/20 focus:border-accent"
                       placeholder="Enter password">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5" for="db_prefix">Table Prefix <span class="text-gray-400 font-normal">(optional)</span></label>
            <input type="text" id="db_prefix" name="db_prefix" value="<?= htmlspecialchars($db['prefix']) ?>"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-accent/20 focus:border-accent"
                   placeholder="e.g. sb_">
        </div>

        <div class="pt-2 flex items-center justify-between border-t border-gray-100">
            <a href="/setup"
               class="text-sm text-gray-500 hover:text-gray-700 transition flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-accent hover:bg-accent-hover text-white text-sm font-semibold rounded-lg transition">
                Test Connection & Continue
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </form>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/layout.php'; ?>
