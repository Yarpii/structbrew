<?php $categories = \App\Data\Products::categories(); ?>
<!-- Breadcrumb -->
<div class="bg-[var(--color-bg)] border-b border-[var(--color-border)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-3">
        <nav class="flex items-center gap-1.5 text-sm text-[var(--color-muted)]">
            <a href="/" class="hover:text-[var,--color-accent)] transition-colors">Home</a>
            <span>/</span>
            <a href="/shop" class="hover:text-[var,--color-accent)] transition-colors">Shop</a>
            <span>/</span>
            <a href="/shop?category=<?= htmlspecialchars($product['category']) ?>" class="hover:text-[var,--color-accent)] transition-colors"><?= htmlspecialchars($categories[$product['category']] ?? $product['category']) ?></a>
            <span>/</span>
            <span class="text-[var(--color-text)] font-medium truncate"><?= htmlspecialchars($product['name']) ?></span>
        </nav>
    </div>
</div>

<div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-8">
    <?php if (!empty($ads['shop_product'])): ?>
        <div class="mb-6">
            <?php $ad = $ads['shop_product']; include dirname(__DIR__) . '/partials/_managed-ad.php'; ?>
        </div>
    <?php endif; ?>

    <div class="grid lg:grid-cols-[0.9fr_1.1fr] gap-8 lg:gap-10">
        <div class="space-y-3" x-data="{ currentImage: '<?= htmlspecialchars($product['image'] ?? '') ?>' }">
            <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] overflow-hidden aspect-[4/3] max-w-xl mx-auto flex items-center justify-center" style="box-shadow: var(--shadow-sm)">
                <?php if (!empty($product['images'])): ?>
                    <img :src="currentImage" alt="<?= htmlspecialchars($product['name']) ?>" class="h-full w-full object-cover" loading="lazy">
                <?php elseif (!empty($product['image'])): ?>
                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="h-full w-full object-cover" loading="lazy">
                <?php else: ?>
                    <div class="w-28 h-28 rounded-2xl bg-gradient-to-br from-[var(--color-accent)]/20 to-[var,--color-accent)-->/5 flex items-center justify-center">
                        <svg width="58" height="58" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" opacity=".55"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($product['images']) && count($product['images']) > 1): ?>
                <div class="max-w-xl mx-auto grid grid-cols-4 sm:grid-cols-6 gap-2">
                    <?php foreach ($product['images'] as $img): ?>
                        <button type="button" @click="currentImage = '<?= htmlspecialchars($img['url']) ?>'" class="overflow-hidden rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] aspect-square hover:border-[var(--color-accent)]/50 transition">
                            <img src="<?= htmlspecialchars($img['url']) ?>" alt="<?= htmlspecialchars($img['alt'] ?? $product['name']) ?>" class="h-full w-full object-cover" loading="lazy">
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-2 gap-2 max-w-xl mx-auto">
                <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] px-3 py-2 text-xs text-[var(--color-muted)]">Fast EU shipping</div>
                <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] px-3 py-2 text-xs text-[var,--color-muted)]">14-day returns</div>
            </div>
        </div>

        <div x-data="{ qty: 1, added: false, section: 'details' }">
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span class="inline-flex items-center rounded-full bg-[var(--color-accent)]/10 px-2.5 py-1 text-[11px] font-semibold uppercase tracking-wide text-[var(--color-accent)]"><?= htmlspecialchars($categories[$product['category']] ?? $product['category']) ?></span>
                <?php if (!empty($product['badge'])): ?>
                    <span class="inline-flex items-center rounded-full bg-[var(--color-bg)] border border-[var,--color-border)] px-2.5 py-1 text-[11px] font-semibold text-[var(--color-text)]"><?= htmlspecialchars($product['badge']) ?></span>
                <?php endif; ?>
            </div>

            <h1 class="text-2xl md:text-3xl font-extrabold text-[var,--color-text)] tracking-tight"><?= htmlspecialchars($product['name']) ?></h1>

            <div class="flex items-center gap-2 mt-3">
                <div class="flex text-amber-400">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="<?= $i < round($product['rating']) ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <?php endfor; ?>
                </div>
                <span class="text-sm text-[var(--color-muted)]"><?= $product['rating'] ?> (<?= $product['reviews'] ?> reviews)</span>
            </div>

            <div class="mt-5 flex items-baseline gap-3">
                <?php if ($product['sale_price']): ?>
                    <span class="text-3xl font-extrabold text-[var,--color-accent)]">$<?= number_format($product['sale_price'], 2) ?></span>
                    <span class="text-lg text-[var,--color-muted)] line-through">$<?= number_format($product['price'], 2) ?></span>
                    <?php $discount = round((1 - $product['sale_price'] / $product['price']) * 100); ?>
                    <span class="rounded-full bg-[var(--color-accent)]/10 text-[var(--color-accent)] text-sm font-semibold px-2.5 py-0.5">-<?= $discount ?>%</span>
                <?php else: ?>
                    <span class="text-3xl font-extrabold text-[var,--color-text)]">$<?= number_format($product['price'], 2) ?></span>
                <?php endif; ?>
            </div>

            <div class="mt-3 flex flex-wrap items-center gap-3">
                <?php if ($product['in_stock']): ?>
                    <span class="inline-flex items-center gap-1.5 text-sm text-emerald-600">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        In Stock
                    </span>
                <?php else: ?>
                    <span class="inline-flex items-center gap-1.5 text-sm text-red-500">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Out of Stock
                    </span>
                <?php endif; ?>
                <span class="text-xs text-[var(--color-muted)]">SKU: <?= htmlspecialchars($product['sku']) ?></span>
                <?php if (!empty($product['oem_number'])): ?>
                    <span class="text-xs text-[var(--color-muted)]">OEM: <?= htmlspecialchars($product['oem_number']) ?></span>
                <?php endif; ?>
            </div>

            <div class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-2">
                <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] px-2.5 py-2">
                    <p class="text-[10px] uppercase tracking-wide text-[var(--color-muted)]">Weight</p>
                    <p class="text-sm font-semibold text-[var,--color-text)]"><?= $product['weight'] !== null ? number_format((float) $product['weight'], 2) . ' kg' : '—' ?></p>
                </div>
                <div class="rounded-lg border border-[var(--color-border)] bg-[var,--color-surface)] px-2.5 py-2">
                    <p class="text-[10px] uppercase tracking-wide text-[var(--color-muted)]">Stock</p>
                    <p class="text-sm font-semibold text-[var,--color-text)]"><?= (int) ($product['stock_qty'] ?? 0) ?></p>
                </div>
                <div class="rounded-lg border border-[var(--color-border)] bg-[var,--color-surface)] px-2.5 py-2">
                    <p class="text-[10px] uppercase tracking-wide text-[var(--color-muted)]">Dispatch</p>
                    <p class="text-sm font-semibold text-[var,--color-text)]">24h</p>
                </div>
                <div class="rounded-lg border border-[var(--color-border)] bg-[var,--color-surface)] px-2.5 py-2">
                    <p class="text-[10px] uppercase tracking-wide text-[var(--color-muted)]">Warranty</p>
                    <p class="text-sm font-semibold text-[var,--color-text)]">12 mo</p>
                </div>
            </div>

            <div class="mt-5 rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-3">
                <div class="flex gap-2 flex-wrap">
                    <button @click="section = 'details'" :class="section === 'details' ? 'bg-[var(--color-accent)] text-white' : 'bg-[var(--color-bg)] text-[var,--color-text)]'" class="h-9 rounded-[var(--radius-button)] px-3 text-sm font-medium transition">Details</button>
                    <button @click="section = 'fitment'" :class="section === 'fitment' ? 'bg-[var(--color-accent)] text-white' : 'bg-[var(--color-bg)] text-[var,--color-text)]'" class="h-9 rounded-[var(--radius-button)] px-3 text-sm font-medium transition">Fitment</button>
                    <button @click="section = 'docs'" :class="section === 'docs' ? 'bg-[var(--color-accent)] text-white' : 'bg-[var(--color-bg)] text-[var,--color-text)]'" class="h-9 rounded-[var(--radius-button)] px-3 text-sm font-medium transition">Docs & Media</button>
                </div>

                <div class="mt-3 text-sm text-[var(--color-muted)] leading-relaxed" x-show="section === 'details'">
                    <p><?= htmlspecialchars($product['description'] !== '' ? $product['description'] : ($product['short_description'] ?? 'High-quality scooter part selected for reliable performance and daily use.')) ?></p>
                    <?php if (!empty($product['features'])): ?>
                        <ul class="mt-3 grid sm:grid-cols-2 gap-1.5">
                            <?php foreach ($product['features'] as $feature): ?>
                                <li class="flex items-center gap-2 rounded-lg bg-[var(--color-bg)] border border-[var(--color-border)] px-2.5 py-2 text-xs text-[var,--color-text)]">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    <?= htmlspecialchars($feature) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <div class="mt-3 text-sm text-[var(--color-muted)] leading-relaxed" x-show="section === 'fitment'" x-cloak>
                    <p><?= htmlspecialchars($product['fitment_notes'] ?? 'Compatibility depends on model year and engine variant. Always verify using SKU/OEM and your scooter model before ordering.') ?></p>
                    <?php if (!empty($product['installation_notes'])): ?>
                        <p class="mt-2"><?= htmlspecialchars($product['installation_notes']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="mt-3" x-show="section === 'docs'" x-cloak>
                    <?php if (!empty($product['documents'])): ?>
                        <h3 class="text-sm font-semibold text-[var,--color-text)] mb-2">Downloads</h3>
                        <ul class="space-y-1.5 mb-3">
                            <?php foreach ($product['documents'] as $doc): ?>
                                <li>
                                    <a href="<?= htmlspecialchars($doc['url']) ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-sm text-[var,--color-accent)] hover:underline">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                        <?= htmlspecialchars($doc['label']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <?php if (!empty($product['videos'])): ?>
                        <h3 class="text-sm font-semibold text-[var,--color-text)] mb-2">Videos</h3>
                        <ul class="space-y-1.5">
                            <?php foreach ($product['videos'] as $video): ?>
                                <li>
                                    <a href="<?= htmlspecialchars($video['url']) ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-sm text-[var,--color-accent)] hover:underline">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                                        <?= htmlspecialchars($video['label']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <?php if (empty($product['documents']) && empty($product['videos'])): ?>
                        <p class="text-sm text-[var,--color-muted)]">No documents or videos yet for this product.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-4 rounded-[var(--radius-card)] border border-amber-300/40 bg-amber-50/70 p-3 text-sm text-amber-900 dark:bg-amber-900/20 dark:text-amber-100 dark:border-amber-500/30">
                <p class="font-semibold">Missing product information?</p>
                <p class="mt-1 text-xs">Tell us what you need (torque specs, fitment proof, install tips, dimensions) and we’ll send details quickly.</p>
                <a href="/contact" class="mt-2 inline-flex items-center gap-1 text-xs font-semibold underline underline-offset-4">Request technical details</a>
            </div>

            <?php if (!empty($product['specs'])): ?>
                <div class="mt-4 rounded-[var,--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-3">
                    <h3 class="text-sm font-semibold text-[var,--color-text)] mb-2">Technical Specifications</h3>
                    <div class="grid sm:grid-cols-2 gap-x-5 gap-y-1.5 text-xs">
                        <?php foreach ($product['specs'] as $label => $value): ?>
                            <div class="flex items-center justify-between gap-3 border-b border-[var(--color-border)]/50 py-1">
                                <span class="text-[var(--color-muted)]"><?= htmlspecialchars($label) ?></span>
                                <span class="font-medium text-[var(--color-text)] text-right"><?= htmlspecialchars($value) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($product['in_stock']): ?>
                <div class="mt-6 flex items-center gap-3">
                    <div class="flex items-center rounded-[var(--radius-button)] border border-[var(--color-border)] bg-[var(--color-bg)]">
                        <button @click="qty = Math.max(1, qty - 1)" class="h-11 w-11 flex items-center justify-center text-[var,--color-text)] hover:text-[var(--color-accent)] transition-colors">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        </button>
                        <span class="w-10 text-center text-sm font-semibold text-[var,--color-text)]" x-text="qty"></span>
                        <button @click="qty++" class="h-11 w-11 flex items-center justify-center text-[var,--color-text)] hover:text-[var(--color-accent)] transition-colors">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        </button>
                    </div>
                    <button
                        @click="for(let i = 0; i < qty; i++) { $store.cart.add(JSON.parse(document.getElementById('product-data').textContent)); } added = true; qty = 1; setTimeout(() => added = false, 2000)"
                        class="flex-1 inline-flex h-11 items-center justify-center gap-2 rounded-[var(--radius-button)] bg-[var(--color-accent)] px-5 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]"
                    >
                    <script type="application/json" id="product-data"><?= json_encode(['id' => (int) $product['id'], 'name' => $product['name'], 'price' => (float) $product['price'], 'sale_price' => isset($product['sale_price']) ? (float) $product['sale_price'] : null, 'slug' => $product['slug']], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
                        <template x-if="!added">
                            <span class="inline-flex items-center gap-2">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                                Add to Cart
                            </span>
                        </template>
                        <template x-if="added">
                            <span class="inline-flex items-center gap-2">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                Added!
                            </span>
                        </template>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($related)): ?>
        <div class="mt-14">
            <h2 class="text-xl font-bold text-[var,--color-text)] mb-5">You might also like</h2>
            <div class="grid grid-cols-1 xs:grid-cols-2 md:grid-cols-4 gap-3">
                <?php foreach ($related as $rel): ?>
                    <div class="product-card group rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] overflow-hidden transition-all duration-200" style="box-shadow: var(--shadow-sm)">
                        <a href="/shop/<?= htmlspecialchars($rel['slug']) ?>" class="block relative h-28 bg-[var(--color-bg)] overflow-hidden">
                            <?php if (!empty($rel['image'])): ?>
                                <img src="<?= htmlspecialchars($rel['image']) ?>" alt="<?= htmlspecialchars($rel['name']) ?>" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
                            <?php else: ?>
                                <div class="product-img w-full h-full flex items-center justify-center transition-transform duration-300 group-hover:scale-105">
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[var(--color-accent)]/20 to-[var,--color-accent)]/5 flex items-center justify-center">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="1.5" opacity=".6"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </a>
                        <div class="p-2.5">
                            <a href="/shop/<?= htmlspecialchars($rel['slug']) ?>" class="block text-sm font-semibold text-[var,--color-text)] leading-snug hover:text-[var,--color-accent)] transition-colors line-clamp-2"><?= htmlspecialchars($rel['name']) ?></a>
                            <div class="mt-1.5 flex items-baseline gap-1.5">
                                <?php if ($rel['sale_price']): ?>
                                    <span class="text-sm font-bold text-[var(--color-accent)]">$<?= number_format($rel['sale_price'], 2) ?></span>
                                    <span class="text-xs text-[var(--color-muted)] line-through">$<?= number_format($rel['price'], 2) ?></span>
                                <?php else: ?>
                                    <span class="text-sm font-bold text-[var,--color-text)]">$<?= number_format($rel['price'], 2) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
