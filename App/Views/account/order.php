<?php
$statusClasses = [
    'pending' => 'bg-amber-500/10 text-amber-600 dark:text-amber-300',
    'processing' => 'bg-sky-500/10 text-sky-600 dark:text-sky-300',
    'shipped' => 'bg-violet-500/10 text-violet-600 dark:text-violet-300',
    'delivered' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-300',
    'cancelled' => 'bg-rose-500/10 text-rose-600 dark:text-rose-300',
    'refunded' => 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-300',
];
$billing = is_string($order['billing_address'] ?? '') ? json_decode((string) $order['billing_address'], true) : ($order['billing_address'] ?? []);
$shipping = is_string($order['shipping_address'] ?? '') ? json_decode((string) $order['shipping_address'], true) : ($order['shipping_address'] ?? []);
$progressSteps = ['pending' => 'Order placed', 'processing' => 'Processing', 'shipped' => 'Shipped', 'delivered' => 'Delivered'];
$progressRank = ['pending' => 1, 'processing' => 2, 'shipped' => 3, 'delivered' => 4];
$currentRank = $progressRank[$order['status']] ?? 0;
$isTerminal = in_array($order['status'], ['cancelled', 'refunded'], true);
?>
<section class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-7 md:py-10">
    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <a href="/account/orders" class="inline-flex items-center gap-2 text-sm font-semibold text-[var(--color-muted)] hover:text-[var(--color-accent)]">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"></path></svg>
            Back to orders
        </a>
        <a href="/account/orders/<?= (int) $order['id'] ?>/invoice" class="inline-flex h-11 items-center justify-center rounded-[var(--radius-button)] bg-[var(--color-accent)] px-4 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">Download invoice</a>
    </div>

    <div class="grid gap-4 xl:grid-cols-[1.25fr_0.75fr]">
        <div class="space-y-4">
            <div class="rounded-3xl border border-[var(--color-border)] bg-[var(--color-surface)] p-5 md:p-7" style="box-shadow: var(--shadow-sm)">
                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[var(--color-accent)]">Order details</p>
                        <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-[var(--color-text)] md:text-3xl">#<?= htmlspecialchars((string) $order['order_number']) ?></h1>
                        <p class="mt-2 text-sm text-[var(--color-muted)]">Placed on <?= htmlspecialchars(\App\Core\StoreResolver::formatDate((string) $order['created_at'])) ?> · Payment via <?= htmlspecialchars((string) ($order['payment_method_label'] ?? $order['payment_method'] ?? '—')) ?></p>
                    </div>
                    <div class="flex flex-col items-start gap-3 md:items-end">
                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold <?= $statusClasses[$order['status']] ?? 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-300' ?>"><?= htmlspecialchars(ucfirst((string) $order['status'])) ?></span>
                        <p class="text-lg font-extrabold text-[var,--color-text)]"><?= htmlspecialchars(\App\Core\StoreResolver::formatPrice((float) $order['grand_total'])) ?></p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-5 md:p-6" style="box-shadow: var(--shadow-sm)">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-bold text-[var,--color-text)]">Order timeline</h2>
                        <p class="text-sm text-[var,--color-muted)]">Follow the current progress of your order.</p>
                    </div>
                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold <?= $statusClasses[$order['status']] ?? 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-300' ?>"><?= htmlspecialchars(ucfirst((string) $order['status'])) ?></span>
                </div>

                <?php if ($isTerminal): ?>
                    <div class="mt-4 rounded-2xl border border-[var(--color-border)] bg-[var,--color-bg)] p-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full <?= $order['status'] === 'cancelled' ? 'bg-rose-500/10 text-rose-600 dark:text-rose-300' : 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-300' ?>"">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                            </div>
                            <div>
                                <p class="font-semibold text-[var,--color-text)]">This order is <?= htmlspecialchars((string) $order['status']) ?>.</p>
                                <p class="text-sm text-[var,--color-muted)]">See the history below for the latest updates and notes.</p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-4">
                        <?php $index = 0; foreach ($progressSteps as $status => $label): $index++; $stepRank = $progressRank[$status]; $isComplete = $currentRank >= $stepRank; $isCurrent = $order['status'] === $status; ?>
                            <div class="relative rounded-2xl border px-4 py-4 <?= $isComplete ? 'border-[var(--color-accent)] bg-[var(--color-accent)]/5' : 'border-[var(--color-border)] bg-[var,--color-bg]' ?>">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full <?= $isComplete ? 'bg-[var(--color-accent)] text-white' : 'bg-[var(--color-surface)] text-[var(--color-muted)] border border-[var(--color-border)]' ?>">
                                        <?php if ($isComplete): ?>
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        <?php else: ?>
                                            <?= $index ?>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-[var,--color-text)]"><?= htmlspecialchars($label) ?></p>
                                        <p class="text-xs <?= $isCurrent ? 'text-[var(--color-accent)]' : 'text-[var,--color-muted)]' ?>"><?= $isCurrent ? 'Current step' : ($isComplete ? 'Completed' : 'Waiting') ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="rounded-2xl border border-[var,--color-border)] bg-[var,--color-surface)] overflow-hidden" style="box-shadow: var(--shadow-sm)">
                <div class="border-b border-[var,--color-border)] px-5 py-4 md:px-6">
                    <h2 class="text-lg font-bold text-[var,--color-text)]">Items in this order</h2>
                </div>
                <div class="divide-y divide-[var(--color-border)]">
                    <?php foreach ($items as $item): ?>
                        <div class="flex flex-col gap-4 px-5 py-4 md:px-6 md:flex-row md:items-center md:justify-between">
                            <div class="flex items-center gap-4">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-[var,--color-border)] bg-[var,--color-bg)] text-[var,--color-accent)]">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12l4 6-10 13L2 9l4-6z"></path><path d="M2 9h20"></path></svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-[var,--color-text)]"><?= htmlspecialchars((string) $item['name']) ?></p>
                                    <p class="mt-1 text-sm text-[var,--color-muted)]">SKU: <?= htmlspecialchars((string) ($item['sku'] ?? '—')) ?> · Qty <?= (int) ($item['qty'] ?? 0) ?></p>
                                </div>
                            </div>
                            <p class="text-sm font-semibold text-[var,--color-text)]"><?= htmlspecialchars(\App\Core\StoreResolver::formatPrice((float) ($item['row_total'] ?? 0))) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="space-y-2 border-t border-[var,--color-border)] bg-[var,--color-bg)] px-5 py-4 text-sm md:px-6">
                    <div class="flex items-center justify-between text-[var,--color-muted)]"><span>Subtotal</span><span><?= htmlspecialchars(\App\Core\StoreResolver::formatPrice((float) $order['subtotal'])) ?></span></div>
                    <div class="flex items-center justify-between text-[var,--color-muted)]"><span>Shipping</span><span><?= htmlspecialchars(\App\Core\StoreResolver::formatPrice((float) $order['shipping_amount'])) ?></span></div>
                    <div class="flex items-center justify-between text-[var,--color-muted)]"><span>Tax</span><span><?= htmlspecialchars(\App\Core\StoreResolver::formatPrice((float) $order['tax_amount'])) ?></span></div>
                    <?php if ((float) ($order['discount_amount'] ?? 0) > 0): ?>
                        <div class="flex items-center justify-between text-rose-600 dark:text-rose-300"><span>Discount</span><span>-<?= htmlspecialchars(\App\Core\StoreResolver::formatPrice((float) $order['discount_amount'])) ?></span></div>
                    <?php endif; ?>
                    <div class="flex items-center justify-between border-t border-[var(--color-border)] pt-2 text-base font-bold text-[var,--color-text)]"><span>Total</span><span><?= htmlspecialchars(\App\Core\StoreResolver::formatPrice((float) $order['grand_total'])) ?></span></div>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-2xl border border-[var,--color-border)] bg-[var,--color-surface)] p-5 md:p-6" style="box-shadow: var(--shadow-sm)">
                    <h2 class="text-lg font-bold text-[var,--color-text)]">Billing address</h2>
                    <div class="mt-4 space-y-1 text-sm text-[var,--color-muted)]">
                        <p class="font-semibold text-[var,--color-text)]"><?= htmlspecialchars(trim((string) (($billing['first_name'] ?? '') . ' ' . ($billing['last_name'] ?? '')))) ?></p>
                        <?php if (!empty($billing['company'])): ?><p><?= htmlspecialchars((string) $billing['company']) ?></p><?php endif; ?>
                        <p><?= htmlspecialchars((string) ($billing['street_1'] ?? '')) ?></p>
                        <?php if (!empty($billing['street_2'])): ?><p><?= htmlspecialchars((string) $billing['street_2']) ?></p><?php endif; ?>
                        <p><?= htmlspecialchars(trim((string) (($billing['postcode'] ?? '') . ' ' . ($billing['city'] ?? '')))) ?></p>
                        <?php if (!empty($billing['state'])): ?><p><?= htmlspecialchars((string) $billing['state']) ?></p><?php endif; ?>
                        <p><?= htmlspecialchars((string) ($billing['country_code'] ?? '')) ?></p>
                    </div>
                </div>
                <div class="rounded-2xl border border-[var,--color-border)] bg-[var,--color-surface)] p-5 md:p-6" style="box-shadow: var(--shadow-sm)">
                    <h2 class="text-lg font-bold text-[var,--color-text)]">Shipping address</h2>
                    <div class="mt-4 space-y-1 text-sm text-[var,--color-muted)]">
                        <p class="font-semibold text-[var,--color-text)]"><?= htmlspecialchars(trim((string) (($shipping['first_name'] ?? '') . ' ' . ($shipping['last_name'] ?? '')))) ?></p>
                        <?php if (!empty($shipping['company'])): ?><p><?= htmlspecialchars((string) $shipping['company']) ?></p><?php endif; ?>
                        <p><?= htmlspecialchars((string) ($shipping['street_1'] ?? '')) ?></p>
                        <?php if (!empty($shipping['street_2'])): ?><p><?= htmlspecialchars((string) $shipping['street_2']) ?></p><?php endif; ?>
                        <p><?= htmlspecialchars(trim((string) (($shipping['postcode'] ?? '') . ' ' . ($shipping['city'] ?? '')))) ?></p>
                        <?php if (!empty($shipping['state'])): ?><p><?= htmlspecialchars((string) $shipping['state']) ?></p><?php endif; ?>
                        <p><?= htmlspecialchars((string) ($shipping['country_code'] ?? '')) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl border border-[var,--color-border)] bg-[var,--color-surface)] p-5 md:p-6" style="box-shadow: var(--shadow-sm)">
                <h2 class="text-lg font-bold text-[var,--color-text)]">Order summary</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between gap-3"><dt class="text-[var,--color-muted)]">Order number</dt><dd class="font-semibold text-[var,--color-text)]">#<?= htmlspecialchars((string) $order['order_number']) ?></dd></div>
                    <div class="flex items-center justify-between gap-3"><dt class="text-[var,--color-muted)]">Email</dt><dd class="font-semibold text-[var,--color-text)] text-right"><?= htmlspecialchars((string) $order['customer_email']) ?></dd></div>
                    <div class="flex items-center justify-between gap-3"><dt class="text-[var,--color-muted)]">Shipping method</dt><dd class="font-semibold text-[var,--color-text)] text-right"><?= htmlspecialchars((string) ($order['shipping_method'] ?? '—')) ?></dd></div>
                    <div class="flex items-center justify-between gap-3"><dt class="text-[var,--color-muted)]">Payment method</dt><dd class="font-semibold text-[var,--color-text)] text-right"><?= htmlspecialchars((string) ($order['payment_method_label'] ?? $order['payment_method'] ?? '—')) ?></dd></div>
                    <?php if (!empty($order['coupon_code'])): ?>
                        <div class="flex items-center justify-between gap-3"><dt class="text-[var,--color-muted)]">Coupon</dt><dd class="font-semibold text-[var,--color-text)] text-right"><?= htmlspecialchars((string) $order['coupon_code']) ?></dd></div>
                    <?php endif; ?>
                    <?php if (!empty($order['customer_note'])): ?>
                        <div class="border-t border-[var(--color-border)] pt-3">
                            <dt class="text-[var,--color-muted)]">Customer note</dt>
                            <dd class="mt-1 text-[var,--color-text)]"><?= nl2br(htmlspecialchars((string) $order['customer_note'])) ?></dd>
                        </div>
                    <?php endif; ?>
                </dl>

                <?php if (!empty($order['payment_instruction'])): ?>
                    <div class="mt-4 rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] p-3">
                        <p class="text-xs uppercase tracking-wide text-[var(--color-muted)]">Payment instructions</p>
                        <p class="mt-1 text-sm text-[var,--color-text)]"><?= nl2br(htmlspecialchars((string) $order['payment_instruction'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="rounded-2xl border border-[var,--color-border)] bg-[var,--color-surface)] p-5 md:p-6" style="box-shadow: var(--shadow-sm)">
                <h2 class="text-lg font-bold text-[var,--color-text)]">Status history</h2>
                <?php if (!empty($statusHistory)): ?>
                    <div class="mt-4 space-y-4">
                        <?php foreach ($statusHistory as $history): ?>
                            <div class="flex gap-3">
                                <div class="mt-1 h-2.5 w-2.5 rounded-full bg-[var(--color-accent)]"></div>
                                <div>
                                    <p class="text-sm font-semibold text-[var,--color-text)]"><?= htmlspecialchars(ucfirst((string) $history['status'])) ?></p>
                                    <?php if (!empty($history['comment'])): ?><p class="mt-1 text-sm text-[var,--color-muted)]"><?= htmlspecialchars((string) $history['comment']) ?></p><?php endif; ?>
                                    <p class="mt-1 text-xs text-[var,--color-muted)]"><?= htmlspecialchars(date('d M Y H:i', strtotime((string) $history['created_at']))) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="mt-4 text-sm text-[var,--color-muted)]">No status updates have been recorded yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
