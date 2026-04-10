<?php $ad = $ads['home_deals'] ?? null; ?>

<?php if (!empty($ad)): ?>
<?php $adClickUrl = '/ad/click/' . (int) ($ad['id'] ?? 0); ?>
<section class="text-white" style="background: <?= htmlspecialchars((string) ($ad['background_value'] ?: 'linear-gradient(90deg,#ef4444,#f97316)')) ?>;">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-10">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <span class="inline-flex items-center rounded-md bg-white/20 px-2.5 py-0.5 text-xs font-bold mb-2">Sponsored</span>
                <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight"><?= htmlspecialchars((string) ($ad['title'] ?? 'Featured Deal')) ?></h2>
                <?php if (!empty($ad['subtitle'])): ?>
                    <p class="mt-1 text-white/85 text-sm"><?= htmlspecialchars((string) $ad['subtitle']) ?></p>
                <?php endif; ?>
            </div>
            <div class="flex items-center gap-3">
                <?php if (!empty($ad['cta_url']) && !empty($ad['cta_label'])): ?>
                    <a href="<?= htmlspecialchars($adClickUrl) ?>" class="inline-flex h-12 items-center rounded-md bg-white text-slate-900 px-6 text-sm font-bold transition hover:bg-gray-100">
                        <?= htmlspecialchars((string) $ad['cta_label']) ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php else: ?>
<!-- Ad Banner: Deals & Promo -->
<section class="bg-gradient-to-r from-[var(--color-accent)] to-orange-500 text-white">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-10">
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="hidden sm:flex w-16 h-16 rounded-2xl bg-white/15 backdrop-blur-sm items-center justify-center shrink-0">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="inline-flex items-center rounded-full bg-white/20 px-2.5 py-0.5 text-xs font-bold">LIMITED TIME</span>
                    </div>
                    <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight">Save up to 40% on selected items</h2>
                    <p class="mt-1 text-white/80 text-sm">Don't miss these deals — <?= (int) count($onSale) ?> products on sale right now.</p>
                </div>
            </div>
            <div class="flex gap-3 shrink-0">
                <a href="/shop?sort=price_asc" class="inline-flex h-12 items-center gap-2 rounded-[var(--radius-button)] bg-white text-[var(--color-accent)] px-6 text-sm font-bold transition hover:bg-gray-100">
                    Shop Sale
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
