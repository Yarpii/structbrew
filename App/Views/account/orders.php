<?php
$activeAccountTab = 'orders';
$statusClasses = [
    'pending' => 'bg-amber-500/10 text-amber-600 dark:text-amber-300',
    'processing' => 'bg-sky-500/10 text-sky-600 dark:text-sky-300',
    'shipped' => 'bg-violet-500/10 text-violet-600 dark:text-violet-300',
    'delivered' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-300',
    'cancelled' => 'bg-rose-500/10 text-rose-600 dark:text-rose-300',
    'refunded' => 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-300',
];

$orderFilters = $orderFilters ?? [];
$availableStatuses = $availableStatuses ?? [];
$availableCountries = $availableCountries ?? [];
?>
<section class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-7 md:py-10">
    <div class="space-y-4">
        <?php include __DIR__ . '/_nav.php'; ?>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-5">
                <p class="text-sm text-[var(--color-muted)]">Orders shown</p>
                <p class="mt-2 text-2xl font-semibold text-[var,--color-text)]"><?= (int) ($filteredOrdersCount ?? count($orders ?? [])) ?></p>
            </div>
            <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-5">
                <p class="text-sm text-[var(--color-muted)]">Open orders</p>
                <p class="mt-2 text-2xl font-semibold text-[var,--color-text)]"><?= (int) ($openOrdersCount ?? 0) ?></p>
            </div>
            <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-5">
                <p class="text-sm text-[var(--color-muted)]">Scope</p>
                <p class="mt-2 text-sm font-semibold text-[var,--color-text)]">Multi-country orders + scooter-specific context</p>
            </div>
        </div>

        <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-5">
            <h1 class="text-2xl font-bold text-[var,--color-text)]">My orders</h1>
            <p class="mt-1 text-sm text-[var(--color-muted)]">Search by order number, SKU, part name, compatibility or country.</p>

            <form method="GET" action="/account/orders" class="mt-4 grid gap-3 md:grid-cols-5">
                <input
                    type="search"
                    name="q"
                    value="<?= htmlspecialchars((string) ($orderFilters['q'] ?? '')) ?>"
                    placeholder="Search order, SKU, part..."
                    class="h-11 rounded-md border border-[var(--color-border)] bg-[var,--color-bg)] px-3.5 text-sm text-[var,--color-text)] md:col-span-2"
                >
                <select name="status" class="h-11 rounded-md border border-[var(--color-border)] bg-[var,--color-bg)] px-3.5 text-sm text-[var,--color-text)]">
                    <option value="">All statuses</option>
                    <?php foreach ($availableStatuses as $statusKey => $statusLabel): ?>
                        <option value="<?= htmlspecialchars((string) $statusKey) ?>" <?= (($orderFilters['status'] ?? '') === $statusKey) ? 'selected' : '' ?>><?= htmlspecialchars((string) $statusLabel) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="country" class="h-11 rounded-md border border-[var(--color-border)] bg-[var,--color-bg)] px-3.5 text-sm text-[var,--color-text)]">
                    <option value="">All countries</option>
                    <?php foreach ($availableCountries as $country): ?>
                        <option value="<?= htmlspecialchars((string) $country) ?>" <?= (($orderFilters['country'] ?? '') === $country) ? 'selected' : '' ?>><?= htmlspecialchars((string) $country) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="inline-flex h-11 items-center justify-center rounded-md bg-[var(--color-accent)] px-4 text-sm font-semibold text-white">Apply</button>

                <input type="date" name="date_from" value="<?= htmlspecialchars((string) ($orderFilters['date_from'] ?? '')) ?>" class="h-11 rounded-md border border-[var(--color-border)] bg-[var,--color-bg)] px-3.5 text-sm text-[var,--color-text)]">
                <input type="date" name="date_to" value="<?= htmlspecialchars((string) ($orderFilters['date_to'] ?? '')) ?>" class="h-11 rounded-md border border-[var(--color-border)] bg-[var,--color-bg)] px-3.5 text-sm text-[var,--color-text)]">
                <a href="/account/orders" class="inline-flex h-11 items-center justify-center rounded-md border border-[var(--color-border)] px-4 text-sm font-semibold text-[var,--color-text)]">Reset</a>
            </form>
        </div>

        <?php if (!empty($orders)): ?>
            <div class="space-y-3">
                <?php foreach ($orders as $order):
                    $reorderItemsJson = htmlspecialchars((string) json_encode($order['reorder_items'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
                ?>
                    <div class="rounded-lg border border-[var(--color-border)] bg-[var,--color-surface)] p-5">
                        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="text-base font-semibold text-[var,--color-text)]">#<?= htmlspecialchars((string) ($order['order_number'] ?? '')) ?></p>
                                    <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold <?= $statusClasses[$order['status']] ?? 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-300' ?>"><?= htmlspecialchars(ucfirst((string) ($order['status'] ?? 'pending'))) ?></span>
                                </div>
                                <p class="mt-1 text-sm text-[var,--color-muted)]">
                                    <?= htmlspecialchars((string) ($order['store_view_name'] ?? 'Store')) ?>
                                    <?php if (!empty($order['store_view_code'])): ?>· <?= htmlspecialchars((string) $order['store_view_code']) ?><?php endif; ?>
                                    <?php if (!empty($order['shipping_country'])): ?> · <?= htmlspecialchars((string) $order['shipping_country']) ?><?php endif; ?>
                                    · <?= htmlspecialchars(\App\Core\StoreResolver::formatDate((string) ($order['created_at'] ?? ''))) ?>
                                </p>
                                <p class="mt-1 text-xs text-[var,--color-muted)]">Payment: <?= htmlspecialchars((string) ($order['payment_method_label'] ?? $order['payment_method'] ?? '—')) ?></p>
                            </div>
                            <div class="text-left md:text-right">
                                <p class="text-sm text-[var,--color-muted)]">Grand total</p>
                                <p class="text-lg font-semibold text-[var,--color-text)]"><?= htmlspecialchars(\App\Core\StoreResolver::formatPrice((float) ($order['grand_total'] ?? 0))) ?></p>
                                <p class="text-xs text-[var,--color-muted)] mt-1"><?= (int) ($order['item_count'] ?? 0) ?> item(s)</p>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-3 md:grid-cols-3">
                            <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3 py-2">
                                <p class="text-xs uppercase tracking-wide text-[var,--color-muted)]">Scooter compatibility</p>
                                <p class="mt-1 text-sm font-semibold text-[var,--color-text)]"><?= htmlspecialchars((string) ($order['compatibility_hint'] ?? 'Compatibility check recommended')) ?></p>
                            </div>
                            <div class="rounded-md border border-[var,--color-border)] bg-[var,--color-bg)] px-3 py-2">
                                <p class="text-xs uppercase tracking-wide text-[var,--color-muted)]">Maintenance signal</p>
                                <p class="mt-1 text-sm font-semibold text-[var,--color-text)]"><?= htmlspecialchars((string) ($order['maintenance_hint'] ?? 'Performance or upgrade item')) ?></p>
                            </div>
                            <div class="rounded-md border border-[var(--color-border)] bg-[var,--color-bg)] px-3 py-2">
                                <p class="text-xs uppercase tracking-wide text-[var,--color-muted)]">Latest timeline note</p>
                                <p class="mt-1 text-sm text-[var,--color-text)]"><?= htmlspecialchars((string) (($order['latest_status_comment'] ?? '') !== '' ? $order['latest_status_comment'] : 'No extra note yet')) ?></p>
                            </div>
                        </div>

                        <?php if (!empty($order['items'])): ?>
                            <div class="mt-4 rounded-md border border-[var,--color-border)] overflow-hidden">
                                <table class="min-w-full text-sm">
                                    <thead>
                                        <tr class="bg-[var(--color-bg)] text-left text-[var,--color-muted)]">
                                            <th class="px-3 py-2 font-semibold">Part</th>
                                            <th class="px-3 py-2 font-semibold">SKU</th>
                                            <th class="px-3 py-2 font-semibold">Qty</th>
                                            <th class="px-3 py-2 font-semibold">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[var(--color-border)]">
                                        <?php foreach ($order['items'] as $item): ?>
                                            <tr>
                                                <td class="px-3 py-2 text-[var,--color-text)]"><?= htmlspecialchars((string) ($item['name'] ?? 'Item')) ?></td>
                                                <td class="px-3 py-2 text-[var,--color-muted)]"><?= htmlspecialchars((string) ($item['sku'] ?? '—')) ?></td>
                                                <td class="px-3 py-2 text-[var,--color-text)]"><?= (int) ($item['qty'] ?? 0) ?></td>
                                                <td class="px-3 py-2 text-[var,--color-text)]"><?= htmlspecialchars(\App\Core\StoreResolver::formatPrice((float) ($item['row_total'] ?? 0))) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="/account/orders/<?= (int) $order['id'] ?>" class="inline-flex h-10 items-center justify-center rounded-md border border-[var(--color-border)] px-3 text-sm font-semibold text-[var,--color-text)]">Details</a>
                            <a href="/account/orders/<?= (int) $order['id'] ?>/invoice" class="inline-flex h-10 items-center justify-center rounded-md border border-[var(--color-border)] px-3 text-sm font-semibold text-[var,--color-text)]">Invoice</a>
                            <?php if (!empty($order['tracking_available'])): ?>
                                <a href="/account/orders/<?= (int) $order['id'] ?>#tracking" class="inline-flex h-10 items-center justify-center rounded-md border border-[var(--color-border)] px-3 text-sm font-semibold text-[var,--color-text)]">Track</a>
                            <?php endif; ?>
                            <button type="button" data-reorder='<?= $reorderItemsJson ?>' @click='(() => { const payload = JSON.parse($el.dataset.reorder || "[]"); payload.forEach(item => { const qty = Math.max(1, Number(item.qty || 1)); for (let i = 0; i < qty; i++) { $store.cart.add({ id: item.id, name: item.name, slug: item.slug || "", price: Number(item.price || 0) }); } }); window.location.href = "/cart"; })()' class="inline-flex h-10 items-center justify-center rounded-md bg-[var(--color-accent)] px-3 text-sm font-semibold text-white">
                                Re-order all
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="rounded-lg border border-dashed border-[var(--color-border)] bg-[var(--color-surface)] p-6 text-sm text-[var(--color-muted)]">
                No orders found for this filter selection.
            </div>
        <?php endif; ?>
    </div>
</section>
