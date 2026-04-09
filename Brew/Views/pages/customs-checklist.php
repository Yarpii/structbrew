<section class="bg-[var(--color-bg)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-12 md:py-16">
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-[var(--color-text)]">Customs Checklist</h1>
        <p class="mt-3 text-[var(--color-muted)] max-w-2xl">A quick reference to help international customers prepare for customs clearance on their orders.</p>

        <div class="mt-10 rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-6" x-data="{ checks: [false,false,false,false,false,false,false] }">
            <h2 class="text-lg font-bold text-[var(--color-text)] mb-5">Before your order arrives</h2>
            <div class="space-y-3">
                <?php
                $items = [
                    'Check if your country has a de minimis threshold (duty-free limit) for imports.',
                    'Look up estimated duty rates for electronics/accessories on your customs website.',
                    'Ensure your shipping address is complete, including postal code and phone number.',
                    'Have a valid ID ready — some carriers require it for customs-cleared deliveries.',
                    'Check if your country restricts lithium battery imports (applies to some electronics).',
                    'Know your preferred payment method for any duties on delivery (cash, card, or online).',
                    'Save your order confirmation email — it contains the commercial invoice if needed.',
                ];
                foreach ($items as $idx => $item): ?>
                    <label class="flex items-start gap-3 cursor-pointer group">
                        <input type="checkbox" x-model="checks[<?= $idx ?>]" class="mt-0.5 h-5 w-5 rounded border-[var(--color-border)] text-[var(--color-accent)] focus:ring-[var(--color-accent)]/20">
                        <span class="text-sm text-[var(--color-muted)] group-hover:text-[var(--color-text)] transition-colors" :class="checks[<?= $idx ?>] && 'line-through opacity-60'"><?= $item ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <div class="mt-5 pt-4 border-t border-[var(--color-border)]">
                <p class="text-sm text-[var(--color-muted)]">
                    <span x-text="checks.filter(Boolean).length"></span> of <?= count($items) ?> completed
                </p>
            </div>
        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-2">
            <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-5">
                <h3 class="text-base font-semibold text-[var(--color-text)] mb-2">Avoid delays</h3>
                <p class="text-sm text-[var(--color-muted)]">Incomplete addresses and missing phone numbers are the #1 cause of customs delays. Double-check your details before ordering.</p>
            </div>
            <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-5">
                <h3 class="text-base font-semibold text-[var(--color-text)] mb-2">Related pages</h3>
                <ul class="text-sm space-y-1">
                    <li><a href="/customs-duties" class="text-[var(--color-accent)] hover:underline">Customs & Duties FAQ</a></li>
                    <li><a href="/incoterms" class="text-[var(--color-accent)] hover:underline">Incoterms: DAP & DDP</a></li>
                    <li><a href="/shipping-restrictions" class="text-[var(--color-accent)] hover:underline">Shipping Restrictions</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>
