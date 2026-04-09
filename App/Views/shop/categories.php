<!-- Breadcrumb -->
<div class="bg-[var(--color-bg)] border-b border-[var(--color-border)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-3">
        <nav class="flex items-center gap-1.5 text-sm text-[var(--color-muted)]">
            <a href="/" class="hover:text-[var(--color-accent)] transition-colors">Home</a>
            <span>/</span>
            <span class="text-[var(--color-text)] font-medium">Categories</span>
        </nav>
    </div>
</div>

<div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-8">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-[var(--color-text)]">Browse Categories</h1>
            <p class="mt-1 text-sm text-[var(--color-muted)]">Find parts and products faster by category.</p>
        </div>
        <a href="/shop" class="inline-flex h-10 items-center rounded-[var(--radius-button)] border border-[var(--color-border)] bg-[var(--color-surface)] px-4 text-sm font-medium text-[var(--color-text)] transition hover:border-[var(--color-accent)]/35 hover:text-[var(--color-accent)]">
            View all products
        </a>
    </div>

    <?php if (!empty($ads['shop_category'])): ?>
        <div class="mb-6">
            <?php $ad = $ads['shop_category']; include dirname(__DIR__) . '/partials/_managed-ad.php'; ?>
        </div>
    <?php endif; ?>

    <?php
    $countTree = static function (array $nodes) use (&$countTree): int {
        $count = count($nodes);
        foreach ($nodes as $node) {
            $count += $countTree($node['children'] ?? []);
        }

        return $count;
    };

    $renderNode = static function (array $node, int $depth = 0) use (&$renderNode): void {
        $children = $node['children'] ?? [];
        ?>
        <li>
            <a href="/shop?category=<?= urlencode($node['slug']) ?>"
               class="inline-flex items-center gap-2 rounded-md px-2 py-1.5 text-sm transition <?= $depth === 0 ? 'font-semibold text-[var(--color-text)] hover:text-[var(--color-accent)]' : 'text-[var(--color-muted)] hover:text-[var(--color-accent)] hover:bg-[var(--color-bg)]' ?>">
                <?php if ($depth === 0): ?>
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-md bg-[var(--color-accent)]/10 text-[var(--color-accent)]">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7a2 2 0 0 1 2-2h3l2 2h9a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                    </span>
                <?php endif; ?>
                <span class="<?= $depth > 0 ? 'truncate' : '' ?>"><?= htmlspecialchars($node['name']) ?></span>
            </a>

            <?php if (!empty($children)): ?>
                <ul class="ml-3 mt-1 space-y-0.5 border-l border-[var(--color-border)] pl-3">
                    <?php foreach ($children as $child): ?>
                        <?php $renderNode($child, $depth + 1); ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </li>
        <?php
    };
    ?>

    <?php if (!empty($categoryTree)): ?>
        <div class="mb-4 rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] px-4 py-3 text-sm text-[var(--color-muted)]" style="box-shadow: var(--shadow-sm)">
            <?= $countTree($categoryTree) ?> categories available
        </div>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <?php foreach ($categoryTree as $root): ?>
                <section class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-4" style="box-shadow: var(--shadow-sm)">
                    <a href="/shop?category=<?= urlencode($root['slug']) ?>" class="group inline-flex items-center gap-2 text-base font-semibold text-[var(--color-text)] hover:text-[var(--color-accent)] transition-colors">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-[var(--color-accent)]/10 text-[var(--color-accent)]">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7a2 2 0 0 1 2-2h3l2 2h9a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                        </span>
                        <span><?= htmlspecialchars($root['name']) ?></span>
                    </a>

                    <?php if (!empty($root['children'])): ?>
                        <ul class="mt-3 space-y-1">
                            <?php foreach ($root['children'] as $child): ?>
                                <?php $renderNode($child, 1); ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="mt-3 text-sm text-[var(--color-muted)]">No subcategories yet.</p>
                    <?php endif; ?>
                </section>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-4 text-sm text-[var(--color-muted)]" style="box-shadow: var(--shadow-sm)">
            No categories available.
        </div>
    <?php endif; ?>
</div>
