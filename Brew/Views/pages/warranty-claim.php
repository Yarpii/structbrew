<section class="bg-[var(--color-bg)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-12 md:py-16">
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-[var(--color-text)]">Warranty Claim Checklist</h1>
        <p class="mt-3 text-[var(--color-muted)] max-w-2xl">Submitting a warranty claim? Make sure you have everything ready for a smooth and fast process.</p>

        <div class="mt-10 grid gap-8 lg:grid-cols-[1fr,22rem]">
            <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-6" x-data="{ checks: [false,false,false,false,false,false] }">
                <h2 class="text-lg font-bold text-[var(--color-text)] mb-5">Before you submit</h2>
                <div class="space-y-3">
                    <?php
                    $items = [
                        'Confirm the product is within the 12-month warranty period (check your order date).',
                        'Verify the issue is a manufacturing defect — not physical damage, water damage, or normal wear.',
                        'Have your order number ready (found in your order confirmation email or account dashboard).',
                        'Take clear photos of the defect — close-up shots showing the issue plus an overview of the whole product.',
                        'Write a brief description of the problem: when it started, what happens, and any troubleshooting you\'ve tried.',
                        'Check our <a href="/warranty-exclusions" class="text-[var(--color-accent)] hover:underline">warranty exclusions</a> page to make sure your issue is covered.',
                    ];
                    foreach ($items as $idx => $item): ?>
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <input type="checkbox" x-model="checks[<?= $idx ?>]" class="mt-0.5 h-5 w-5 rounded border-[var(--color-border)] text-[var(--color-accent)] focus:ring-[var(--color-accent)]/20">
                            <span class="text-sm text-[var(--color-muted)] group-hover:text-[var(--color-text)] transition-colors" :class="checks[<?= $idx ?>] && 'line-through opacity-60'"><?= $item ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="mt-5 pt-4 border-t border-[var(--color-border)] flex items-center justify-between">
                    <p class="text-sm text-[var(--color-muted)]"><span x-text="checks.filter(Boolean).length"></span> of <?= count($items) ?> completed</p>
                    <a href="/contact" class="inline-flex h-10 items-center gap-2 rounded-[var(--radius-button)] bg-[var(--color-accent)] px-5 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">
                        Submit Claim
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>
            </div>

            <div class="space-y-4">
                <div class="rounded-[var,--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-5">
                    <h3 class="text-sm font-bold text-[var(--color-text)] mb-2">What happens next?</h3>
                    <ol class="space-y-2 text-sm text-[var(--color-muted)] list-decimal list-inside">
                        <li>We review your claim within 2 business days.</li>
                        <li>If approved, we arrange a repair, replacement, or refund.</li>
                        <li>You may be asked to return the item (prepaid label provided).</li>
                    </ol>
                </div>
                <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-5">
                    <h3 class="text-sm font-bold text-[var(--color-text)] mb-2">Contact</h3>
                    <p class="text-sm text-[var(--color-muted)]">warranty@scooterdynamics.store</p>
                    <p class="text-sm text-[var(--color-muted)]">+1 800 555 1234 opt. 3</p>
                </div>
            </div>
        </div>
    </div>
</section>
