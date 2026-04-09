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
    <?php if (!empty($ads['shop_listing'])): ?>
        <?php $ad = $ads['shop_listing']; include dirname(__DIR__) . '/partials/_managed-ad.php'; ?>
    <?php endif; ?>

    <?php if (!empty($selectedVehicleLabel ?? '')): ?>
        <div class="mb-4 rounded-md border border-[var(--color-accent)]/25 bg-[var(--color-accent)]/5 px-4 py-3 text-sm text-[var(--color-text)]">
            Vehicle filter active: <span class="font-semibold"><?= htmlspecialchars((string) $selectedVehicleLabel) ?></span>
            <a href="/shop" class="ml-3 text-[var(--color-accent)] font-semibold hover:underline">Clear</a>
        </div>
    <?php endif; ?>
    <div class="sticky top-16 z-20 mb-5 border-b border-[var(--color-border)] bg-[var(--color-surface)]/95 backdrop-blur supports-[backdrop-filter]:bg-[var(--color-surface)]/85 lg:static lg:border-0 lg:bg-transparent lg:backdrop-blur-0">
        <div class="flex items-center justify-between gap-4 py-3 lg:py-0 lg:mb-6">
            <div>
                <h1 class="text-2xl font-bold text-[var(--color-text)]\"><?= htmlspecialchars($title) ?></h1>
                <p class="text-sm text-[var(--color-muted)] mt-0.5">
                    <?= ($pagination['from'] ?? 0) ?>-<?= ($pagination['to'] ?? 0) ?> of <?= ($pagination['total'] ?? count($products)) ?> products
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button @click="showFilters = !showFilters" class="lg:hidden inline-flex h-9 items-center gap-1.5 rounded-[var(--radius-button)] border border-[var(--color-border)] bg-[var(--color-surface)] px-3 text-sm font-medium text-[var,--color-text)]">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/></svg>
                    Filters
                </button>
                <form method="get" action="/shop" class="flex items-center gap-2">
                    <?php if ($activeCategory): ?><input type="hidden" name="category" value="<?= htmlspecialchars($activeCategory) ?>"><?php endif; ?>
                    <?php if ($searchQuery): ?><input type="hidden" name="q" value="<?= htmlspecialchars($searchQuery) ?>"><?php endif; ?>
                    <?php if (!empty($activeVehicleId ?? 0)): ?><input type="hidden" name="vehicle_id" value="<?= (int) $activeVehicleId ?>"><?php endif; ?>
                    <select name="sort" onchange="this.form.submit()" class="h-9 rounded-[var(--radius-input)] border border-[var(--color-border)] bg-[var(--color-surface)] px-3 pr-8 text-sm text-[var,--color-text)] focus:border-[var(--color-accent)]">
                        <option value="default" <?= $activeSort === 'default' ? 'selected' : '' ?>>Default</option>
                        <option value="price_asc" <?= $activeSort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                        <option value="price_desc" <?= $activeSort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                        <option value="rating" <?= $activeSort === 'rating' ? 'selected' : '' ?>>Top Rated</option>
                        <option value="name" <?= $activeSort === 'name' ? 'selected' : '' ?>>Name A-Z</option>
                    </select>
                </form>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-[260px_1fr] gap-6">
        <!-- Sidebar -->
        <aside class="lg:block lg:sticky lg:top-20 lg:self-start" x-show="showFilters || window.innerWidth >= 1024" x-cloak x-transition>
            <div class="rounded-[var(--radius-card)] border border-[var,--color-border)] bg-[var,--color-surface)] p-4 max-h-[calc(100vh-6rem)] overflow-auto" style="box-shadow: var(--shadow-sm)">
                <?php if (!empty($attributeFacets ?? [])): ?>
                    <form method="get" action="/shop" class="space-y-4">
                        <?php if ($activeCategory): ?><input type="hidden" name="category" value="<?= htmlspecialchars($activeCategory) ?>"><?php endif; ?>
                        <?php if ($searchQuery): ?><input type="hidden" name="q" value="<?= htmlspecialchars($searchQuery) ?>"><?php endif; ?>
                        <?php if (!empty($activeVehicleId ?? 0)): ?><input type="hidden" name="vehicle_id" value="<?= (int) $activeVehicleId ?>"><?php endif; ?>
                        <?php if (!empty($activeSort) && $activeSort !== 'default'): ?><input type="hidden" name="sort" value="<?= htmlspecialchars($activeSort) ?>"><?php endif; ?>

                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-[var(--color-text)]">Filter by attributes</h3>
                            <a href="/shop<?= $activeCategory ? '?category=' . urlencode($activeCategory) . ($searchQuery ? '&q=' . urlencode($searchQuery) : '') . (!empty($activeVehicleId ?? 0) ? '&vehicle_id=' . (int) $activeVehicleId : '') : ($searchQuery ? '?q=' . urlencode($searchQuery) . (!empty($activeVehicleId ?? 0) ? '&vehicle_id=' . (int) $activeVehicleId : '') : (!empty($activeVehicleId ?? 0) ? '?vehicle_id=' . (int) $activeVehicleId : '')) ?>" class="text-xs text-[var(--color-muted)] hover:text-[var(--color-accent)]">Clear</a>
                        </div>

                        <?php foreach ($attributeFacets as $facet): ?>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-[var(--color-muted)] mb-2"><?= htmlspecialchars($facet['label']) ?></p>
                                <div class="space-y-1.5 max-h-40 overflow-auto pr-1">
                                    <?php foreach ($facet['options'] as $option): ?>
                                        <?php $isChecked = in_array($option['value'], $activeAttributes[$facet['key']] ?? [], true); ?>
                                        <label class="flex items-center justify-between gap-2 text-sm text-[var,--color-text)]">
                                            <span class="inline-flex items-center gap-2 min-w-0">
                                                <input type="checkbox" name="attr[<?= htmlspecialchars($facet['key']) ?>][]" value="<?= htmlspecialchars($option['value']) ?>" <?= $isChecked ? 'checked' : '' ?> class="rounded border-[var,--color-border)] text-[var,--color-accent)] focus:ring-[var,--color-accent)]/30">
                                                <span class="truncate"><?= htmlspecialchars($option['value']) ?></span>
                                            </span>
                                            <span class="text-xs text-[var,--color-muted)]"><?= (int) $option['count'] ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <button type="submit" class="w-full inline-flex h-9 items-center justify-center rounded-[var,--radius-button)] bg-[var,--color-accent)] text-white text-sm font-semibold transition hover:bg-[var,--color-accent-hover)]">
                            Apply filters
                        </button>
                    </form>
                <?php else: ?>
                    <div class="text-sm text-[var(--color-muted)]">No attribute filters available for this selection.</div>
                <?php endif; ?>
            </div>
        </aside>

        <!-- Product Grid -->
        <div>
            <div class="mb-4 rounded-[var(--radius-card)] border border-[var,--color-border)] bg-[var,--color-surface)] p-3" style="box-shadow: var(--shadow-sm)">
                <div class="flex items-center justify-between gap-2 mb-2">
                    <h3 class="text-sm font-bold text-[var,--color-text)]">Categories</h3>
                    <a href="/categories" class="text-xs text-[var,--color-muted)] hover:text-[var,--color-accent)]">View all</a>
                </div>
                <div class="relative">
                    <div class="pointer-events-none absolute left-0 top-0 z-10 h-full w-5 bg-gradient-to-r from-[var(--color-surface)] to-transparent md:hidden"></div>
                    <div class="pointer-events-none absolute right-0 top-0 z-10 h-full w-5 bg-gradient-to-l from-[var(--color-surface)] to-transparent md:hidden"></div>
                    <div class="-mx-3 px-3 overflow-x-auto">
                        <div class="flex w-max gap-1.5 md:w-auto md:flex-wrap">
                            <?php
                            $allQuery = [];
                            if ($searchQuery) { $allQuery['q'] = $searchQuery; }
                            if (!empty($activeSort) && $activeSort !== 'default') { $allQuery['sort'] = $activeSort; }
                            if (!empty($activeVehicleId ?? 0)) { $allQuery['vehicle_id'] = (int) $activeVehicleId; }
                            ?>
                            <a href="/shop<?= !empty($allQuery) ? '?' . htmlspecialchars(http_build_query($allQuery)) : '' ?>"
                               class="inline-flex h-8 items-center whitespace-nowrap rounded-[var(--radius-button)] border px-3 text-xs font-medium transition <?= !$activeCategory ? 'border-[var(--color-accent)] bg-[var(--color-accent)] text-white' : 'border-[var(--color-border)] text-[var,--color-text)] hover:border-[var(--color-accent)]/35 hover:text-[var,--color-accent]' ?>">
                                All
                            </a>
                            <?php foreach ($categories as $key => $name): ?>
                                <?php
                                $catQuery = ['category' => $key];
                                if ($searchQuery) { $catQuery['q'] = $searchQuery; }
                                if (!empty($activeSort) && $activeSort !== 'default') { $catQuery['sort'] = $activeSort; }
                                if (!empty($activeVehicleId ?? 0)) { $catQuery['vehicle_id'] = (int) $activeVehicleId; }
                                ?>
                                <a href="/shop?<?= htmlspecialchars(http_build_query($catQuery)) ?>"
                                   class="inline-flex h-8 items-center whitespace-nowrap rounded-[var(--radius-button)] border px-3 text-xs font-medium transition <?= $activeCategory === $key ? 'border-[var(--color-accent)] bg-[var(--color-accent)] text-white' : 'border-[var(--color-border)] text-[var,--color-text)] hover:border-[var(--color-accent)]/35 hover:text-[var,--color-accent]' ?>">
                                    <?= htmlspecialchars($name) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (empty($products)): ?>
                <div class="text-center py-16 rounded-[var,--radius-card)] border border-[var,--color-border)] bg-[var,--color-surface)]">
                    <svg class="mx-auto mb-4 text-[var,--color-muted)]" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <h3 class="text-lg font-semibold text-[var,--color-text)]">No products found</h3>
                    <p class="mt-1 text-sm text-[var,--color-muted)]">Try adjusting your search or filter to find what you're looking for.</p>
                    <a href="/shop" class="mt-4 inline-flex h-10 items-center rounded-[var,--radius-button)] bg-[var,--color-accent)] px-5 text-sm font-semibold text-white transition hover:bg-[var,--color-accent-hover)]">View All Products</a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 xs:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card group rounded-[var,--radius-card)] border border-[var,--color-border)] bg-[var,--color-surface)] overflow-hidden transition-all duration-200" style="box-shadow: var(--shadow-sm)">
                            <a href="/shop/<?= htmlspecialchars($product['slug']) ?>" class="block relative aspect-square bg-[var(--color-bg)] overflow-hidden">
                                <?php if (!empty($product['image'])): ?>
                                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
                                <?php else: ?>
                                    <div class="product-img w-full h-full flex items-center justify-center transition-transform duration-300">
                                        <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-[var,--color-accent)]/20 to-[var,--color-accent)]/5 flex items-center justify-center">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" opacity=".6"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if ($product['badge']): ?>
                                    <span class="absolute top-2 left-2 rounded-full px-2 py-0.5 text-[10px] font-semibold
                                        <?= $product['badge'] === 'Sale' ? 'bg-[var,--color-accent)] text-white' : '' ?>
                                        <?= $product['badge'] === 'New' ? 'bg-emerald-500 text-white' : '' ?>
                                        <?= $product['badge'] === 'Bestseller' ? 'bg-amber-500 text-white' : '' ?>
                                        <?= $product['badge'] === 'Popular' ? 'bg-blue-500 text-white' : '' ?>
                                        <?= $product['badge'] === 'Out of Stock' ? 'bg-gray-500 text-white' : '' ?>
                                    "><?= htmlspecialchars($product['badge']) ?></span>
                                <?php endif; ?>
                            </a>
                            <div class="p-3">
                                <p class="text-[11px] text-[var,--color-muted)] uppercase tracking-wide mb-1"><?= htmlspecialchars($categories[$product['category']] ?? $product['category']) ?></p>
                                <a href="/shop/<?= htmlspecialchars($product['slug']) ?>" class="block text-sm font-semibold text-[var,--color-text)] leading-snug hover:text-[var,--color-accent)] transition-colors line-clamp-2">
                                    <?= htmlspecialchars($product['name']) ?>
                                </a>
                                <div class="flex items-center gap-1 mt-1.5">
                                    <div class="flex text-amber-400">
                                        <?php for ($i = 0; $i < 5; $i++): ?>
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="<?= $i < round($product['rating']) ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="text-[11px] text-[var,--color-muted)]">(<?= $product['reviews'] ?>)</span>
                                </div>
                                <div class="flex items-center justify-between mt-2.5">
                                    <div class="flex items-baseline gap-1.5">
                                        <?php if ($product['sale_price']): ?>
                                            <span class="text-sm font-bold text-[var,--color-accent)]">$<?= number_format($product['sale_price'], 2) ?></span>
                                            <span class="text-[11px] text-[var,--color-muted)] line-through">$<?= number_format($product['price'], 2) ?></span>
                                        <?php else: ?>
                                            <span class="text-sm font-bold text-[var,--color-text)]">$<?= number_format($product['price'], 2) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($product['in_stock']): ?>
                                        <button
                                            @click="$store.cart.add({id: <?= $product['id'] ?>, name: '<?= addslashes($product['name']) ?>', price: <?= $product['price'] ?>, sale_price: <?= $product['sale_price'] ?? 'null' ?>, slug: '<?= $product['slug'] ?>'})"
                                            class="inline-flex h-7 w-7 items-center justify-center rounded-[var,--radius-button)] bg-[var,--color-accent)] text-white transition hover:bg-[var,--color-accent-hover)]"
                                            title="Add to cart"
                                        >
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (($pagination['last_page'] ?? 1) > 1): ?>
                    <div class="mt-6 flex items-center justify-between rounded-[var,--radius-card)] border border-[var,--color-border)] bg-[var,--color-surface)] px-4 py-3 text-sm" style="box-shadow: var(--shadow-sm)">
                        <span class="text-[var,--color-muted)]">Page <?= $pagination['current_page'] ?> of <?= $pagination['last_page'] ?></span>
                        <div class="flex items-center gap-1.5">
                            <?php $prevPage = max(1, (int) $pagination['current_page'] - 1); ?>
                            <?php $nextPage = min((int) $pagination['last_page'], (int) $pagination['current_page'] + 1); ?>

                            <?php $queryPrev = $_GET; $queryPrev['page'] = $prevPage; ?>
                            <a href="?<?= htmlspecialchars(http_build_query($queryPrev)) ?>"
                               class="inline-flex h-8 items-center rounded-[var,--radius-button)] border border-[var,--color-border)] px-3 <?= (int) $pagination['current_page'] === 1 ? 'pointer-events-none opacity-50' : 'hover:border-[var,--color-accent)]/35 hover:text-[var,--color-accent)]' ?>">
                                Prev
                            </a>

                            <?php
                            $start = max(1, (int) $pagination['current_page'] - 2);
                            $end = min((int) $pagination['last_page'], (int) $pagination['current_page'] + 2);
                            for ($i = $start; $i <= $end; $i++):
                                $queryPage = $_GET;
                                $queryPage['page'] = $i;
                            ?>
                                <a href="?<?= htmlspecialchars(http_build_query($queryPage)) ?>"
                                   class="inline-flex h-8 min-w-8 items-center justify-center rounded-[var,--radius-button)] border px-2 <?= $i === (int) $pagination['current_page'] ? 'border-[var,--color-accent)] bg-[var,--color-accent)] text-white' : 'border-[var,--color-border)] hover:border-[var,--color-accent)]/35 hover:text-[var,--color-accent)]' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <?php $queryNext = $_GET; $queryNext['page'] = $nextPage; ?>
                            <a href="?<?= htmlspecialchars(http_build_query($queryNext)) ?>"
                               class="inline-flex h-8 items-center rounded-[var,--radius-button)] border border-[var,--color-border)] px-3 <?= (int) $pagination['current_page'] === (int) $pagination['last_page'] ? 'pointer-events-none opacity-50' : 'hover:border-[var,--color-accent)]/35 hover:text-[var,--color-accent)]' ?>">
                                Next
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
