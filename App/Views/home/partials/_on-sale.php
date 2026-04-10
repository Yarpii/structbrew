<?php
$_onSaleSlice = array_slice($onSale, 0, 6);
?>
<?php if (!empty($_onSaleSlice)): ?>
<!-- On Sale -->
<section class="bg-[var(--color-bg)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-12">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold text-[var(--color-text)]">On Sale</h2>
                <span class="hidden sm:inline-flex items-center gap-1 rounded-full bg-[var(--color-accent)]/10 border border-[var(--color-accent)]/20 px-2.5 py-0.5 text-xs font-medium text-[var(--color-accent)]">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                    Deals
                </span>
            </div>
            <a href="/shop?sort=price_asc" class="text-sm font-semibold text-[var(--color-accent)] hover:underline flex items-center gap-1">
                View All
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
            <?php foreach ($_onSaleSlice as $product): ?>
                <?php include __DIR__ . '/_product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
