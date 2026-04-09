<section class="bg-[var(--color-bg)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-12 md:py-16">
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-[var(--color-text)]">Customs & Duties FAQ</h1>
        <p class="mt-3 text-[var(--color-muted)] max-w-2xl">Everything you need to know about import duties, taxes, and customs clearance on international orders.</p>

        <div class="mt-10 space-y-4" x-data="{ open: null }">
            <?php
            $faqs = [
                ['Will I have to pay customs duties?', 'If your order ships under DAP terms (the default for most international orders), you may need to pay import duties and taxes when the package arrives in your country. These fees are set by your local customs authority and vary by product category and value. If you choose DDP shipping where available, all duties are prepaid.'],
                ['How much will duties cost?', 'Duty rates vary by country and product type, typically ranging from 0% to 20% of the declared value. Your country\'s customs website will have the most accurate rates. As a rough guide, electronics usually fall in the 0-5% range in most countries.'],
                ['Who collects the duties?', 'Under DAP shipping, the delivery carrier (DHL, FedEx, UPS, or your local postal service) collects duties on behalf of customs before delivering your package. They may also charge a small brokerage fee ($5-15) for processing the customs clearance.'],
                ['Can I avoid paying duties?', 'Many countries have a de minimis threshold — orders below a certain value are duty-free. For example, the US threshold is $800, Australia is AUD 1,000, and the UK is £135. Alternatively, choose DDP shipping to have duties included in your checkout total.'],
                ['What if I refuse to pay duties?', 'If you refuse to pay the duties on delivery, the package will be returned to us. You\'ll receive a refund minus the original and return shipping costs. We recommend checking estimated duties before ordering.'],
                ['Do you provide customs documentation?', 'Yes. All international shipments include a commercial invoice and customs declaration with accurate product descriptions and values. These are attached to the outside of the package and included electronically in the carrier\'s system.'],
                ['My package is held in customs — what do I do?', 'Customs holds usually resolve within 1-3 business days. If the carrier contacts you for additional documentation, respond promptly. If your package has been held for more than 5 business days, <a href="/contact" class="text-[var(--color-accent)] hover:underline">contact us</a> and we\'ll help investigate.'],
            ];
            foreach ($faqs as $idx => $faq): ?>
                <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] overflow-hidden">
                    <button @click="open === <?= $idx ?> ? open = null : open = <?= $idx ?>" class="flex w-full items-center justify-between p-5 text-left">
                        <span class="text-sm font-semibold text-[var(--color-text)] pr-4"><?= $faq[0] ?></span>
                        <svg :class="open === <?= $idx ?> && 'rotate-180'" class="shrink-0 w-4 h-4 text-[var(--color-muted)] transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div x-show="open === <?= $idx ?>" x-cloak x-collapse class="px-5 pb-5">
                        <p class="text-sm text-[var(--color-muted)] leading-relaxed"><?= $faq[1] ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-8 flex flex-wrap gap-3">
            <a href="/incoterms" class="inline-flex h-10 items-center rounded-[var(--radius-button)] border border-[var(--color-border)] bg-[var(--color-surface)] px-5 text-sm font-semibold text-[var(--color-text)] transition hover:border-[var(--color-accent)] hover:text-[var(--color-accent)]">Learn about DAP vs DDP</a>
            <a href="/customs-checklist" class="inline-flex h-10 items-center rounded-[var(--radius-button)] border border-[var(--color-border)] bg-[var(--color-surface)] px-5 text-sm font-semibold text-[var(--color-text)] transition hover:border-[var(--color-accent)] hover:text-[var(--color-accent)]">Customs Checklist</a>
        </div>
    </div>
</section>
