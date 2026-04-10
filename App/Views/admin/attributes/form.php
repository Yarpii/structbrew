<form method="POST" action="<?= $formAction ?? '/admin/attributes' ?>" enctype="multipart/form-data">
    <input type="hidden" name="_csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">

    <!-- Sticky action bar -->
    <div class="sticky top-16 z-10 bg-white border-b border-gray-200 px-5 py-2 flex items-center justify-between gap-4">
        <div class="flex items-center gap-1.5 min-w-0 text-xs text-gray-400">
            <a href="/admin/attributes" class="hover:text-gray-600">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <a href="/admin/attributes" class="hover:text-gray-600">Attributes</a>
            <span>/</span>
            <span class="font-medium text-gray-700 truncate"><?= !empty($attribute['label']) ? htmlspecialchars($attribute['label']) : 'New Attribute' ?></span>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="/admin/attributes" class="px-3 py-1.5 text-xs text-gray-500 hover:text-gray-700 rounded transition-colors">Cancel</a>
            <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded transition-colors">
                Save Attribute
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 p-5"
         x-data="attributeForm('<?= htmlspecialchars($attribute['input_type'] ?? 'text') ?>')">

        <div class="xl:col-span-2 space-y-4">

            <!-- Details -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-4 py-2 border-b border-gray-100 bg-gray-50/70">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Attribute Details</p>
                </div>
                <div class="p-4 grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Label <span class="text-red-400">*</span></label>
                        <input type="text" name="label" required value="<?= htmlspecialchars($attribute['label'] ?? '') ?>"
                               class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Code <span class="text-red-400">*</span></label>
                        <input type="text" name="code" required value="<?= htmlspecialchars($attribute['code'] ?? '') ?>"
                               placeholder="e.g. sticker-style" class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded font-mono focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Input Type</label>
                        <select name="input_type" x-model="type"
                                class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <optgroup label="Basic">
                                <option value="text">Text</option>
                                <option value="textarea">Textarea</option>
                                <option value="number">Number</option>
                                <option value="boolean">Yes / No</option>
                            </optgroup>
                            <optgroup label="Choice">
                                <option value="select">Select (dropdown)</option>
                                <option value="multi_select">Multi-select (checkboxes)</option>
                            </optgroup>
                            <optgroup label="Visual">
                                <option value="swatch_color">Colour Swatch</option>
                                <option value="swatch_image">Image Swatch</option>
                            </optgroup>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Sort Order</label>
                        <input type="number" name="sort_order" value="<?= (int) ($attribute['sort_order'] ?? 0) ?>"
                               class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Select options (plain text list) -->
            <div x-show="type === 'select'" x-cloak class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-4 py-2 border-b border-gray-100 bg-gray-50/70">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Options <span class="font-normal normal-case tracking-normal text-gray-400">— one per line</span></p>
                </div>
                <div class="p-4">
                    <textarea name="options_text" rows="8" placeholder="Red&#10;Blue&#10;Black&#10;White"
                              class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded font-mono focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($optionsText ?? '') ?></textarea>
                </div>
            </div>

            <!-- Multi-select options -->
            <div x-show="type === 'multi_select'" x-cloak class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-4 py-2 border-b border-gray-100 bg-gray-50/70 flex items-center justify-between">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Options</p>
                    <button type="button" @click="addOption()"
                            class="flex items-center gap-1 px-2 py-1 text-xs text-blue-600 hover:bg-blue-50 rounded transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add option
                    </button>
                </div>
                <div class="p-4 space-y-2">
                    <template x-for="(opt, i) in options" :key="i">
                        <div class="flex items-center gap-2">
                            <input type="hidden" :name="'swatch_ids[' + i + ']'" :value="opt.id">
                            <input type="text" :name="'swatch_labels[' + i + ']'" x-model="opt.label"
                                   placeholder="Label (e.g. Yamaha R1 Livery)"
                                   class="flex-1 px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <input type="text" :name="'swatch_values[' + i + ']'" x-model="opt.value"
                                   placeholder="Value (e.g. r1-livery)"
                                   class="flex-1 px-2.5 py-1.5 text-sm border border-gray-300 rounded font-mono focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <button type="button" @click="removeOption(i)"
                                    class="p-1.5 text-gray-300 hover:text-red-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                    <p x-show="options.length === 0" class="text-xs text-gray-400 text-center py-4">No options yet — click "Add option"</p>
                </div>
            </div>

            <!-- Colour swatch options -->
            <div x-show="type === 'swatch_color'" x-cloak class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-4 py-2 border-b border-gray-100 bg-gray-50/70 flex items-center justify-between">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Colour Options</p>
                    <button type="button" @click="addOption()"
                            class="flex items-center gap-1 px-2 py-1 text-xs text-blue-600 hover:bg-blue-50 rounded transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add colour
                    </button>
                </div>
                <div class="p-4 space-y-2">
                    <template x-for="(opt, i) in options" :key="i">
                        <div class="flex items-center gap-2">
                            <input type="hidden" :name="'swatch_ids[' + i + ']'" :value="opt.id">
                            <div class="relative shrink-0">
                                <input type="color" :name="'swatch_values[' + i + ']'" x-model="opt.value"
                                       class="w-10 h-8 rounded border border-gray-300 cursor-pointer p-0.5">
                            </div>
                            <input type="text" x-model="opt.value" placeholder="#000000"
                                   class="w-28 px-2.5 py-1.5 text-sm border border-gray-300 rounded font-mono focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <input type="text" :name="'swatch_labels[' + i + ']'" x-model="opt.label"
                                   placeholder="Label (e.g. Gloss Black)"
                                   class="flex-1 px-2.5 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <button type="button" @click="removeOption(i)"
                                    class="p-1.5 text-gray-300 hover:text-red-400 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                    <p x-show="options.length === 0" class="text-xs text-gray-400 text-center py-4">No colours yet — click "Add colour"</p>
                </div>
            </div>

            <!-- Image swatch options -->
            <div x-show="type === 'swatch_image'" x-cloak class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-4 py-2 border-b border-gray-100 bg-gray-50/70 flex items-center justify-between">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Image Options</p>
                    <button type="button" @click="addOption()"
                            class="flex items-center gap-1 px-2 py-1 text-xs text-blue-600 hover:bg-blue-50 rounded transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add image
                    </button>
                </div>
                <div class="p-4 space-y-3">
                    <template x-for="(opt, i) in options" :key="i">
                        <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <input type="hidden" :name="'swatch_ids[' + i + ']'" :value="opt.id">
                            <!-- Preview -->
                            <div class="w-14 h-14 shrink-0 rounded border border-gray-200 bg-white overflow-hidden flex items-center justify-center">
                                <img x-show="opt.preview || opt.value"
                                     :src="opt.preview || '/uploads/attributes/' + opt.value"
                                     class="w-full h-full object-cover">
                                <svg x-show="!opt.preview && !opt.value" class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01"/>
                                </svg>
                            </div>
                            <div class="flex-1 space-y-2">
                                <input type="text" :name="'swatch_labels[' + i + ']'" x-model="opt.label"
                                       placeholder="Label (e.g. Flame Red Sticker Pack)"
                                       class="w-full px-2.5 py-1.5 text-sm border border-gray-300 rounded bg-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <span class="text-xs text-gray-400">Image</span>
                                    <input type="file" :name="'swatch_images[' + i + ']'" accept="image/*"
                                           @change="previewImage($event, i)"
                                           class="text-xs text-gray-500 file:mr-2 file:px-2 file:py-1 file:text-xs file:bg-gray-100 file:border file:border-gray-300 file:rounded file:cursor-pointer">
                                </label>
                                <input type="hidden" :name="'swatch_values[' + i + ']'" :value="opt.value">
                            </div>
                            <button type="button" @click="removeOption(i)"
                                    class="p-1.5 text-gray-300 hover:text-red-400 transition-colors shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                    <p x-show="options.length === 0" class="text-xs text-gray-400 text-center py-4">No images yet — click "Add image"</p>
                </div>
            </div>

        </div>

        <!-- Sidebar -->
        <div class="space-y-4">

            <!-- Settings -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-4 py-2 border-b border-gray-100 bg-gray-50/70">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Settings</p>
                </div>
                <div class="p-4 space-y-2.5">
                    <label class="flex items-center justify-between cursor-pointer">
                        <span class="text-sm text-gray-700">Required on product</span>
                        <input type="checkbox" name="is_required" value="1" <?= ((int) ($attribute['is_required'] ?? 0) === 1) ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    </label>
                    <label class="flex items-center justify-between cursor-pointer">
                        <span class="text-sm text-gray-700">Use as catalog filter</span>
                        <input type="checkbox" name="is_filterable" value="1" <?= ((int) ($attribute['is_filterable'] ?? 0) === 1) ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    </label>
                    <label class="flex items-center justify-between cursor-pointer">
                        <span class="text-sm text-gray-700">Active</span>
                        <input type="checkbox" name="is_active" value="1" <?= ((int) ($attribute['is_active'] ?? 1) === 1) ? 'checked' : '' ?>
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    </label>
                </div>
            </div>

            <!-- Type guide -->
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-4 py-2 border-b border-gray-100 bg-gray-50/70">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Type Guide</p>
                </div>
                <div class="p-4 space-y-2 text-xs text-gray-500">
                    <div x-show="type === 'text'"><strong class="text-gray-700">Text</strong> — free-form single line. Good for part numbers, thread pitch, fitment notes.</div>
                    <div x-show="type === 'textarea'"><strong class="text-gray-700">Textarea</strong> — multi-line text. Good for detailed spec notes or compatibility descriptions.</div>
                    <div x-show="type === 'number'"><strong class="text-gray-700">Number</strong> — numeric value. Good for bore size, voltage, wattage, displacement (cc).</div>
                    <div x-show="type === 'boolean'"><strong class="text-gray-700">Yes / No</strong> — toggle flag. Good for "Universal fit", "OEM compatible", "Waterproof".</div>
                    <div x-show="type === 'select'"><strong class="text-gray-700">Select</strong> — single choice dropdown. Good for size, finish (gloss/matte), type (sport/touring).</div>
                    <div x-show="type === 'multi_select'"><strong class="text-gray-700">Multi-select</strong> — tick multiple. Good for compatible models, kit contents, standards (Euro 4 + Euro 5).</div>
                    <div x-show="type === 'swatch_color'"><strong class="text-gray-700">Colour Swatch</strong> — colour dots the customer picks from. Good for body kits, fairings, grips, seat foam colour.</div>
                    <div x-show="type === 'swatch_image'"><strong class="text-gray-700">Image Swatch</strong> — image thumbnails the customer picks from. Good for sticker packs, graphic kits, print styles, liveries.</div>
                </div>
            </div>

            <!-- Information (edit only) -->
            <?php if (!empty($attribute['id'])): ?>
            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                <div class="px-4 py-2 border-b border-gray-100 bg-gray-50/70">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Information</p>
                </div>
                <div class="p-4 space-y-1.5 text-xs">
                    <div class="flex justify-between text-gray-500">
                        <span>ID</span>
                        <span class="font-mono text-gray-700"><?= (int) $attribute['id'] ?></span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>Updated</span>
                        <span class="text-gray-700"><?= htmlspecialchars($attribute['updated_at'] ?? '—') ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</form>

<script>
function attributeForm(initialType) {
    return {
        type: initialType,
        options: <?= json_encode(array_values(array_map(static fn($o) => [
            'id'      => (int) $o['id'],
            'label'   => $o['label'],
            'value'   => $o['value'],
            'preview' => '',
        ], $swatchOptions ?? []))) ?>,

        addOption() {
            this.options.push({ id: 0, label: '', value: '', preview: '' });
        },
        removeOption(i) {
            this.options.splice(i, 1);
        },
        previewImage(event, i) {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = (e) => { this.options[i].preview = e.target.result; };
            reader.readAsDataURL(file);
        },
    };
}
</script>