<?php
/** @var array $product */
/** @var array $categories */
?>
<div class="product-card group rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] overflow-hidden transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5" style="box-shadow: var(--shadow-sm)">
    <a href="/shop/<?= htmlspecialchars($product['slug']) ?>" class="block relative aspect-square bg-[var(--color-bg)] overflow-hidden">
        <div class="product-img w-full h-full flex items-center justify-center transition-transform duration-300 group-hover:scale-105">
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
