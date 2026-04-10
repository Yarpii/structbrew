<div class="bg-white border border-gray-200 rounded-lg overflow-hidden" x-data="{ activeTab: '<?= (int) ($storeViews[0]['id'] ?? 0) ?>' }">
    <div class="px-4 py-2 border-b border-gray-100 bg-gray-50/70 flex items-center justify-between gap-3">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 shrink-0">Content &amp; Pricing</p>
        <div class="flex gap-1 flex-wrap justify-end">
            <?php foreach ($storeViews ?? [] as $sv): ?>
            <button type="button"
                    @click="activeTab = '<?= (int) $sv['id'] ?>'"
                    :class="activeTab === '<?= (int) $sv['id'] ?>' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'"
                    class="px-2.5 py-0.5 text-xs font-medium rounded transition-colors">
                <?= htmlspecialchars($sv['locale'] ?? $sv['name']) ?>
            </button>
            <?php endforeach; ?>
        </div>
    </div>
    <?php foreach ($storeViews ?? [] as $sv): $trans = $translations[$sv['id']] ?? []; $pricing = $pricingData[$sv['id']] ?? []; ?>
    <div x-show="activeTab === '<?= (int) $sv['id'] ?>'" x-cloak class="p-4 space-y-3"
         x-data="{
             name: '<?= addslashes(htmlspecialchars($trans['name'] ?? '')) ?>',
             metaLocked: <?= !empty($trans['meta_title']) ? 'true' : 'false' ?>,
             urlLocked: <?= !empty($trans['url_key']) ? 'true' : 'false' ?>,
             get metaTitle() { return this.metaLocked ? this.$refs.meta.value : this.name; },
             get urlKey() {
                 if (this.urlLocked) return this.$refs.url.value;
                 return this.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
             },
             onNameInput(val) {
                 this.name = val;
                 if (!this.metaLocked) this.$refs.meta.value = val;
                 if (!this.urlLocked) this.$refs.url.value = val.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
             }
         }">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Name <span class="text-red-400">*</span></label>
            <input type="text" name="translations[<?= (int) $sv['id'] ?>][name]"
                   value="<?= htmlspecialchars($trans['name'] ?? '') ?>"
                   @input="onNameInput($event.target.value)"
                   class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Short Description</label>
            <textarea name="translations[<?= (int) $sv['id'] ?>][short_description]" rows="2"
                      class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 resize-none"><?= htmlspecialchars($trans['short_description'] ?? '') ?></textarea>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Description</label>
            <textarea name="translations[<?= (int) $sv['id'] ?>][description]" rows="5"
                      class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($trans['description'] ?? '') ?></textarea>
        </div>
        <div class="grid grid-cols-3 gap-3 pt-3 border-t border-gray-100">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Price (<?= htmlspecialchars($sv['currency_code'] ?? '') ?>)</label>
                <input type="number" step="0.01" name="pricing[<?= (int) $sv['id'] ?>][price]"
                       value="<?= htmlspecialchars($pricing['price'] ?? '') ?>"
                       class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Sale Price</label>
                <input type="number" step="0.01" name="pricing[<?= (int) $sv['id'] ?>][sale_price]"
                       value="<?= htmlspecialchars($pricing['sale_price'] ?? '') ?>"
                       class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Cost Price</label>
                <input type="number" step="0.01" name="pricing[<?= (int) $sv['id'] ?>][cost_price]"
                       value="<?= htmlspecialchars($pricing['cost_price'] ?? '') ?>"
                       class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3 pt-3 border-t border-gray-100">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">
                    Meta Title
                    <span x-show="!metaLocked" class="ml-1 text-blue-400 font-normal normal-case tracking-normal">auto</span>
                </label>
                <input type="text" name="translations[<?= (int) $sv['id'] ?>][meta_title]"
                       value="<?= htmlspecialchars($trans['meta_title'] ?? '') ?>"
                       x-ref="meta"
                       @input="metaLocked = true"
                       class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">
                    URL Key
                    <span x-show="!urlLocked" class="ml-1 text-blue-400 font-normal normal-case tracking-normal">auto</span>
                </label>
                <input type="text" name="translations[<?= (int) $sv['id'] ?>][url_key]"
                       value="<?= htmlspecialchars($trans['url_key'] ?? '') ?>"
                       x-ref="url"
                       @input="urlLocked = true"
                       class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>