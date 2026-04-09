<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">Manage your multi-store website hierarchy</p>
    <button onclick="document.getElementById('add-website-form').classList.toggle('hidden')"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Website
    </button>
</div>

<!-- Add Website Form -->
<div id="add-website-form" class="hidden bg-white rounded-xl border border-gray-200 p-6 mb-6">
    <h3 class="font-semibold text-gray-800 mb-4">New Website</h3>
    <form method="POST" action="/admin/stores/websites" class="space-y-4">
        <input type="hidden" name="_csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                <input type="text" name="code" required placeholder="europe" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" required placeholder="Europe" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                <input type="number" name="sort_order" value="0" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
            </div>
        </div>
        <div class="flex items-center gap-4">
            <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-blue-600"> <span class="text-sm">Active</span></label>
            <label class="flex items-center gap-2"><input type="checkbox" name="is_default" value="1" class="rounded border-gray-300 text-blue-600"> <span class="text-sm">Default</span></label>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">Create Website</button>
    </form>
</div>

<!-- Websites -->
<?php foreach ($websites ?? [] as $website): ?>
<div class="bg-white rounded-xl border border-gray-200 mb-4">
    <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900"><?= htmlspecialchars($website['name']) ?></h3>
                <p class="text-xs text-gray-400 font-mono"><?= htmlspecialchars($website['code']) ?></p>
            </div>
            <?php if ($website['is_default']): ?>
            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full font-medium">Default</span>
            <?php endif; ?>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full <?= $website['is_active'] ? 'bg-green-500' : 'bg-gray-300' ?>"></span>
        </div>
    </div>

    <!-- Stores under this website -->
    <div class="px-6 py-3">
        <?php if (!empty($website['stores'])): ?>
            <?php foreach ($website['stores'] as $store): ?>
            <div class="ml-6 py-2 border-l-2 border-gray-200 pl-4 mb-2">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($store['name']) ?> <span class="text-gray-400 font-mono text-xs">(<?= htmlspecialchars($store['code'] ?? '') ?>)</span></p>
                    </div>
                </div>
                <!-- Store Views under this store -->
                <?php if (!empty($store['views'])): ?>
                    <?php foreach ($store['views'] as $view): ?>
                    <div class="ml-6 py-1 border-l-2 border-gray-100 pl-4 mt-1">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <p class="text-sm text-gray-600"><?= htmlspecialchars($view['name']) ?></p>
                                <span class="text-xs text-gray-400"><?= htmlspecialchars($view['locale'] ?? '') ?> / <?= htmlspecialchars($view['currency_code'] ?? '') ?></span>
                                <?php if ($view['is_default']): ?>
                                <span class="px-1.5 py-0.5 bg-green-100 text-green-700 text-[10px] rounded">Default</span>
                                <?php endif; ?>
                            </div>
                            <span class="w-2 h-2 rounded-full <?= $view['is_active'] ? 'bg-green-500' : 'bg-gray-300' ?>"></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-sm text-gray-400 py-2">No stores configured</p>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>

<?php if (empty($websites)): ?>
<div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/></svg>
    <p class="text-gray-500">No websites configured yet</p>
    <p class="text-sm text-gray-400 mt-1">Create your first website to get started with multi-store setup</p>
</div>
<?php endif; ?>
