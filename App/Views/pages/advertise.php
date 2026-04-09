<section class="bg-[var(--color-bg)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-12 md:py-16 space-y-8">
        <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-6 md:p-8" style="box-shadow: var(--shadow-sm)">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[var(--color-accent)]">B2B Advertising</p>
            <h1 class="mt-2 text-3xl md:text-4xl font-extrabold tracking-tight text-[var(--color-text)]">Advertise with Us</h1>
            <p class="mt-3 max-w-3xl text-[var(--color-muted)]">Promote your brand to a high-intent audience actively shopping for performance parts, accessories and upgrades. We offer transparent placements, measurable reporting and flexible campaign formats.</p>

            <div class="mt-6 flex flex-wrap gap-3">
                <a href="/b2b-contact" class="inline-flex h-10 items-center justify-center rounded-md bg-[var(--color-accent)] px-5 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">Request Media Kit</a>
                <a href="/priority-support" class="inline-flex h-10 items-center justify-center rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-5 text-sm font-semibold text-[var(--color-text)] transition hover:border-[var(--color-accent)] hover:text-[var(--color-accent)]">Talk to Partnerships</a>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-5 text-center" style="box-shadow: var(--shadow-sm)">
                <p class="text-2xl font-extrabold text-[var(--color-text)]">50K+</p>
                <p class="mt-1 text-sm text-[var(--color-muted)]">Monthly visitors</p>
            </div>
            <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-5 text-center" style="box-shadow: var(--shadow-sm)">
                <p class="text-2xl font-extrabold text-[var(--color-text)]">12K+</p>
                <p class="mt-1 text-sm text-[var(--color-muted)]">Newsletter subscribers</p>
            </div>
            <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-5 text-center" style="box-shadow: var(--shadow-sm)">
                <p class="text-2xl font-extrabold text-[var(--color-text)]">4.2 min</p>
                <p class="mt-1 text-sm text-[var(--color-muted)]">Average session</p>
            </div>
            <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-5 text-center" style="box-shadow: var(--shadow-sm)">
                <p class="text-2xl font-extrabold text-[var(--color-text)]">Global</p>
                <p class="mt-1 text-sm text-[var(--color-muted)]">Shipping audience</p>
            </div>
        </div>

        <div class="space-y-4">
            <h2 class="text-xl font-bold text-[var(--color-text)]">Advertising Packages</h2>
            <div class="grid gap-4 md:grid-cols-3">
                <?php
                $plans = [
                    [
                        'title' => 'Featured Banner',
                        'price' => '$299/mo',
                        'desc' => 'Homepage hero placement with high visibility for launches and promotions.',
                        'items' => ['Up to 50K monthly impressions', 'CTR and view reporting', 'Creative refresh 1x/month'],
                    ],
                    [
                        'title' => 'Product Spotlight',
                        'price' => '$149/mo',
                        'desc' => 'Targeted product feature across category pages and campaign email placements.',
                        'items' => ['Newsletter highlight slot', 'Featured badge on product card', 'Campaign performance summary'],
                    ],
                    [
                        'title' => 'Brand Partnership',
                        'price' => 'Custom',
                        'desc' => 'Multi-channel campaigns built around your objectives and seasonal roadmap.',
                        'items' => ['Co-branded campaign concept', 'Store + social rollout', 'Dedicated account coordination'],
                    ],
                ];
                foreach ($plans as $plan): ?>
                    <article class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-6 flex flex-col" style="box-shadow: var(--shadow-sm)">
                        <h3 class="text-lg font-bold text-[var(--color-text)]"><?= htmlspecialchars($plan['title']) ?></h3>
                        <p class="mt-2 text-2xl font-extrabold text-[var(--color-accent)]"><?= htmlspecialchars($plan['price']) ?></p>
                        <p class="mt-3 text-sm text-[var(--color-muted)]"><?= htmlspecialchars($plan['desc']) ?></p>
                        <ul class="mt-4 space-y-2 text-sm text-[var(--color-text)] flex-1">
                            <?php foreach ($plan['items'] as $item): ?>
                                <li class="flex items-start gap-2">
                                    <span class="mt-[6px] inline-block h-1.5 w-1.5 rounded-full bg-[var(--color-accent)]"></span>
                                    <span><?= htmlspecialchars($item) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="/b2b-contact" class="mt-5 inline-flex h-10 items-center justify-center rounded-md border border-[var(--color-accent)] text-sm font-semibold text-[var(--color-accent)] transition hover:bg-[var(--color-accent)] hover:text-white">Get Started</a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-6" style="box-shadow: var(--shadow-sm)">
            <h2 class="text-xl font-bold text-[var(--color-text)]">How it works</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-3">
                <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] p-4">
                    <p class="text-sm font-semibold text-[var(--color-text)]">1. Share your goals</p>
                    <p class="mt-1 text-sm text-[var(--color-muted)]">Tell us your product focus, target market and campaign timing.</p>
                </div>
                <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] p-4">
                    <p class="text-sm font-semibold text-[var(--color-text)]">2. Approve placement plan</p>
                    <p class="mt-1 text-sm text-[var(--color-muted)]">We propose channels, expected visibility and creative requirements.</p>
                </div>
                <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] p-4">
                    <p class="text-sm font-semibold text-[var(--color-text)]">3. Launch and report</p>
                    <p class="mt-1 text-sm text-[var(--color-muted)]">Campaign goes live with periodic reporting and optimization feedback.</p>
                </div>
            </div>
        </div>
    </div>
</section>
