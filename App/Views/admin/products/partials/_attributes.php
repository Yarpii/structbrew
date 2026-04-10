<?php if (!empty($attributes)): ?>
<div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
    <div class="px-4 py-2 border-b border-gray-100 bg-gray-50/70">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Attributes</p>
    </div>
    <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php foreach ($attributes as $attribute):
            $code      = $attribute['code'];
            $value     = $productAttributes[$code] ?? '';
            $inputType = $attribute['input_type'] ?? 'text';
            $options   = $attribute['options'] ?? [];
            $isWide    = in_array($inputType, ['textarea', 'swatch_image', 'swatch_color', 'multi_select'], true);
        ?>
        <div class="<?= $isWide ? 'md:col-span-2' : '' ?>">
            <label class="block text-xs font-medium text-gray-500 mb-1.5">
                <?= htmlspecialchars($attribute['label']) ?>
                <?php if ((int) ($attribute['is_required'] ?? 0) === 1): ?><span class="text-red-400">*</span><?php endif; ?>
            </label>

            <?php if ($inputType === 'textarea'): ?>
                <textarea name="attributes[<?= (int) $attribute['id'] ?>]" rows="3"
                          class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars((string) $value) ?></textarea>

            <?php elseif ($inputType === 'number'): ?>
                <input type="number" step="0.01" name="attributes[<?= (int) $attribute['id'] ?>]"
                       value="<?= htmlspecialchars((string) $value) ?>"
                       class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">

            <?php elseif ($inputType === 'boolean'): ?>
                <select name="attributes[<?= (int) $attribute['id'] ?>]"
                        class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">—</option>
                    <option value="1" <?= (string) $value === '1' ? 'selected' : '' ?>>Yes</option>
                    <option value="0" <?= (string) $value === '0' ? 'selected' : '' ?>>No</option>
                </select>

            <?php elseif ($inputType === 'select'): ?>
                <select name="attributes[<?= (int) $attribute['id'] ?>]"
                        class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">—</option>
                    <?php foreach ($options as $option): ?>
                    <option value="<?= htmlspecialchars((string) $option) ?>" <?= (string) $value === (string) $option ? 'selected' : '' ?>>
                        <?= htmlspecialchars((string) $option) ?>
                    </option>
                    <?php endforeach; ?>
                </select>

            <?php elseif ($inputType === 'multi_select'): ?>
                <?php $selected = is_string($value) && $value !== '' ? (json_decode($value, true) ?? []) : []; ?>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($options as $opt): ?>
                    <label class="flex items-center gap-1.5 px-2.5 py-1 border border-gray-200 rounded cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 transition-colors">
                        <input type="checkbox" name="attributes[<?= (int) $attribute['id'] ?>][]"
                               value="<?= htmlspecialchars((string) $opt['value']) ?>"
                               <?= in_array((string) $opt['value'], array_map('strval', $selected), true) ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700"><?= htmlspecialchars($opt['label']) ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>

            <?php elseif ($inputType === 'swatch_color'): ?>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($options as $opt): ?>
                    <?php $isSelected = (string) $value === (string) $opt['value']; ?>
                    <label class="relative cursor-pointer group" title="<?= htmlspecialchars($opt['label']) ?>">
                        <input type="radio" name="attributes[<?= (int) $attribute['id'] ?>]"
                               value="<?= htmlspecialchars((string) $opt['value']) ?>"
                               <?= $isSelected ? 'checked' : '' ?>
                               class="sr-only peer">
                        <span class="block w-8 h-8 rounded-full border-2 border-transparent peer-checked:border-blue-500 ring-1 ring-gray-300 transition-all"
                              style="background-color: <?= htmlspecialchars((string) $opt['value']) ?>"></span>
                        <span class="absolute -bottom-5 left-1/2 -translate-x-1/2 text-[10px] text-gray-500 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity">
                            <?= htmlspecialchars($opt['label']) ?>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>
                <div class="mt-6"></div>

            <?php elseif ($inputType === 'swatch_image'): ?>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($options as $opt): ?>
                    <?php $isSelected = (string) $value === (string) $opt['value']; ?>
                    <label class="relative cursor-pointer group" title="<?= htmlspecialchars($opt['label']) ?>">
                        <input type="radio" name="attributes[<?= (int) $attribute['id'] ?>]"
                               value="<?= htmlspecialchars((string) $opt['value']) ?>"
                               <?= $isSelected ? 'checked' : '' ?>
                               class="sr-only peer">
                        <span class="block w-14 h-14 rounded-lg border-2 border-transparent peer-checked:border-blue-500 ring-1 ring-gray-200 overflow-hidden transition-all">
                            <img src="/uploads/attributes/<?= htmlspecialchars((string) $opt['value']) ?>"
                                 alt="<?= htmlspecialchars($opt['label']) ?>"
                                 class="w-full h-full object-cover">
                        </span>
                        <span class="absolute -bottom-5 left-1/2 -translate-x-1/2 text-[10px] text-gray-500 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity">
                            <?= htmlspecialchars($opt['label']) ?>
                        </span>
                    </label>
                    <?php endforeach; ?>
                </div>
                <div class="mt-6"></div>

            <?php else: ?>
                <input type="text" name="attributes[<?= (int) $attribute['id'] ?>]"
                       value="<?= htmlspecialchars((string) $value) ?>"
                       class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
            <?php endif; ?>

        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>