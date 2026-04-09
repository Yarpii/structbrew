<section class="bg-[var(--color-bg)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-12 md:py-16 space-y-8">
        <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-6 md:p-8" style="box-shadow: var(--shadow-sm)">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[var(--color-accent)]">Checkout & Billing</p>
            <h1 class="mt-2 text-3xl md:text-4xl font-extrabold tracking-tight text-[var(--color-text)]">Payment Methods</h1>
            <p class="mt-3 max-w-3xl text-[var(--color-muted)]">Choose from secure payment options for retail and business orders. All transactions are processed through encrypted and monitored payment channels.</p>
        </div>

        <div class="space-y-4">
            <h2 class="text-xl font-bold text-[var(--color-text)]">Available Payment Options</h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <?php
                $methods = [
                    ['Credit & Debit Cards', 'Visa, Mastercard, American Express, and Maestro via PCI-compliant gateway.', 'Cards'],
                    ['PayPal', 'Pay with PayPal balance, linked bank account, or card with buyer protection.', 'PayPal'],
                    ['Apple Pay & Google Pay', 'Fast checkout on supported devices using tokenized card details.', 'Wallet'],
                    ['Klarna', 'Flexible pay-later options where available, subject to provider approval.', 'Klarna'],
                    ['Bank Transfer', 'Available for eligible higher-value orders. Dispatch starts after cleared funds.', 'Bank'],
                    ['Gift Cards', 'Store gift cards can be applied at checkout and combined with other methods.', 'Gift'],
                ];
                foreach ($methods as $method): ?>
                    <article class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-5" style="box-shadow: var(--shadow-sm)">
                        <div class="mb-3 flex items-center gap-3">
                            <span class="inline-flex h-8 min-w-[3.5rem] items-center justify-center rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-2 text-xs font-semibold text-[var(--color-muted)]"><?= htmlspecialchars($method[2]) ?></span>
                            <h3 class="text-base font-semibold text-[var(--color-text)]"><?= htmlspecialchars($method[0]) ?></h3>
                        </div>
                        <p class="text-sm text-[var(--color-muted)]"><?= htmlspecialchars($method[1]) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.2fr_1fr]">
            <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-6" style="box-shadow: var(--shadow-sm)">
                <h2 class="text-xl font-bold text-[var(--color-text)]">Security & Fraud Protection</h2>
                <div class="mt-4 grid gap-3 sm:grid-cols-2 text-sm text-[var(--color-muted)]">
                    <p class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#10003;</span> SSL/TLS encryption on all transactions</p>
                    <p class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#10003;</span> 3D Secure authentication for supported card flows</p>
                    <p class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#10003;</span> PCI DSS aligned payment processing</p>
                    <p class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#10003;</span> Real-time fraud screening before order approval</p>
                </div>
            </div>

            <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-6" style="box-shadow: var(--shadow-sm)">
                <h2 class="text-xl font-bold text-[var(--color-text)]">Billing Notes</h2>
                <ul class="mt-3 space-y-2 text-sm text-[var(--color-muted)]">
                    <li>• Currency is shown at checkout before payment confirmation.</li>
                    <li>• Failed or canceled authorizations are automatically released by your bank.</li>
                    <li>• VAT invoice availability depends on account and order details.</li>
                    <li>• Some methods can be region or device dependent.</li>
                </ul>
                <div class="mt-5 space-y-2">
                    <a href="/vat-invoices" class="inline-flex h-9 w-full items-center justify-center rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] text-sm font-semibold text-[var(--color-text)] transition hover:border-[var(--color-accent)] hover:text-[var(--color-accent)]">VAT & Invoices</a>
                    <a href="/support" class="inline-flex h-9 w-full items-center justify-center rounded-md border border-[var(--color-accent)] text-sm font-semibold text-[var(--color-accent)] transition hover:bg-[var(--color-accent)] hover:text-white">Need Payment Help?</a>
                </div>
            </div>
        </div>
    </div>
</section>
