<?php /** @var array $checks */ /** @var bool $allPassed */ ?>
<?php ob_start(); ?>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-8 text-center border-b border-gray-100">
        <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Welcome to StructBrew</h2>
        <p class="text-gray-500 max-w-md mx-auto">
            This wizard will guide you through the installation process.
            Let's first check that your server meets all the requirements.
        </p>
    </div>

    <div class="px-6 py-6">
        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">System Requirements</h3>
        <div class="space-y-3">
            <?php foreach ($checks as $check): ?>
            <div class="flex items-center justify-between py-2 px-4 rounded-lg <?= $check['passed'] ? 'bg-green-50' : 'bg-red-50' ?>">
                <div>
                    <span class="font-medium text-sm <?= $check['passed'] ? 'text-green-900' : 'text-red-900' ?>">
                        <?= htmlspecialchars($check['name']) ?>
                    </span>
                    <span class="text-xs <?= $check['passed'] ? 'text-green-600' : 'text-red-600' ?> ml-2">
                        (Required: <?= htmlspecialchars($check['required']) ?>)
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm <?= $check['passed'] ? 'text-green-700' : 'text-red-700' ?>">
                        <?= htmlspecialchars($check['current']) ?>
                    </span>
                    <?php if ($check['passed']): ?>
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    <?php else: ?>
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
        <?php if ($allPassed): ?>
            <a href="/setup/database"
               class="inline-flex items-center gap-2 px-6 py-2.5 bg-accent hover:bg-accent-hover text-white text-sm font-semibold rounded-lg transition">
                Continue
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        <?php else: ?>
            <div class="flex items-center gap-3">
                <span class="text-sm text-red-600">Please fix the requirements above before continuing.</span>
                <a href="/setup"
                   class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-200 text-gray-600 text-sm font-semibold rounded-lg hover:bg-gray-300 transition">
                    Re-check
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include __DIR__ . '/layout.php'; ?>
