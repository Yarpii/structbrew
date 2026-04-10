<?php
$flashSuccess = $flashSuccess ?? null;
$flashError   = $flashError ?? null;
$csrfToken    = $csrfToken ?? '';
?>
<section class="bg-[var(--color-bg)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-12 md:py-16 space-y-8">
        <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-6 md:p-8" style="box-shadow: var(--shadow-sm)">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[var(--color-accent)]">Partner Program</p>
            <h1 class="mt-2 text-3xl md:text-4xl font-extrabold tracking-tight text-[var(--color-text)]">Dealer Onboarding</h1>
            <p class="mt-3 max-w-3xl text-[var(--color-muted)]">Become an authorized Scooter Dynamics dealer and scale your catalog with structured pricing, product support and operational onboarding.</p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="/contact" class="inline-flex h-10 items-center justify-center rounded-md bg-[var(--color-accent)] px-5 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">Start Application</a>
                <a href="/wholesale-partnerships" class="inline-flex h-10 items-center justify-center rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-5 text-sm font-semibold text-[var(--color-text)] transition hover:border-[var(--color-accent)] hover:text-[var(--color-accent)]">View Wholesale Info</a>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.2fr_1fr]">
            <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-6" style="box-shadow: var(--shadow-sm)">
                <h2 class="text-xl font-bold text-[var(--color-text)]">Onboarding Process</h2>
                <div class="mt-4 grid gap-4 md:grid-cols-2">
                    <?php
                    $steps = [
                        ['01', 'Apply', 'Submit your company details, business credentials and sales channels.'],
                        ['02', 'Review', 'Partnership team reviews fit, market positioning and operational readiness.'],
                        ['03', 'Activate', 'Receive account setup, pricing access and onboarding documentation.'],
                        ['04', 'Launch', 'Start placing orders and scale with support from account and ops teams.'],
                    ];
                    foreach ($steps as $step): ?>
                        <article class="rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] p-4">
                            <div class="inline-flex h-7 min-w-[2.5rem] items-center justify-center rounded-md bg-[var(--color-accent)] px-2 text-xs font-bold text-white"><?= htmlspecialchars($step[0]) ?></div>
                            <h3 class="mt-3 text-base font-semibold text-[var(--color-text)]"><?= htmlspecialchars($step[1]) ?></h3>
                            <p class="mt-1 text-sm text-[var(--color-muted)]"><?= htmlspecialchars($step[2]) ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-6" style="box-shadow: var(--shadow-sm)">
                <h2 class="text-xl font-bold text-[var(--color-text)]">Minimum Requirements</h2>
                <ul class="mt-3 space-y-2 text-sm text-[var(--color-muted)]">
                    <li>• Registered business entity with valid VAT/tax information</li>
                    <li>• Active sales channel (storefront, webshop, or workshop)</li>
                    <li>• Ability to manage after-sales support for your customers</li>
                    <li>• Commitment to brand and pricing policy compliance</li>
                </ul>
                <p class="mt-4 text-xs text-[var(--color-muted)]">Applications are typically reviewed within 2–3 business days.</p>
            </div>
        </div>

        <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-6" style="box-shadow: var(--shadow-sm)">
            <h2 class="text-xl font-bold text-[var(--color-text)]">Dealer Benefits</h2>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3 text-sm text-[var(--color-muted)]">
                <p class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#10003;</span> Structured dealer pricing and margin support</p>
                <p class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#10003;</span> Product assets and sales-ready content</p>
                <p class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#10003;</span> Priority allocation on selected launches</p>
                <p class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#10003;</span> Dedicated support workflow and escalation path</p>
                <p class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#10003;</span> Optional co-marketing campaign support</p>
                <p class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#10003;</span> Business payment terms for approved accounts</p>
            </div>
            <div class="mt-6">
                <a href="/contact" class="inline-flex h-10 items-center gap-2 rounded-md bg-[var(--color-accent)] px-5 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">
                    Apply Now
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Apply CTA -->
<section class="border-t border-[var(--color-border)] bg-[var(--color-bg)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-12">
        <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-6 md:p-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between" style="box-shadow: var(--shadow-sm)">
            <div>
                <h2 class="text-lg font-bold text-[var(--color-text)]">Ready to apply?</h2>
                <p class="mt-1 text-sm text-[var(--color-muted)]">Fill out the dealer application on our contact page. Our B2B team reviews every application within 2&ndash;3 business days.</p>
            </div>
            <a href="/contact" class="shrink-0 inline-flex h-10 items-center gap-2 rounded-md bg-[var(--color-accent)] px-6 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">
                Apply Now
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
    </div>
</section>
