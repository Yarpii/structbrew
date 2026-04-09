<section class="bg-[var(--color-bg)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-12 md:py-16">
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-[var(--color-text)]">VAT & Invoices</h1>
        <p class="mt-3 text-[var(--color-muted)] max-w-2xl">Information about VAT, tax-exempt purchasing, and how to access your invoices.</p>

        <div class="mt-10 grid gap-6 md:grid-cols-2">
            <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-6">
                <h2 class="text-lg font-bold text-[var(--color-text)] mb-3">Invoices</h2>
                <ul class="space-y-2 text-sm text-[var(--color-muted)]">
                    <li class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#8226;</span> An invoice is automatically emailed when your order ships.</li>
                    <li class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#8226;</span> Download past invoices anytime from your <a href="/login" class="text-[var(--color-accent)] hover:underline">account dashboard</a>.</li>
                    <li class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#8226;</span> Invoices include itemized pricing, shipping costs, and applicable tax.</li>
                    <li class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#8226;</span> Need a modified or duplicate invoice? <a href="/contact" class="text-[var(--color-accent)] hover:underline">Contact us</a> with your order number.</li>
                </ul>
            </div>

            <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-6">
                <h2 class="text-lg font-bold text-[var(--color-text)] mb-3">VAT Information</h2>
                <ul class="space-y-2 text-sm text-[var(--color-muted)]">
                    <li class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#8226;</span> Prices displayed include VAT where applicable.</li>
                    <li class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#8226;</span> EU businesses with a valid VAT ID may qualify for VAT-exempt pricing.</li>
                    <li class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#8226;</span> Enter your VAT ID during checkout or in your account settings.</li>
                    <li class="flex items-start gap-2"><span class="text-[var(--color-accent)]">&#8226;</span> VAT ID is validated in real-time via the EU VIES database.</li>
                </ul>
            </div>
        </div>

        <div class="mt-8 rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-6">
            <h2 class="text-lg font-bold text-[var(--color-text)] mb-3">Tax rates by region</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-[var(--color-border)] text-left">
                            <th class="py-2 pr-4 font-semibold text-[var(--color-text)]">Region</th>
                            <th class="py-2 pr-4 font-semibold text-[var(--color-text)]">VAT / Tax Rate</th>
                            <th class="py-2 font-semibold text-[var(--color-text)]">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="text-[var(--color-muted)]">
                        <tr class="border-b border-[var(--color-border)]"><td class="py-2 pr-4">United States</td><td class="py-2 pr-4">Varies by state</td><td class="py-2">Sales tax calculated at checkout</td></tr>
                        <tr class="border-b border-[var(--color-border)]"><td class="py-2 pr-4">European Union</td><td class="py-2 pr-4">17% - 27%</td><td class="py-2">Based on destination country</td></tr>
                        <tr class="border-b border-[var(--color-border)]"><td class="py-2 pr-4">United Kingdom</td><td class="py-2 pr-4">20%</td><td class="py-2">Standard UK VAT rate</td></tr>
                        <tr><td class="py-2 pr-4">Rest of World</td><td class="py-2 pr-4">Varies</td><td class="py-2">Import duties may apply separately</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
