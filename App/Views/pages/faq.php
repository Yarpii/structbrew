<section class="bg-[var(--color-bg)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-12 md:py-16">
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-[var(--color-text)]">FAQ Hub</h1>
        <p class="mt-3 text-[var(--color-muted)] max-w-2xl">Quick answers to the most common questions about ordering, shipping, returns, and more.</p>

        <div class="mt-10 grid gap-8 lg:grid-cols-2">
            <?php
            $sections = [
                'Ordering' => [
                    ['How do I place an order?', 'Browse our shop, add items to your cart, and proceed to checkout. You can check out as a guest or create an account to track your orders.'],
                    ['Can I modify my order after placing it?', 'Orders can be modified within 1 hour of placement. After that, contact us and we\'ll do our best to accommodate changes before the order ships.'],
                    ['Do you offer gift wrapping?', 'Not at the moment, but we\'re working on it! All orders are shipped in branded packaging with care.'],
                ],
                'Shipping' => [
                    ['How long does shipping take?', 'Standard shipping takes 2-5 business days within the US. Express shipping (1-2 days) is available at checkout. International orders typically take 5-12 business days.'],
                    ['Do you ship internationally?', 'Yes! We ship to most countries worldwide. Shipping costs and delivery times vary by destination. See our <a href="/shipping-by-country" class="text-[var(--color-accent)] hover:underline">shipping by country</a> page for details.'],
                    ['Is shipping free?', 'Free standard shipping on orders over $50 within the US. International free shipping threshold is $150.'],
                ],
                'Returns & Refunds' => [
                    ['What is your return policy?', 'We offer a 30-day return policy on all items. Products must be unused and in original packaging. See our <a href="/returns-warranty" class="text-[var(--color-accent)] hover:underline">returns & warranty</a> page for full details.'],
                    ['How long do refunds take?', 'Refunds are processed within 3-5 business days after we receive the returned item. It may take an additional 5-10 days for the refund to appear on your statement.'],
                    ['Can I exchange an item?', 'Yes — contact us to arrange an exchange. We\'ll send the new item once the original is on its way back.'],
                ],
                'Account & Payment' => [
                    ['Do I need an account to order?', 'No, guest checkout is available. However, creating an account lets you track orders, save addresses, and access your invoice history.'],
                    ['What payment methods do you accept?', 'We accept Visa, Mastercard, Amex, PayPal, Apple Pay, Google Pay, Klarna, and bank transfer. See <a href="/payment-methods" class="text-[var(--color-accent)] hover:underline">all payment methods</a>.'],
                    ['Is my payment information secure?', 'Absolutely. All transactions are SSL encrypted and processed through a PCI DSS Level 1 compliant gateway. We never store your full card details.'],
                ],
            ];
            foreach ($sections as $heading => $faqs): ?>
                <div>
                    <h2 class="text-lg font-bold text-[var(--color-text)] mb-4"><?= $heading ?></h2>
                    <div class="space-y-3" x-data="{ open: null }">
                        <?php foreach ($faqs as $idx => $faq): ?>
                            <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] overflow-hidden">
                                <button @click="open === <?= $idx ?> ? open = null : open = <?= $idx ?>" class="flex w-full items-center justify-between p-4 text-left">
                                    <span class="text-sm font-semibold text-[var(--color-text)] pr-4"><?= $faq[0] ?></span>
                                    <svg :class="open === <?= $idx ?> && 'rotate-180'" class="shrink-0 w-4 h-4 text-[var(--color-muted)] transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                                </button>
                                <div x-show="open === <?= $idx ?>" x-cloak x-collapse class="px-4 pb-4">
                                    <p class="text-sm text-[var(--color-muted)] leading-relaxed"><?= $faq[1] ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-10 rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-6 text-center">
            <h2 class="text-lg font-bold text-[var(--color-text)] mb-2">Can't find what you're looking for?</h2>
            <p class="text-sm text-[var(--color-muted)] mb-4">Our support team is happy to help with any questions.</p>
            <a href="/contact" class="inline-flex h-10 items-center gap-2 rounded-[var(--radius-button)] bg-[var(--color-accent)] px-5 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">
                Contact Us
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
    </div>
</section>
