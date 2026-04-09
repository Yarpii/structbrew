<?php $categories = \Brew\Data\Products::categories(); ?>
<!-- Breadcrumb -->
<div class="bg-[var(--color-bg)] border-b border-[var(--color-border)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-3">
        <nav class="flex items-center gap-1.5 text-sm text-[var(--color-muted)]">
            <a href="/" class="hover:text-[var(--color-accent)] transition-colors">Home</a>
            <span>/</span>
            <a href="/shop" class="hover:text-[var(--color-accent)] transition-colors">Shop</a>
            <span>/</span>
            <a href="/shop?category=<?= htmlspecialchars($product['category']) ?>" class="hover:text-[var(--color-accent)] transition-colors"><?= htmlspecialchars($categories[$product['category']] ?? $product['category']) ?></a>
            <span>/</span>
            <span class="text-[var(--color-text)] font-medium truncate"><?= htmlspecialchars($product['name']) ?></span>
        </nav>
    </div>
</div>

<div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-8">
    <div class="grid lg:grid-cols-2 gap-8 lg:gap-12">
        <!-- Product Image -->
        <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] overflow-hidden aspect-square flex items-center justify-center" style="box-shadow: var(--shadow-sm)">
            <div class="w-40 h-40 rounded-3xl bg-gradient-to-br from-[var(--color-accent)]/20 to-[var(--color-accent)]/5 flex items-center justify-center">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" opacity=".5"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            </div>
        </div>

        <!-- Product Info -->
        <div x-data="{ qty: 1, added: false }">
            <p class="text-xs uppercase tracking-widest text-[var(--color-accent)] font-semibold mb-2"><?= htmlspecialchars($categories[$product['category']] ?? $product['category']) ?></p>
            <h1 class="text-2xl md:text-3xl font-extrabold text-[var(--color-text)] tracking-tight"><?= htmlspecialchars($product['name']) ?></h1>

            <!-- Rating -->
            <div class="flex items-center gap-2 mt-3">
                <div class="flex text-amber-400">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="<?= $i < round($product['rating']) ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <?php endfor; ?>
                </div>
                <span class="text-sm text-[var(--color-muted)]"><?= $product['rating'] ?> (<?= $product['reviews'] ?> reviews)</span>
            </div>

            <!-- Price -->
            <div class="mt-5 flex items-baseline gap-3">
                <?php if ($product['sale_price']): ?>
                    <span class="text-3xl font-extrabold text-[var(--color-accent)]">$<?= number_format($product['sale_price'], 2) ?></span>
                    <span class="text-lg text-[var(--color-muted)] line-through">$<?= number_format($product['price'], 2) ?></span>
                    <?php $discount = round((1 - $product['sale_price'] / $product['price']) * 100); ?>
                    <span class="rounded-full bg-[var(--color-accent)]/10 text-[var(--color-accent)] text-sm font-semibold px-2.5 py-0.5">-<?= $discount ?>%</span>
                <?php else: ?>
                    <span class="text-3xl font-extrabold text-[var(--color-text)]">$<?= number_format($product['price'], 2) ?></span>
                <?php endif; ?>
            </div>

            <!-- Stock -->
            <div class="mt-3">
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
                <span class="text-xs text-[var(--color-muted)] ml-3">SKU: <?= htmlspecialchars($product['sku']) ?></span>
            </div>

            <!-- Description -->
            <p class="mt-5 text-sm text-[var(--color-muted)] leading-relaxed"><?= htmlspecialchars($product['description']) ?></p>

            <!-- Features -->
            <?php if (!empty($product['features'])): ?>
                <div class="mt-5">
                    <h3 class="text-sm font-semibold text-[var(--color-text)] mb-2">Key Features</h3>
                    <div class="grid grid-cols-2 gap-1.5">
                        <?php foreach ($product['features'] as $feature): ?>
                            <div class="flex items-center gap-2 rounded-lg bg-[var(--color-bg)] border border-[var(--color-border)] px-2.5 py-2 text-xs text-[var(--color-text)]">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <?= htmlspecialchars($feature) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Add to Cart -->
            <?php if ($product['in_stock']): ?>
                <div class="mt-6 flex items-center gap-3">
                    <div class="flex items-center rounded-[var(--radius-button)] border border-[var(--color-border)] bg-[var(--color-bg)]">
                        <button @click="qty = Math.max(1, qty - 1)" class="h-11 w-11 flex items-center justify-center text-[var(--color-text)] hover:text-[var(--color-accent)] transition-colors">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        </button>
                        <span class="w-10 text-center text-sm font-semibold text-[var(--color-text)]" x-text="qty"></span>
                        <button @click="qty++" class="h-11 w-11 flex items-center justify-center text-[var(--color-text)] hover:text-[var(--color-accent)] transition-colors">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        </button>
                    </div>
                    <button
                        @click="for(let i = 0; i < qty; i++) { $store.cart.add({id: <?= $product['id'] ?>, name: '<?= addslashes($product['name']) ?>', price: <?= $product['price'] ?>, sale_price: <?= $product['sale_price'] ?? 'null' ?>, slug: '<?= $product['slug'] ?>'}); } added = true; qty = 1; setTimeout(() => added = false, 2000)"
                        class="flex-1 inline-flex h-11 items-center justify-center gap-2 rounded-[var(--radius-button)] bg-[var(--color-accent)] px-5 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]"
                    >
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

    <!-- Related Products -->
    <?php if (!empty($related)): ?>
        <div class="mt-14">
            <h2 class="text-xl font-bold text-[var(--color-text)] mb-5">You might also like</h2>
            <div class="grid grid-cols-1 xs:grid-cols-2 md:grid-cols-4 gap-4">
                <?php foreach ($related as $rel): ?>
                    <div class="product-card group rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] overflow-hidden transition-all duration-200" style="box-shadow: var(--shadow-sm)">
                        <a href="/shop/<?= htmlspecialchars($rel['slug']) ?>" class="block relative aspect-square bg-[var(--color-bg)] overflow-hidden">
                            <div class="product-img w-full h-full flex items-center justify-center transition-transform duration-300">
                                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-[var(--color-accent)]/20 to-[var(--color-accent)]/5 flex items-center justify-center">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="1.5" opacity=".6"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                </div>
                            </div>
                        </a>
                        <div class="p-3">
                            <a href="/shop/<?= htmlspecialchars($rel['slug']) ?>" class="block text-sm font-semibold text-[var(--color-text)] leading-snug hover:text-[var(--color-accent)] transition-colors line-clamp-2"><?= htmlspecialchars($rel['name']) ?></a>
                            <div class="mt-2 flex items-baseline gap-1.5">
                                <?php if ($rel['sale_price']): ?>
                                    <span class="text-sm font-bold text-[var(--color-accent)]">$<?= number_format($rel['sale_price'], 2) ?></span>
                                    <span class="text-xs text-[var(--color-muted)] line-through">$<?= number_format($rel['price'], 2) ?></span>
                                <?php else: ?>
                                    <span class="text-sm font-bold text-[var(--color-text)]">$<?= number_format($rel['price'], 2) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
