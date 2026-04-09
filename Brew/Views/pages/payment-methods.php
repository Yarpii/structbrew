<section class="bg-[var(--color-bg)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-12 md:py-16">
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-[var(--color-text)]">Payment Methods</h1>
        <p class="mt-3 text-[var(--color-muted)] max-w-2xl">We offer a range of secure payment options so you can choose the one that suits you best.</p>

        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <?php
            $methods = [
                ['Credit & Debit Cards', 'Visa, Mastercard, American Express, and Maestro. Processed securely through our PCI-compliant payment gateway.', 'VISA / MC'],
                ['PayPal', 'Pay with your PayPal balance, linked bank account, or card. Buyer protection included on all purchases.', 'PayPal'],
                ['Apple Pay & Google Pay', 'Fast checkout on supported devices. Your card details are never shared with us.', 'Wallet'],
                ['Klarna', 'Split your purchase into 3 interest-free installments, or pay within 30 days.', 'Klarna'],
                ['Bank Transfer', 'Direct bank transfer available for orders over $100. Order ships after funds are received.', 'Bank'],
                ['Gift Cards', 'Scooter Dynamics gift cards can be applied at checkout. Combine with other payment methods.', 'Gift'],
            ];
            foreach ($methods as $m): ?>
                <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="inline-flex h-9 w-14 items-center justify-center rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] text-xs font-bold text-[var(--color-muted)]"><?= $m[2] ?></span>
                        <h2 class="text-base font-semibold text-[var(--color-text)]"><?= $m[0] ?></h2>
                    </div>
                    <p class="text-sm text-[var(--color-muted)]"><?= $m[1] ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-10 rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-6">
            <h2 class="text-lg font-bold text-[var(--color-text)] mb-3">Security & fraud protection</h2>
            <div class="grid gap-3 sm:grid-cols-2 text-sm text-[var(--color-muted)]">
                <p class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#10003;</span> All transactions are SSL encrypted</p>
                <p class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#10003;</span> 3D Secure authentication on card payments</p>
                <p class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#10003;</span> PCI DSS Level 1 compliant payment processing</p>
                <p class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#10003;</span> Real-time fraud monitoring on every order</p>
            </div>
        </div>
    </div>
</section>
