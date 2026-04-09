<!-- Featured Products -->
<section class="bg-[var(--color-surface)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-12">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-[var(--color-text)]">Featured Products</h2>
            <a href="/shop" class="text-sm font-semibold text-[var(--color-accent)] hover:underline flex items-center gap-1">
                View All
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
        <div class="grid grid-cols-1 xs:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php foreach ($featured as $product): ?>
                <?php include __DIR__ . '/_product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
