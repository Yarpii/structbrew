<?php $ad = $ads['home_find_setup'] ?? null; ?>

<?php if (!empty($ad)): ?>
<?php $adClickUrl = '/ad/click/' . (int) ($ad['id'] ?? 0); ?>
<section class="relative overflow-hidden text-white" style="background: <?= htmlspecialchars((string) ($ad['background_value'] ?: 'linear-gradient(135deg,#0f172a,#1f2937)')) ?>;">
    <div class="relative mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-14">
        <div class="grid gap-6 md:grid-cols-[1.4fr_1fr] items-center">
            <div>
                <span class="inline-flex items-center rounded-md bg-white/10 border border-white/20 px-3 py-1 text-xs font-medium mb-4">Sponsored</span>
                <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight"><?= htmlspecialchars((string) ($ad['title'] ?? 'Featured Promotion')) ?></h2>
                <?php if (!empty($ad['subtitle'])): ?>
                    <p class="mt-3 text-white/85 leading-relaxed max-w-xl"><?= htmlspecialchars((string) $ad['subtitle']) ?></p>
                <?php endif; ?>
                <?php if (!empty($ad['cta_url']) && !empty($ad['cta_label'])): ?>
                    <div class="mt-6">
                        <a href="<?= htmlspecialchars($adClickUrl) ?>" class="inline-flex h-10 items-center rounded-md bg-white text-slate-900 px-5 text-sm font-semibold transition hover:bg-gray-100">
                            <?= htmlspecialchars((string) $ad['cta_label']) ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (!empty($ad['image_url'])): ?>
                <div class="hidden md:block">
                    <img src="<?= htmlspecialchars((string) $ad['image_url']) ?>" alt="<?= htmlspecialchars((string) ($ad['title'] ?? 'Ad')) ?>" class="w-full max-h-56 object-cover rounded-md border border-white/20">
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php else: ?>
<!-- Ad Banner: Find Your Setup -->
<section class="relative overflow-hidden bg-gradient-to-br from-zinc-900 via-zinc-800 to-zinc-900 text-white">
    <div class="absolute inset-0 opacity-15">
        <div class="absolute top-0 right-1/3 w-80 h-80 bg-[var(--color-accent)] rounded-full blur-[100px]"></div>
        <div class="absolute bottom-0 left-1/4 w-60 h-60 bg-blue-500 rounded-full blur-[80px]"></div>
    </div>
    <div class="relative mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-14">
        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 border border-white/20 px-3 py-1 text-xs font-medium text-white/80 mb-4">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Parts Finder
                </span>
                <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight">Find the Right Parts for Your Scooter</h2>
                <p class="mt-3 text-gray-300 leading-relaxed max-w-md">
                    Whether you're restoring a classic or upgrading your ride — we've got the parts to get you moving.
                </p>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="/shop?category=engine" class="inline-flex h-10 items-center gap-2 rounded-[var(--radius-button)] bg-[var(--color-accent)] px-5 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">
                        Engine Parts
                    </a>
                    <a href="/shop?category=body" class="inline-flex h-10 items-center gap-2 rounded-[var(--radius-button)] border border-white/25 bg-white/10 px-5 text-sm font-semibold text-white transition hover:bg-white/20">
                        Body &amp; Frame
                    </a>
                    <a href="/shop?category=accessories" class="inline-flex h-10 items-center gap-2 rounded-[var(--radius-button)] border border-white/25 bg-white/10 px-5 text-sm font-semibold text-white transition hover:bg-white/20">
                        Accessories
                    </a>
                </div>
            </div>
            <div class="hidden md:grid grid-cols-2 gap-3">
                <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-sm p-5 flex flex-col items-center text-center gap-2">
                    <div class="w-12 h-12 rounded-xl bg-[var(--color-accent)]/20 flex items-center justify-center text-[var(--color-accent)]">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/></svg>
                    </div>
                    <span class="text-sm font-medium">Wheels</span>
                    <span class="text-xs text-gray-400">From $29</span>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-sm p-5 flex flex-col items-center text-center gap-2">
                    <div class="w-12 h-12 rounded-xl bg-[var(--color-accent)]/20 flex items-center justify-center text-[var(--color-accent)]">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                    </div>
                    <span class="text-sm font-medium">Brakes</span>
                    <span class="text-xs text-gray-400">From $15</span>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-sm p-5 flex flex-col items-center text-center gap-2">
                    <div class="w-12 h-12 rounded-xl bg-[var(--color-accent)]/20 flex items-center justify-center text-[var(--color-accent)]">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
                    </div>
                    <span class="text-sm font-medium">Engines</span>
                    <span class="text-xs text-gray-400">From $199</span>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-sm p-5 flex flex-col items-center text-center gap-2">
                    <div class="w-12 h-12 rounded-xl bg-[var(--color-accent)]/20 flex items-center justify-center text-[var(--color-accent)]">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                    </div>
                    <span class="text-sm font-medium">Lights</span>
                    <span class="text-xs text-gray-400">From $12</span>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
