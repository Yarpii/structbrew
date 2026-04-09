<?php
$activeAccountTab = 'overview';
?>
<section class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-7 md:py-10">
    <div class="space-y-4">
        <?php include __DIR__ . '/_nav.php'; ?>

        <?php if (!empty($flashError)): ?>
            <div class="rounded-md border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-700 dark:text-rose-200"><?= htmlspecialchars((string) $flashError) ?></div>
        <?php endif; ?>
        <?php if (!empty($flashSuccess)): ?>
            <div class="rounded-md border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-200"><?= htmlspecialchars((string) $flashSuccess) ?></div>
        <?php endif; ?>

        <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-5 md:p-6">
            <p class="text-sm font-semibold text-[var(--color-accent)]">Customer portal</p>
            <h1 class="mt-1 text-2xl font-bold md:text-3xl text-[var(--color-text)]">Welcome back, <?= htmlspecialchars(trim(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? ''))) ?></h1>
            <p class="mt-2 text-sm text-[var(--color-muted)]">Manage profile, orders and addresses from one structured workspace.</p>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <a href="/account/profile" class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-5 transition hover:border-[var(--color-accent)]">
                <h2 class="text-base font-semibold text-[var(--color-text)]">Profile</h2>
                <p class="mt-1 text-sm text-[var,--color-muted]">Update personal and account information.</p>
            </a>
            <a href="/account/orders" class="rounded-lg border border-[var(--color-border)] bg-[var,--color-surface)] p-5 transition hover:border-[var(--color-accent)]">
                <h2 class="text-base font-semibold text-[var(--color-text)]">Orders</h2>
                <p class="mt-1 text-sm text-[var,--color-muted]">Review order history and order status.</p>
            </a>
            <a href="/account/addresses" class="rounded-lg border border-[var(--color-border)] bg-[var,--color-surface)] p-5 transition hover:border-[var(--color-accent)]">
                <h2 class="text-base font-semibold text-[var(--color-text)]">Addresses</h2>
                <p class="mt-1 text-sm text-[var,--color-muted]">Maintain billing and shipping addresses.</p>
            </a>
        </div>

        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-5">
                <p class="text-sm text-[var(--color-muted)]">Account type</p>
                <p class="mt-2 text-xl font-semibold text-[var,--color-text]"><?= htmlspecialchars((string) ($customerGroupLabel ?? 'Normal Client')) ?></p>
            </div>
            <div class="rounded-lg border border-[var,--color-border)] bg-[var,--color-surface] p-5">
                <p class="text-sm text-[var,--color-muted]">Total orders</p>
                <p class="mt-2 text-xl font-semibold text-[var,--color-text]"><?= count($orders ?? []) ?></p>
            </div>
            <div class="rounded-lg border border-[var,--color-border)] bg-[var,--color-surface] p-5">
                <p class="text-sm text-[var,--color-muted]">Open orders</p>
                <p class="mt-2 text-xl font-semibold text-[var,--color-text]"><?= (int) ($activeOrders ?? 0) ?></p>
            </div>
            <div class="rounded-lg border border-[var,--color-border)] bg-[var,--color-surface] p-5">
                <p class="text-sm text-[var,--color-muted]">Lifetime spend</p>
                <p class="mt-2 text-xl font-semibold text-[var,--color-text]"><?= htmlspecialchars(\App\Core\StoreResolver::formatPrice((float) ($totalSpent ?? 0))) ?></p>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="rounded-lg border border-[var(--color-border)] bg-[var,--color-surface)] p-5">
                <p class="text-sm text-[var,--color-muted]">Loyalty points</p>
                <p class="mt-2 text-2xl font-semibold text-[var,--color-text]"><?= (int) ($customer['loyalty_points'] ?? 0) ?></p>
                <p class="mt-2 text-xs text-[var,--color-muted]">Earn points with orders, birthday rewards and admin-defined promotions.</p>
            </div>

            <div class="rounded-lg border border-[var(--color-border)] bg-[var,--color-surface)] p-5">
                <p class="text-sm text-[var,--color-muted]">Store credits</p>
                <p class="mt-2 text-2xl font-semibold text-[var,--color-text]"><?= htmlspecialchars(\App\Core\StoreResolver::formatPrice((float) ($customer['credits_balance'] ?? 0))) ?></p>
                <p class="mt-2 text-xs text-[var,--color-muted]">Credits are spend-only and cannot be withdrawn.</p>

                <?php if (!empty($creditsEnabled)): ?>
                    <form method="POST" action="/account/credits/purchase" class="mt-4 flex flex-wrap items-end gap-3">
                        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-[var(--color-muted)]">Buy credits</label>
                            <input type="number" step="0.01" min="<?= htmlspecialchars((string) number_format((float) ($creditsMinPurchaseAmount ?? 0), 2, '.', '')) ?>" name="amount" required
                                   placeholder="<?= htmlspecialchars((string) number_format((float) ($creditsMinPurchaseAmount ?? 0), 2)) ?>"
                                   class="h-10 w-40 rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3 text-sm text-[var,--color-text]">
                        </div>
                        <button type="submit" class="inline-flex h-10 items-center justify-center rounded-md bg-[var(--color-accent)] px-4 text-sm font-semibold text-white">Add Credits</button>
                    </form>
                <?php else: ?>
                    <p class="mt-4 text-xs text-amber-600">Credit purchases are currently disabled.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
