<section class="bg-[var(--color-bg)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-12 md:py-16">
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-[var(--color-text)]">Dealer Onboarding</h1>
        <p class="mt-3 text-[var(--color-muted)] max-w-2xl">Become an authorized Scooter Dynamics dealer and grow your tech retail business with our curated product range.</p>

        <div class="mt-10">
            <h2 class="text-xl font-bold text-[var(--color-text)] mb-6">How it works</h2>
            <div class="grid gap-4 md:grid-cols-4">
                <?php
                $steps = [
                    ['1', 'Apply', 'Submit your dealer application with business credentials and store details.'],
                    ['2', 'Review', 'Our partnerships team evaluates your application within 2-3 business days.'],
                    ['3', 'Onboard', 'Receive your dealer portal access, product catalog, and pricing sheet.'],
                    ['4', 'Sell', 'Start ordering at dealer rates and grow your product offering.'],
                ];
                foreach ($steps as $s): ?>
                    <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-5">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-[var(--color-accent)] text-sm font-bold text-white mb-3"><?= $s[0] ?></span>
                        <h3 class="text-base font-semibold text-[var(--color-text)]"><?= $s[1] ?></h3>
                        <p class="mt-1 text-sm text-[var(--color-muted)]"><?= $s[2] ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mt-10 rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-6">
            <h2 class="text-lg font-bold text-[var(--color-text)] mb-2">Dealer benefits</h2>
            <div class="grid gap-3 sm:grid-cols-2 mt-4 text-sm text-[var(--color-muted)]">
                <p class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#10003;</span> Up to 40% off retail pricing</p>
                <p class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#10003;</span> Marketing materials and product imagery</p>
                <p class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#10003;</span> Co-branded promotional campaigns</p>
                <p class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#10003;</span> Priority stock allocation on new releases</p>
                <p class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#10003;</span> Dedicated dealer support line</p>
                <p class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#10003;</span> Flexible NET-15 / NET-30 payment terms</p>
            </div>
            <a href="/b2b-contact" class="mt-5 inline-flex h-10 items-center gap-2 rounded-[var(--radius-button)] bg-[var(--color-accent)] px-5 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">
                Start Application
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
    </div>
</section>
