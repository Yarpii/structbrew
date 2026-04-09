<!-- Breadcrumb -->
<div class="bg-[var(--color-bg)] border-b border-[var(--color-border)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-3">
        <nav class="flex items-center gap-1.5 text-sm text-[var(--color-muted)]">
            <a href="/" class="hover:text-[var(--color-accent)] transition-colors">Home</a>
            <span>/</span>
            <span class="text-[var(--color-text)] font-medium"><?= htmlspecialchars($title) ?></span>
        </nav>
    </div>
</div>

<div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-6" x-data="{ showFilters: false }">
    <div class="flex items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-[var(--color-text)]"><?= htmlspecialchars($title) ?></h1>
            <p class="text-sm text-[var(--color-muted)] mt-0.5"><?= count($products) ?> product<?= count($products) !== 1 ? 's' : '' ?></p>
        </div>
        <div class="flex items-center gap-2">
            <button @click="showFilters = !showFilters" class="lg:hidden inline-flex h-9 items-center gap-1.5 rounded-[var(--radius-button)] border border-[var(--color-border)] bg-[var(--color-surface)] px-3 text-sm font-medium text-[var(--color-text)]">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/></svg>
                Filters
            </button>
            <form method="get" action="/shop" class="flex items-center gap-2">
                <?php if ($activeCategory): ?><input type="hidden" name="category" value="<?= htmlspecialchars($activeCategory) ?>"><?php endif; ?>
                <?php if ($searchQuery): ?><input type="hidden" name="q" value="<?= htmlspecialchars($searchQuery) ?>"><?php endif; ?>
                <select name="sort" onchange="this.form.submit()" class="h-9 rounded-[var(--radius-input)] border border-[var(--color-border)] bg-[var(--color-surface)] px-3 pr-8 text-sm text-[var(--color-text)] focus:border-[var(--color-accent)]">
                    <option value="default" <?= $activeSort === 'default' ? 'selected' : '' ?>>Default</option>
                    <option value="price_asc" <?= $activeSort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_desc" <?= $activeSort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                    <option value="rating" <?= $activeSort === 'rating' ? 'selected' : '' ?>>Top Rated</option>
                    <option value="name" <?= $activeSort === 'name' ? 'selected' : '' ?>>Name A-Z</option>
                </select>
            </form>
        </div>
    </div>

    <div class="grid lg:grid-cols-[220px_1fr] gap-6">
        <!-- Sidebar -->
        <aside class="lg:block" x-show="showFilters || window.innerWidth >= 1024" x-cloak x-transition>
            <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-4" style="box-shadow: var(--shadow-sm)">
                <h3 class="text-sm font-bold text-[var(--color-text)] mb-3">Categories</h3>
                <ul class="space-y-1">
                    <li>
                        <a href="/shop<?= $searchQuery ? '?q=' . urlencode($searchQuery) : '' ?>"
                           class="block rounded-lg px-2.5 py-1.5 text-sm transition-colors <?= !$activeCategory ? 'bg-[var(--color-accent)]/10 text-[var(--color-accent)] font-semibold border border-[var(--color-accent)]/25' : 'text-[var(--color-text)] hover:bg-[var(--color-bg)]' ?>">
                            All Products
                        </a>
                    </li>
                    <?php foreach ($categories as $key => $name): ?>
                        <li>
                            <a href="/shop?category=<?= urlencode($key) ?><?= $searchQuery ? '&q=' . urlencode($searchQuery) : '' ?>"
                               class="block rounded-lg px-2.5 py-1.5 text-sm transition-colors <?= $activeCategory === $key ? 'bg-[var(--color-accent)]/10 text-[var(--color-accent)] font-semibold border border-[var(--color-accent)]/25' : 'text-[var(--color-text)] hover:bg-[var(--color-bg)]' ?>">
                                <?= htmlspecialchars($name) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>

        <!-- Product Grid -->
        <div>
            <?php if (empty($products)): ?>
                <div class="text-center py-16 rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)]">
                    <svg class="mx-auto mb-4 text-[var(--color-muted)]" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <h3 class="text-lg font-semibold text-[var(--color-text)]">No products found</h3>
                    <p class="mt-1 text-sm text-[var(--color-muted)]">Try adjusting your search or filter to find what you're looking for.</p>
                    <a href="/shop" class="mt-4 inline-flex h-10 items-center rounded-[var(--radius-button)] bg-[var(--color-accent)] px-5 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">View All Products</a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 xs:grid-cols-2 md:grid-cols-3 gap-4">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card group rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] overflow-hidden transition-all duration-200" style="box-shadow: var(--shadow-sm)">
                            <a href="/shop/<?= htmlspecialchars($product['slug']) ?>" class="block relative aspect-square bg-[var(--color-bg)] overflow-hidden">
                                <div class="product-img w-full h-full flex items-center justify-center transition-transform duration-300">
                                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-[var(--color-accent)]/20 to-[var(--color-accent)]/5 flex items-center justify-center">
                                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" opacity=".6"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                    </div>
                                </div>
                                <?php if ($product['badge']): ?>
                                    <span class="absolute top-2.5 left-2.5 rounded-full px-2.5 py-0.5 text-xs font-semibold
                                        <?= $product['badge'] === 'Sale' ? 'bg-[var(--color-accent)] text-white' : '' ?>
                                        <?= $product['badge'] === 'New' ? 'bg-emerald-500 text-white' : '' ?>
                                        <?= $product['badge'] === 'Bestseller' ? 'bg-amber-500 text-white' : '' ?>
                                        <?= $product['badge'] === 'Popular' ? 'bg-blue-500 text-white' : '' ?>
                                        <?= $product['badge'] === 'Out of Stock' ? 'bg-gray-500 text-white' : '' ?>
                                    "><?= htmlspecialchars($product['badge']) ?></span>
                                <?php endif; ?>
                            </a>
                            <div class="p-3.5">
                                <p class="text-xs text-[var(--color-muted)] uppercase tracking-wide mb-1"><?= htmlspecialchars($categories[$product['category']] ?? $product['category']) ?></p>
                                <a href="/shop/<?= htmlspecialchars($product['slug']) ?>" class="block text-sm font-semibold text-[var(--color-text)] leading-snug hover:text-[var(--color-accent)] transition-colors line-clamp-2">
                                    <?= htmlspecialchars($product['name']) ?>
                                </a>
                                <div class="flex items-center gap-1 mt-1.5">
                                    <div class="flex text-amber-400">
                                        <?php for ($i = 0; $i < 5; $i++): ?>
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="<?= $i < round($product['rating']) ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="text-xs text-[var(--color-muted)]">(<?= $product['reviews'] ?>)</span>
                                </div>
                                <div class="flex items-center justify-between mt-3">
                                    <div class="flex items-baseline gap-1.5">
                                        <?php if ($product['sale_price']): ?>
                                            <span class="text-base font-bold text-[var(--color-accent)]">$<?= number_format($product['sale_price'], 2) ?></span>
                                            <span class="text-xs text-[var(--color-muted)] line-through">$<?= number_format($product['price'], 2) ?></span>
                                        <?php else: ?>
                                            <span class="text-base font-bold text-[var(--color-text)]">$<?= number_format($product['price'], 2) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($product['in_stock']): ?>
                                        <button
                                            @click="$store.cart.add({id: <?= $product['id'] ?>, name: '<?= addslashes($product['name']) ?>', price: <?= $product['price'] ?>, sale_price: <?= $product['sale_price'] ?? 'null' ?>, slug: '<?= $product['slug'] ?>'})"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-[var(--radius-button)] bg-[var(--color-accent)] text-white transition hover:bg-[var(--color-accent-hover)]"
                                            title="Add to cart"
                                        >
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
