<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <div class="px-4 py-2 border-b border-gray-100 bg-gray-50/70">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Images</p>
    </div>
    <div class="p-4">
        <?php if (!empty($images)): ?>
        <div class="grid grid-cols-6 gap-2 mb-3">
            <?php foreach ($images as $img): ?>
            <div class="relative">
                <img src="<?= htmlspecialchars($img['path']) ?>" alt=""
                     class="w-full aspect-square object-cover rounded border border-gray-200">
                <?php if ($img['is_main']): ?>
                <span class="absolute top-1 left-1 px-1 py-0.5 bg-blue-600 text-white text-[9px] font-medium rounded">Main</span>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <label for="product-images"
               class="flex items-center gap-3 border border-dashed border-gray-300 rounded-lg px-4 py-4 cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 transition-colors">
            <svg class="w-6 h-6 text-gray-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <div>
                <p class="text-sm text-gray-500">Drop images here or <span class="text-blue-600 font-medium">browse</span></p>
                <p class="text-xs text-gray-400">JPG, PNG, WebP — max 5 MB each</p>
            </div>
            <input type="file" name="images[]" multiple accept="image/*" class="hidden" id="product-images">
        </label>
    </div>
</div>