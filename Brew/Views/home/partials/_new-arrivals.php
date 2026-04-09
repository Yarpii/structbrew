<!-- New Arrivals -->
<section class="bg-[var(--color-bg)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-12">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold text-[var(--color-text)]">New Arrivals</h2>
                <span class="hidden sm:inline-flex items-center gap-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 px-2.5 py-0.5 text-xs font-medium text-emerald-500">
                    Fresh
                </span>
            </div>
            <a href="/shop" class="text-sm font-semibold text-[var(--color-accent)] hover:underline flex items-center gap-1">
                View All
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
        <div class="grid grid-cols-1 xs:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php foreach ($newArrivals as $product): ?>
                <?php include __DIR__ . '/_product-card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
