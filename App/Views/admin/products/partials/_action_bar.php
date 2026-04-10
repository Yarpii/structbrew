<div class="sticky top-16 z-10 bg-white border-b border-gray-200 px-5 py-2 flex items-center justify-between gap-4">
    <div class="flex items-center gap-1.5 min-w-0 text-xs text-gray-400">
        <a href="/admin/products" class="hover:text-gray-600">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <a href="/admin/products" class="hover:text-gray-600">Products</a>
        <span>/</span>
        <span class="font-medium text-gray-700 truncate"><?= !empty($product['sku']) ? htmlspecialchars($product['sku']) : 'New Product' ?></span>
    </div>
    <div class="flex items-center gap-2 shrink-0">
        <a href="/admin/products" class="px-3 py-1.5 text-xs text-gray-500 hover:text-gray-700 rounded transition-colors">Cancel</a>
        <button type="submit" name="action" value="save_continue" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded transition-colors">Save &amp; Continue</button>
        <button type="submit" name="action" value="save" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded transition-colors">Save Product</button>
    </div>
</div>