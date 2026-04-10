<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <div class="px-4 py-2 border-b border-gray-100 bg-gray-50/70">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">General</p>
    </div>
    <div class="p-4 grid grid-cols-2 md:grid-cols-4 gap-3">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">SKU <span class="text-red-400">*</span></label>
            <input type="text" name="sku" required value="<?= htmlspecialchars($product['sku'] ?? '') ?>"
                   class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Slug</label>
            <input type="text" name="slug" value="<?= htmlspecialchars($product['slug'] ?? '') ?>" placeholder="auto-generated"
                   class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">OEM Number</label>
            <input type="text" name="oem_number" value="<?= htmlspecialchars($product['oem_number'] ?? '') ?>"
                   class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Weight (kg)</label>
            <input type="number" name="weight" step="0.01" value="<?= htmlspecialchars($product['weight'] ?? '') ?>"
                   class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
        </div>
    </div>
</div>