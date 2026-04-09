<?php if (!empty($ad)): ?>
<?php $adClickUrl = '/ad/click/' . (int) ($ad['id'] ?? 0); ?>
<section class="rounded-md border border-[var(--color-border)] text-[var(--color-text)] overflow-hidden" style="background: <?= htmlspecialchars((string) ($ad['background_value'] ?: 'var(--color-surface)')) ?>; box-shadow: var(--shadow-sm)">
    <div class="p-5 md:p-6">
        <div class="grid gap-4 md:grid-cols-[1.4fr_1fr] items-center">
            <div>
                <span class="inline-flex items-center rounded-md bg-[var(--color-accent)]/10 border border-[var(--color-accent)]/25 px-2.5 py-0.5 text-[11px] font-semibold text-[var(--color-accent)] mb-2">Sponsored</span>
                <h2 class="text-xl md:text-2xl font-extrabold tracking-tight text-[var(--color-text)]\"><?= htmlspecialchars((string) ($ad['title'] ?? 'Promotion')) ?></h2>
                <?php if (!empty($ad['subtitle'])): ?>
                    <p class="mt-2 text-sm text-[var(--color-muted)] leading-relaxed max-w-2xl"><?= htmlspecialchars((string) $ad['subtitle']) ?></p>
                <?php endif; ?>
                <?php if (!empty($ad['cta_url']) && !empty($ad['cta_label'])): ?>
                    <div class="mt-4">
                        <a href="<?= htmlspecialchars($adClickUrl) ?>" class="inline-flex h-10 items-center rounded-md bg-[var(--color-accent)] px-4 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">
                            <?= htmlspecialchars((string) $ad['cta_label']) ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <?php if (!empty($ad['image_url'])): ?>
                <div class="hidden md:block justify-self-end w-full max-w-xs">
                    <img src="<?= htmlspecialchars((string) $ad['image_url']) ?>" alt="<?= htmlspecialchars((string) ($ad['title'] ?? 'Ad')) ?>" class="w-full max-h-40 object-cover rounded-md border border-[var(--color-border)] bg-[var(--color-surface)]">
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?>
