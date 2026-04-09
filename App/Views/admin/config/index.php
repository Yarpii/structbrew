<!-- Header -->
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">Manage system configuration settings</p>
</div>

<!-- Scope Selector -->
<div class="bg-white rounded-xl border border-gray-200 mb-6 p-4" x-data="{ scope: '<?= htmlspecialchars($_GET['scope'] ?? 'global') ?>', scopeId: '<?= htmlspecialchars($_GET['scope_id'] ?? '0') ?>' }">
    <form method="GET" action="/admin/config" class="flex flex-wrap gap-4 items-end">
        <div class="w-48">
            <label class="block text-xs font-medium text-gray-500 mb-1">Configuration Scope</label>
            <select name="scope" x-model="scope"
                    class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="global">Global</option>
                <option value="website">Website</option>
                <option value="store">Store</option>
                <option value="store_view">Store View</option>
            </select>
        </div>

        <div class="w-64" x-show="scope === 'website'" x-cloak>
            <label class="block text-xs font-medium text-gray-500 mb-1">Website</label>
            <select name="scope_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <?php foreach ($websites ?? [] as $ws): ?>
                <option value="<?= $ws['id'] ?>" <?= ($_GET['scope_id'] ?? '') == $ws['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($ws['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="w-64" x-show="scope === 'store'" x-cloak>
            <label class="block text-xs font-medium text-gray-500 mb-1">Store</label>
            <select name="scope_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <?php foreach ($stores ?? [] as $st): ?>
                <option value="<?= $st['id'] ?>" <?= ($_GET['scope_id'] ?? '') == $st['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($st['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="w-64" x-show="scope === 'store_view'" x-cloak>
            <label class="block text-xs font-medium text-gray-500 mb-1">Store View</label>
            <select name="scope_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <?php foreach ($storeViews ?? [] as $sv): ?>
                <option value="<?= $sv['id'] ?>" <?= ($_GET['scope_id'] ?? '') == $sv['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sv['name']) ?> (<?= $sv['locale'] ?>)
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
            Switch Scope
        </button>
    </form>
</div>

<!-- Configuration Sections -->
<?php
$sections = [
    'general' => ['label' => 'General', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
    'tax' => ['label' => 'Tax', 'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z'],
    'shipping' => ['label' => 'Shipping', 'icon' => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0'],
    'payment' => ['label' => 'Payment', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
    'currency' => ['label' => 'Currency', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
];
?>

<div class="space-y-6" x-data="{ openSection: '<?= $_GET['section'] ?? 'general' ?>' }">
    <?php foreach ($sections as $sectionKey => $section): ?>
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <!-- Section Header -->
        <button @click="openSection = openSection === '<?= $sectionKey ?>' ? '' : '<?= $sectionKey ?>'"
                class="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $section['icon'] ?>"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-800"><?= $section['label'] ?></h3>
            </div>
            <svg class="w-5 h-5 text-gray-400 transition-transform" :class="openSection === '<?= $sectionKey ?>' && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <!-- Section Content -->
        <div x-show="openSection === '<?= $sectionKey ?>'" x-cloak class="border-t border-gray-200">
            <?php
            $sectionSettings = array_filter($settings ?? [], fn($s) => ($s['group'] ?? '') === $sectionKey);
            if (!empty($sectionSettings)):
            ?>
                <?php foreach ($sectionSettings as $setting): ?>
                <form method="POST" action="/admin/config" class="px-6 py-4 flex items-center gap-4 border-b border-gray-50 last:border-0 hover:bg-gray-50/50">
                    <input type="hidden" name="_token" value="<?= \App\Core\Session::csrfToken() ?>">
                    <input type="hidden" name="scope" value="<?= htmlspecialchars($_GET['scope'] ?? 'global') ?>">
                    <input type="hidden" name="scope_id" value="<?= htmlspecialchars($_GET['scope_id'] ?? '0') ?>">
                    <input type="hidden" name="path" value="<?= htmlspecialchars($setting['path']) ?>">

                    <div class="w-1/3">
                        <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($setting['label'] ?? $setting['path']) ?></p>
                        <p class="text-xs text-gray-400 font-mono mt-0.5"><?= htmlspecialchars($setting['path']) ?></p>
                    </div>

                    <div class="flex-1">
                        <?php if (($setting['type'] ?? 'text') === 'select'): ?>
                            <select name="value" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <?php foreach ($setting['options'] ?? [] as $optVal => $optLabel): ?>
                                <option value="<?= htmlspecialchars($optVal) ?>" <?= ($setting['value'] ?? '') == $optVal ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($optLabel) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        <?php elseif (($setting['type'] ?? 'text') === 'textarea'): ?>
                            <textarea name="value" rows="2"
                                      class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($setting['value'] ?? '') ?></textarea>
                        <?php elseif (($setting['type'] ?? 'text') === 'boolean'): ?>
                            <select name="value" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="0" <?= empty($setting['value']) ? 'selected' : '' ?>>No</option>
                                <option value="1" <?= !empty($setting['value']) ? 'selected' : '' ?>>Yes</option>
                            </select>
                        <?php else: ?>
                            <input type="text" name="value" value="<?= htmlspecialchars($setting['value'] ?? '') ?>"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center gap-2">
                        <?php if (!empty($setting['is_overridden'])): ?>
                            <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-[10px] rounded font-medium">Overridden</span>
                        <?php endif; ?>
                        <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors">
                            Save
                        </button>
                    </div>
                </form>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="px-6 py-8 text-center text-gray-400 text-sm">No settings configured for this section</div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
