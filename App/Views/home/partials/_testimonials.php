<!-- Testimonials -->
<section class="bg-[var(--color-bg)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-14">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-[var(--color-text)]">What Our Customers Say</h2>
            <p class="mt-2 text-sm text-[var(--color-muted)]">Real reviews from real people.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-5">
            <?php
            $testimonials = [
                ['name' => 'Sarah K.', 'role' => 'Remote Worker', 'text' => 'The noise-cancelling headphones are a game-changer for my home office. Crystal clear audio and the battery lasts all week. Best tech purchase this year.', 'rating' => 5],
                ['name' => 'Marcus T.', 'role' => 'Gamer', 'text' => 'Bought the mechanical keyboard and gaming mouse combo. The build quality is insane for the price. Fast shipping too — arrived in 2 days.', 'rating' => 5],
                ['name' => 'Lisa M.', 'role' => 'Content Creator', 'text' => 'The 4K webcam and ultrawide monitor completely upgraded my streaming setup. Customer support helped me pick the right specs. Will definitely buy again.', 'rating' => 4],
            ];
            foreach ($testimonials as $t): ?>
                <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-6" style="box-shadow: var(--shadow-sm)">
                    <div class="flex text-amber-400 mb-3">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="<?= $i < $t['rating'] ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        <?php endfor; ?>
                    </div>
                    <p class="text-sm text-[var(--color-muted)] leading-relaxed mb-4">"<?= htmlspecialchars($t['text']) ?>"</p>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-[var(--color-accent)]/10 flex items-center justify-center text-[var(--color-accent)] text-sm font-bold">
                            <?= $t['name'][0] ?>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-[var(--color-text)]"><?= htmlspecialchars($t['name']) ?></p>
                            <p class="text-xs text-[var(--color-muted)]"><?= htmlspecialchars($t['role']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
