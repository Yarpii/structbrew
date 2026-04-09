<?php /** @var array $app */ /** @var array $timezones */ /** @var string|null $error */ /** @var string $csrfToken */ ?>
<?php ob_start(); ?>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-6 border-b border-gray-100">
        <h2 class="text-xl font-bold text-gray-900">Application Settings</h2>
        <p class="text-sm text-gray-500 mt-1">Configure your store name, URL, and mail settings.</p>
    </div>

    <?php if (!empty($error)): ?>
    <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start gap-3">
        <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-sm text-red-700"><?= htmlspecialchars($error) ?></p>
    </div>
    <?php endif; ?>

    <form method="POST" action="/setup/application" class="px-6 py-6 space-y-6">
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrfToken) ?>">

        <!-- General -->
        <div>
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">General</h3>
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5" for="app_name">Application Name</label>
                    <input type="text" id="app_name" name="app_name" value="<?= htmlspecialchars($app['name']) ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-accent/20 focus:border-accent"
                           placeholder="StructBrew">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5" for="app_url">Application URL</label>
                    <input type="text" id="app_url" name="app_url" value="<?= htmlspecialchars($app['url']) ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-accent/20 focus:border-accent"
                           placeholder="https://yourdomain.com">
                    <p class="text-xs text-gray-400 mt-1">No trailing slash. Example: https://mystore.com</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5" for="app_timezone">Timezone</label>
                        <select id="app_timezone" name="app_timezone"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-accent/20 focus:border-accent bg-white">
                            <?php foreach ($timezones as $tz => $label): ?>
                                <option value="<?= htmlspecialchars($tz) ?>" <?= $app['timezone'] === $tz ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5" for="app_debug">Debug Mode</label>
                        <select id="app_debug" name="app_debug"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-accent/20 focus:border-accent bg-white">
                            <option value="true" <?= $app['debug'] === 'true' ? 'selected' : '' ?>>Enabled (development)</option>
                            <option value="false" <?= $app['debug'] === 'false' ? 'selected' : '' ?>>Disabled (production)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mail -->
        <div class="pt-2">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Mail Settings <span class="text-gray-400 font-normal normal-case">(optional - can be configured later)</span></h3>
            <div class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5" for="mail_host">SMTP Host</label>
                        <input type="text" id="mail_host" name="mail_host" value="<?= htmlspecialchars($app['mail_host']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-accent/20 focus:border-accent"
                               placeholder="smtp.example.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5" for="mail_port">SMTP Port</label>
                        <input type="text" id="mail_port" name="mail_port" value="<?= htmlspecialchars($app['mail_port']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-accent/20 focus:border-accent"
                               placeholder="587">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5" for="mail_username">SMTP Username</label>
                        <input type="text" id="mail_username" name="mail_username" value="<?= htmlspecialchars($app['mail_username']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-accent/20 focus:border-accent"
                               placeholder="user@example.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5" for="mail_password">SMTP Password</label>
                        <input type="password" id="mail_password" name="mail_password" value="<?= htmlspecialchars($app['mail_password']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-accent/20 focus:border-accent"
                               placeholder="Enter password">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5" for="mail_from_address">From Address</label>
                        <input type="email" id="mail_from_address" name="mail_from_address" value="<?= htmlspecialchars($app['mail_from_address']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-accent/20 focus:border-accent"
                               placeholder="noreply@yourstore.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5" for="mail_from_name">From Name</label>
                        <input type="text" id="mail_from_name" name="mail_from_name" value="<?= htmlspecialchars($app['mail_from_name']) ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-accent/20 focus:border-accent"
                               placeholder="StructBrew">
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-2 flex items-center justify-between border-t border-gray-100">
            <a href="/setup/database"
               class="text-sm text-gray-500 hover:text-gray-700 transition flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-accent hover:bg-accent-hover text-white text-sm font-semibold rounded-lg transition">
                Continue
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </form>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/layout.php'; ?>
