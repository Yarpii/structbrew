<section class="bg-[var(--color-bg)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-12 md:py-16">
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-[var(--color-text)]">Advertise with Us</h1>
        <p class="mt-3 text-[var(--color-muted)] max-w-2xl">Reach thousands of tech enthusiasts every month. Feature your brand or products across our store and marketing channels.</p>

        <div class="mt-10 grid gap-4 md:grid-cols-3">
            <?php
            $plans = [
                ['Featured Banner', '$299/mo', 'Homepage banner placement with up to 50K monthly impressions. Includes A/B testing and click-through reporting.'],
                ['Product Spotlight', '$149/mo', 'Dedicated product highlight in our newsletter and category pages. Includes featured badge on product cards.'],
                ['Brand Partnership', 'Custom', 'Co-branded campaigns, sponsored content, and social media features. Tailored to your brand goals and budget.'],
            ];
            foreach ($plans as $p): ?>
                <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-6 flex flex-col">
                    <h2 class="text-lg font-bold text-[var(--color-text)]"><?= $p[0] ?></h2>
                    <p class="text-2xl font-extrabold text-[var(--color-accent)] mt-2"><?= $p[1] ?></p>
                    <p class="mt-3 text-sm text-[var(--color-muted)] flex-1"><?= $p[2] ?></p>
                    <a href="/b2b-contact" class="mt-5 inline-flex h-10 items-center justify-center rounded-[var(--radius-button)] border border-[var(--color-accent)] text-sm font-semibold text-[var(--color-accent)] transition hover:bg-[var(--color-accent)] hover:text-white">
                        Get Started
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-10 rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-6">
            <h2 class="text-lg font-bold text-[var(--color-text)] mb-4">Audience snapshot</h2>
            <div class="grid gap-4 sm:grid-cols-4 text-center">
                <div><p class="text-2xl font-extrabold text-[var(--color-text)]">50K+</p><p class="text-sm text-[var(--color-muted)]">Monthly visitors</p></div>
                <div><p class="text-2xl font-extrabold text-[var(--color-text)]">12K+</p><p class="text-sm text-[var(--color-muted)]">Newsletter subscribers</p></div>
                <div><p class="text-2xl font-extrabold text-[var(--color-text)]">18-45</p><p class="text-sm text-[var(--color-muted)]">Core age range</p></div>
                <div><p class="text-2xl font-extrabold text-[var(--color-text)]">4.2 min</p><p class="text-sm text-[var(--color-muted)]">Avg. session duration</p></div>
            </div>
        </div>
    </div>
</section>
