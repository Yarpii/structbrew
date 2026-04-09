<section class="bg-[var(--color-bg)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-12 md:py-16">
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-[var(--color-text)]">Shipping by Country</h1>
        <p class="mt-3 text-[var(--color-muted)] max-w-2xl">We ship worldwide. Find estimated delivery times and costs for your region below.</p>

        <div class="mt-10 rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-[var(--color-border)] bg-[var(--color-bg)]">
                            <th class="py-3 px-4 text-left font-semibold text-[var(--color-text)]">Region</th>
                            <th class="py-3 px-4 text-left font-semibold text-[var(--color-text)]">Standard</th>
                            <th class="py-3 px-4 text-left font-semibold text-[var(--color-text)]">Express</th>
                            <th class="py-3 px-4 text-left font-semibold text-[var(--color-text)]">Free Shipping</th>
                        </tr>
                    </thead>
                    <tbody class="text-[var(--color-muted)]">
                        <?php
                        $rows = [
                            ['United States', '3-5 days / $4.99', '1-2 days / $12.99', 'Orders over $50'],
                            ['Canada', '5-8 days / $9.99', '2-4 days / $19.99', 'Orders over $100'],
                            ['United Kingdom', '5-7 days / $8.99', '2-3 days / $18.99', 'Orders over $100'],
                            ['Germany, France, Netherlands', '4-7 days / $7.99', '2-3 days / $16.99', 'Orders over $100'],
                            ['Rest of EU', '5-10 days / $9.99', '3-5 days / $19.99', 'Orders over $150'],
                            ['Australia / New Zealand', '7-12 days / $14.99', '4-6 days / $29.99', 'Orders over $150'],
                            ['Japan / South Korea', '6-10 days / $12.99', '3-5 days / $24.99', 'Orders over $150'],
                            ['Rest of World', '10-18 days / $14.99', '5-8 days / $29.99', 'Orders over $200'],
                        ];
                        foreach ($rows as $r): ?>
                            <tr class="border-b border-[var(--color-border)]">
                                <td class="py-3 px-4 font-medium text-[var(--color-text)]"><?= $r[0] ?></td>
                                <td class="py-3 px-4"><?= $r[1] ?></td>
                                <td class="py-3 px-4"><?= $r[2] ?></td>
                                <td class="py-3 px-4"><?= $r[3] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-2">
            <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-5">
                <h2 class="text-base font-semibold text-[var(--color-text)] mb-2">Tracking</h2>
                <p class="text-sm text-[var(--color-muted)]">All orders include tracking. You'll receive an email with your tracking number as soon as your order ships.</p>
            </div>
            <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-5">
                <h2 class="text-base font-semibold text-[var(--color-text)] mb-2">Customs & duties</h2>
                <p class="text-sm text-[var(--color-muted)]">International orders may be subject to customs duties and import taxes. See our <a href="/customs-duties" class="text-[var(--color-accent)] hover:underline">customs & duties FAQ</a>.</p>
            </div>
        </div>
    </div>
</section>
