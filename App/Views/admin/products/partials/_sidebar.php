<!-- Status -->
<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <div class="px-4 py-2 border-b border-gray-100 bg-gray-50/70">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Status</p>
    </div>
    <div class="p-4 space-y-2.5">
        <label class="flex items-center justify-between cursor-pointer">
            <span class="text-sm text-gray-700">Active</span>
            <input type="checkbox" name="is_active" value="1" <?= ($product['is_active'] ?? 1) ? 'checked' : '' ?>
                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
        </label>
        <label class="flex items-center justify-between cursor-pointer">
            <span class="text-sm text-gray-700">Featured</span>
            <input type="checkbox" name="is_featured" value="1" <?= ($product['is_featured'] ?? 0) ? 'checked' : '' ?>
                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
        </label>
    </div>
</div>

<!-- Inventory -->
<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <div class="px-4 py-2 border-b border-gray-100 bg-gray-50/70">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Inventory</p>
    </div>
    <div class="p-4 space-y-3">
        <label class="flex items-center justify-between cursor-pointer">
            <span class="text-sm text-gray-700">Track inventory</span>
            <input type="checkbox" name="manage_stock" value="1" <?= ($product['manage_stock'] ?? 1) ? 'checked' : '' ?>
                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
        </label>
        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Qty</label>
                <input type="number" name="stock_qty" value="<?= (int) ($product['stock_qty'] ?? 0) ?>"
                       class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Low stock</label>
                <input type="number" name="low_stock_threshold" value="<?= (int) ($product['low_stock_threshold'] ?? 5) ?>"
                       class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
    </div>
</div>

<!-- Brand -->
<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <div class="px-4 py-2 border-b border-gray-100 bg-gray-50/70">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Brand</p>
    </div>
    <div class="p-4">
        <select name="brand_id" class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
            <option value="">No brand</option>
            <?php foreach ($brands ?? [] as $brand): ?>
            <option value="<?= (int) $brand['id'] ?>" <?= ($product['brand_id'] ?? '') == $brand['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($brand['name']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<!-- Categories -->
<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <div class="px-4 py-2 border-b border-gray-100 bg-gray-50/70">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Categories</p>
    </div>
    <div class="px-4 py-3 max-h-52 overflow-y-auto space-y-1.5">
        <?php foreach ($categories ?? [] as $cat): ?>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="categories[]" value="<?= (int) $cat['id'] ?>"
                   <?= in_array($cat['id'], $selectedCategories ?? []) ? 'checked' : '' ?>
                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="text-sm text-gray-700"><?= htmlspecialchars($cat['name'] ?? $cat['slug']) ?></span>
        </label>
        <?php endforeach; ?>
    </div>
</div>

<!-- Vehicle Compatibility -->
<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <div class="px-4 py-2 border-b border-gray-100 bg-gray-50/70">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Vehicle Compatibility</p>
    </div>
    <div class="px-4 py-3 max-h-64 overflow-y-auto space-y-1.5">
        <?php foreach ($vehicles ?? [] as $vehicle): ?>
        <label class="flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="vehicles[]" value="<?= (int) $vehicle['id'] ?>"
                   <?= in_array($vehicle['id'], $selectedVehicles ?? []) ? 'checked' : '' ?>
                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            <span class="text-sm text-gray-700"><?= htmlspecialchars(trim(($vehicle['brand_name'] ?? '') . ' ' . $vehicle['model'])) ?></span>
        </label>
        <?php endforeach; ?>
    </div>
</div>

<!-- Information (edit only) -->
<?php if (!empty($product['id'])): ?>
<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <div class="px-4 py-2 border-b border-gray-100 bg-gray-50/70">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Information</p>
    </div>
    <div class="p-4 space-y-1.5 text-xs">
        <div class="flex justify-between text-gray-500">
            <span>ID</span>
            <span class="font-mono text-gray-700"><?= (int) $product['id'] ?></span>
        </div>
        <div class="flex justify-between text-gray-500">
            <span>Stock</span>
            <span class="text-gray-700"><?= (int) ($product['stock_qty'] ?? 0) ?> units</span>
        </div>
        <div class="flex justify-between text-gray-500">
            <span>Created</span>
            <span class="text-gray-700"><?= htmlspecialchars($product['created_at'] ?? '—') ?></span>
        </div>
        <div class="flex justify-between text-gray-500">
            <span>Updated</span>
            <span class="text-gray-700"><?= htmlspecialchars($product['updated_at'] ?? '—') ?></span>
        </div>
        <?php if (!empty($product['slug'])): ?>
        <div class="pt-1.5 border-t border-gray-100">
            <a href="/shop/product/<?= htmlspecialchars($product['slug']) ?>" target="_blank"
               class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700">
                View on storefront
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>