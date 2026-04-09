<form method="POST" action="<?= $formAction ?? '/admin/products' ?>" enctype="multipart/form-data" class="space-y-6">
    <input type="hidden" name="_token" value="<?= \App\Core\Session::csrfToken() ?>">

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="xl:col-span-2 space-y-6">
            <!-- General Info -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">General Information</h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SKU <span class="text-red-500">*</span></label>
                            <input type="text" name="sku" required value="<?= htmlspecialchars($product['sku'] ?? '') ?>"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                            <input type="text" name="slug" value="<?= htmlspecialchars($product['slug'] ?? '') ?>"
                                   placeholder="auto-generated from name"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">OEM Number</label>
                        <input type="text" name="oem_number" value="<?= htmlspecialchars($product['oem_number'] ?? '') ?>"
                               placeholder="Original Equipment Manufacturer number"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Weight (kg)</label>
                        <input type="number" name="weight" step="0.01" value="<?= htmlspecialchars($product['weight'] ?? '') ?>"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Translations (per store view) -->
            <div class="bg-white rounded-xl border border-gray-200 p-6" x-data="{ activeTab: '<?= $storeViews[0]['id'] ?? 0 ?>' }">
                <h3 class="font-semibold text-gray-800 mb-4">Product Details (per Store View)</h3>

                <!-- Store View Tabs -->
                <div class="flex gap-1 border-b border-gray-200 mb-4 overflow-x-auto">
                    <?php foreach ($storeViews ?? [] as $sv): ?>
                    <button type="button" @click="activeTab = '<?= $sv['id'] ?>'"
                            :class="activeTab === '<?= $sv['id'] ?>' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="px-4 py-2 text-sm font-medium border-b-2 whitespace-nowrap transition-colors">
                        <?= htmlspecialchars($sv['name']) ?>
                        <span class="text-xs text-gray-400">(<?= $sv['locale'] ?>)</span>
                    </button>
                    <?php endforeach; ?>
                </div>

                <?php foreach ($storeViews ?? [] as $sv):
                    $trans = $translations[$sv['id']] ?? [];
                    $pricing = $pricingData[$sv['id']] ?? [];
                ?>
                <div x-show="activeTab === '<?= $sv['id'] ?>'" x-cloak class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                        <input type="text" name="translations[<?= $sv['id'] ?>][name]"
                               value="<?= htmlspecialchars($trans['name'] ?? '') ?>"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Short Description</label>
                        <textarea name="translations[<?= $sv['id'] ?>][short_description]" rows="2"
                                  class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($trans['short_description'] ?? '') ?></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="translations[<?= $sv['id'] ?>][description]" rows="6"
                                  class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($trans['description'] ?? '') ?></textarea>
                    </div>

                    <!-- Pricing for this store view -->
                    <div class="grid grid-cols-3 gap-4 pt-2 border-t border-gray-100">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Price (<?= $sv['currency_code'] ?>)</label>
                            <input type="number" step="0.01" name="pricing[<?= $sv['id'] ?>][price]"
                                   value="<?= htmlspecialchars($pricing['price'] ?? '') ?>"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sale Price</label>
                            <input type="number" step="0.01" name="pricing[<?= $sv['id'] ?>][sale_price]"
                                   value="<?= htmlspecialchars($pricing['sale_price'] ?? '') ?>"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cost Price</label>
                            <input type="number" step="0.01" name="pricing[<?= $sv['id'] ?>][cost_price]"
                                   value="<?= htmlspecialchars($pricing['cost_price'] ?? '') ?>"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <!-- SEO -->
                    <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                            <input type="text" name="translations[<?= $sv['id'] ?>][meta_title]"
                                   value="<?= htmlspecialchars($trans['meta_title'] ?? '') ?>"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">URL Key</label>
                            <input type="text" name="translations[<?= $sv['id'] ?>][url_key]"
                                   value="<?= htmlspecialchars($trans['url_key'] ?? '') ?>"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Images -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Images</h3>
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-400 transition-colors">
                    <input type="file" name="images[]" multiple accept="image/*" class="hidden" id="product-images">
                    <label for="product-images" class="cursor-pointer">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm text-gray-500">Drop images here or <span class="text-blue-600 font-medium">browse</span></p>
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP (max 5MB each)</p>
                    </label>
                </div>
                <?php if (!empty($images)): ?>
                <div class="grid grid-cols-6 gap-3 mt-4">
                    <?php foreach ($images as $img): ?>
                    <div class="relative group">
                        <img src="<?= htmlspecialchars($img['path']) ?>" alt="" class="w-full aspect-square object-cover rounded-lg">
                        <?php if ($img['is_main']): ?>
                        <span class="absolute top-1 left-1 px-1.5 py-0.5 bg-blue-600 text-white text-[10px] rounded">Main</span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status & Visibility -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Status</h3>
                <div class="space-y-4">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" value="1" <?= ($product['is_active'] ?? 1) ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Active</span>
                    </label>
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="is_featured" value="1" <?= ($product['is_featured'] ?? 0) ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Featured</span>
                    </label>
                </div>
            </div>

            <!-- Brand -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Brand</h3>
                <select name="brand_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">No brand</option>
                    <?php foreach ($brands ?? [] as $brand): ?>
                    <option value="<?= $brand['id'] ?>" <?= ($product['brand_id'] ?? '') == $brand['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($brand['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Categories -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Categories</h3>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    <?php foreach ($categories ?? [] as $cat): ?>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>"
                               <?= in_array($cat['id'], $selectedCategories ?? []) ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700"><?= htmlspecialchars($cat['name'] ?? $cat['slug']) ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Inventory -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Inventory</h3>
                <div class="space-y-4">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="manage_stock" value="1" <?= ($product['manage_stock'] ?? 1) ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Track inventory</span>
                    </label>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity</label>
                        <input type="number" name="stock_qty" value="<?= $product['stock_qty'] ?? 0 ?>"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Low Stock Threshold</label>
                        <input type="number" name="low_stock_threshold" value="<?= $product['low_stock_threshold'] ?? 5 ?>"
                               class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Vehicle Compatibility -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Vehicle Compatibility</h3>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    <?php foreach ($vehicles ?? [] as $vehicle): ?>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="vehicles[]" value="<?= $vehicle['id'] ?>"
                               <?= in_array($vehicle['id'], $selectedVehicles ?? []) ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700"><?= htmlspecialchars($vehicle['display_name'] ?? $vehicle['model']) ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit -->
    <div class="flex items-center justify-between bg-white rounded-xl border border-gray-200 p-4 sticky bottom-4">
        <a href="/admin/products" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
        <div class="flex gap-3">
            <button type="submit" name="action" value="save_continue"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                Save & Continue
            </button>
            <button type="submit" name="action" value="save"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Save Product
            </button>
        </div>
    </div>
</form>
