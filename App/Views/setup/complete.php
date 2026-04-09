<?php /** @var string $adminEmail */ /** @var int $migrationCount */ ?>
<?php ob_start(); ?>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-10 text-center border-b border-gray-100">
        <div class="w-16 h-16 bg-green-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Installation Complete!</h2>
        <p class="text-gray-500 max-w-md mx-auto">
            StructBrew has been successfully installed and is ready to use.
        </p>
    </div>

    <div class="px-6 py-6 space-y-5">
        <!-- Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-sm text-gray-500">Migrations executed</div>
                <div class="text-2xl font-bold text-gray-900"><?= $migrationCount ?></div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-sm text-gray-500">Admin email</div>
                <div class="text-sm font-semibold text-gray-900 mt-1"><?= htmlspecialchars($adminEmail) ?></div>
            </div>
        </div>

        <!-- Security Warning -->
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-5">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <div>
                    <h3 class="font-semibold text-amber-800">Important: Remove Setup Files</h3>
                    <p class="text-sm text-amber-700 mt-1">
                        For security, you should remove the setup wizard files now that the installation is complete.
                        Delete the following files and directories from your server:
                    </p>
                    <div class="mt-3 bg-amber-100 rounded-lg p-3 font-mono text-xs text-amber-900 space-y-1">
                        <div>App/Controllers/SetupController.php</div>
                        <div>App/Views/setup/  <span class="text-amber-600">(entire directory)</span></div>
                        <div>App/Routes/setup.php</div>
                    </div>
                    <p class="text-sm text-amber-700 mt-3">
                        Or run from your project root:
                    </p>
                    <div class="mt-2 bg-amber-100 rounded-lg p-3 font-mono text-xs text-amber-900">
                        rm -rf App/Controllers/SetupController.php App/Views/setup App/Routes/setup.php
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="border border-gray-200 rounded-lg divide-y divide-gray-200">
            <a href="/" class="flex items-center justify-between px-5 py-4 hover:bg-gray-50 transition group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-900">Visit Storefront</div>
                        <div class="text-xs text-gray-500">See your store as customers will</div>
                    </div>
                </div>
                <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            <a href="/admin" class="flex items-center justify-between px-5 py-4 hover:bg-gray-50 transition group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-900">Open Admin Panel</div>
                        <div class="text-xs text-gray-500">Manage products, orders, and settings</div>
                    </div>
                </div>
                <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/layout.php'; ?>
