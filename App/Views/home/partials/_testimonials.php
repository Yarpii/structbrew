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
                ['name' => 'Jamie R.', 'role' => 'Daily Commuter', 'text' => 'Ordered a replacement throttle cable and brake pads — both fit perfectly on my first attempt. Shipping was fast and the packaging kept everything safe. Will be my go-to for parts from now on.', 'rating' => 5],
                ['name' => 'Marcus T.', 'role' => 'Scooter Enthusiast', 'text' => 'Picked up a performance variator kit and new drive belt. The quality is excellent and the price beat every other site I checked. Had my scooter running like new within an hour.', 'rating' => 5],
                ['name' => 'Lisa M.', 'role' => 'Mechanic', 'text' => 'I order parts here for multiple customer scooters every week. The stock is reliable, descriptions are accurate, and the customer support team helped me track down a hard-to-find carburettor jet. Highly recommended.', 'rating' => 5],
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
                            <?= htmlspecialchars(substr((string) ($t['name'] ?? ''), 0, 1)) ?>
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
