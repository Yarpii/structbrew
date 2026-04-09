<!-- Categories -->
<section class="bg-[var(--color-bg)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-12">
        <div class="mb-5 flex items-end justify-between gap-3">
            <div>
                <h2 class="text-2xl font-bold text-[var(--color-text)]">Shop by Category</h2>
                <p class="mt-1 text-sm text-[var(--color-muted)]">Browse all categories in the catalog</p>
            </div>
            <a href="/shop" class="inline-flex h-9 items-center rounded-[var(--radius-button)] border border-[var(--color-border)] bg-[var(--color-surface)] px-3 text-sm font-medium text-[var(--color-text)] transition hover:border-[var(--color-accent)]/35 hover:text-[var(--color-accent)]">
                View All Products
            </a>
        </div>

        <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-3 sm:p-4" style="box-shadow: var(--shadow-sm)">
            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <?php foreach ($categories as $key => $name): ?>
                    <a href="/shop?category=<?= htmlspecialchars($key) ?>"
                       class="group flex items-center gap-2.5 rounded-lg border border-[var(--color-border)] bg-[var(--color-bg)] px-3 py-2 text-sm text-[var(--color-text)] transition hover:border-[var(--color-accent)]/35 hover:bg-[var(--color-accent)]/5 hover:text-[var(--color-accent)]">
                        <span class="inline-flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-md bg-[var(--color-accent)]/10 text-[var(--color-accent)]">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7a2 2 0 0 1 2-2h3l2 2h9a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                        </span>
                        <span class="truncate font-medium"><?= htmlspecialchars($name) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
