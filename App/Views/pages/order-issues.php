<section class="bg-[var(--color-bg)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-12 md:py-16">
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-[var(--color-text)]">Order Issues Help</h1>
        <p class="mt-3 text-[var(--color-muted)] max-w-2xl">Something wrong with your order? Find quick answers below or reach out to our support team.</p>

        <div class="mt-10 space-y-4" x-data="{ open: null }">
            <?php
            $issues = [
                ['I haven\'t received my order', 'Most orders are delivered within 2-5 business days. Check your tracking link in the shipping confirmation email. If your tracking shows "delivered" but you haven\'t received the package, check with neighbors or your building\'s mail room. If still missing, contact us within 7 days and we\'ll investigate with the carrier.'],
                ['My order arrived damaged', 'We\'re sorry about that. Take photos of the damaged item and packaging, then contact us within 48 hours of delivery. We\'ll arrange a free replacement or full refund — no need to ship the damaged item back in most cases.'],
                ['I received the wrong item', 'Please contact us with your order number and a photo of the item you received. We\'ll send the correct item right away and provide a prepaid label for the wrong one.'],
                ['I want to cancel my order', 'Orders can be cancelled within 1 hour of placement. After that, the order may already be in processing. Contact us as soon as possible and we\'ll do our best to stop the shipment. If already shipped, you can return it under our 30-day return policy.'],
                ['My discount code didn\'t apply', 'Make sure the code is entered exactly as shown (codes are case-sensitive). Check that the items in your cart meet the promotion requirements. Some codes exclude sale items or specific categories. If the code should work, contact us and we\'ll apply the discount manually.'],
                ['I need to change my shipping address', 'Contact us immediately with your order number and the correct address. If the order hasn\'t shipped yet, we can update it. Once shipped, we cannot redirect the package — but we can help arrange a return and reship.'],
            ];
            foreach ($issues as $idx => $issue): ?>
                <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] overflow-hidden">
                    <button @click="open === <?= $idx ?> ? open = null : open = <?= $idx ?>" class="flex w-full items-center justify-between p-5 text-left">
                        <span class="text-sm font-semibold text-[var(--color-text)]"><?= htmlspecialchars($issue[0]) ?></span>
                        <svg :class="open === <?= $idx ?> && 'rotate-180'" class="shrink-0 w-5 h-5 text-[var(--color-muted)] transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div x-show="open === <?= $idx ?>" x-cloak x-collapse class="px-5 pb-5">
                        <p class="text-sm text-[var(--color-muted)] leading-relaxed"><?= $issue[1] ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-10 rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-6">
            <h2 class="text-lg font-bold text-[var(--color-text)] mb-2">Still need help?</h2>
            <p class="text-sm text-[var(--color-muted)] mb-4">Our support team is available 7 days a week to help resolve any order issues.</p>
            <a href="/contact" class="inline-flex h-10 items-center gap-2 rounded-[var(--radius-button)] bg-[var(--color-accent)] px-5 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">
                Contact Support
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
    </div>
</section>
