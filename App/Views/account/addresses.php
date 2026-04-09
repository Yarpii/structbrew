<?php $activeAccountTab = 'addresses'; ?>
<section class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-7 md:py-10">
    <div class="space-y-4">
        <?php include __DIR__ . '/_nav.php'; ?>

        <?php if (!empty($flashError)): ?>
            <div class="rounded-md border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-700 dark:text-rose-200"><?= htmlspecialchars((string) $flashError) ?></div>
        <?php endif; ?>
        <?php if (!empty($flashSuccess)): ?>
            <div class="rounded-md border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-200"><?= htmlspecialchars((string) $flashSuccess) ?></div>
        <?php endif; ?>

        <div class="grid gap-4 xl:grid-cols-2">
            <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-5 md:p-6">
                <h1 class="text-2xl font-bold text-[var(--color-text)]">Add address</h1>
                <form method="POST" action="/account/addresses" class="mt-4 space-y-4">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <select name="type" class="h-11 w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 text-[var(--color-text)]">
                            <option value="shipping">Shipping</option>
                            <option value="billing">Billing</option>
                        </select>
                        <label class="inline-flex h-11 items-center gap-2 text-sm text-[var(--color-muted)]"><input type="checkbox" name="is_default" value="1" class="accent-[var(--color-accent)]"> <span>Set as default</span></label>
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <input type="text" name="first_name" placeholder="First name" required class="h-11 w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 text-[var(--color-text)]">
                        <input type="text" name="last_name" placeholder="Last name" required class="h-11 w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 text-[var(--color-text)]">
                    </div>
                    <input type="text" name="company" placeholder="Company" class="h-11 w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 text-[var(--color-text)]">
                    <input type="text" name="street_1" placeholder="Street address" required class="h-11 w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 text-[var(--color-text)]">
                    <input type="text" name="city" placeholder="City" required class="h-11 w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 text-[var(--color-text)]">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <input type="text" name="postcode" placeholder="Postcode" required class="h-11 w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 text-[var(--color-text)]">
                        <input type="text" name="country_code" placeholder="Country code" maxlength="2" required class="h-11 w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 uppercase text-[var(--color-text)]">
                    </div>
                    <button type="submit" class="inline-flex h-11 items-center justify-center rounded-md bg-[var(--color-accent)] px-4 text-sm font-semibold text-white">Save address</button>
                </form>
            </div>

            <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-5 md:p-6">
                <h2 class="text-xl font-semibold text-[var(--color-text)]">Address book</h2>
                <?php if (!empty($addresses)): ?>
                    <div class="mt-4 space-y-3">
                        <?php foreach ($addresses as $address): ?>
                            <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] p-4 text-sm text-[var(--color-muted)]">
                                <p class="font-semibold text-[var(--color-text)]"><?= htmlspecialchars(trim(($address['first_name'] ?? '') . ' ' . ($address['last_name'] ?? ''))) ?></p>
                                <p><?= htmlspecialchars((string) ($address['street_1'] ?? '')) ?></p>
                                <p><?= htmlspecialchars(trim((string) (($address['postcode'] ?? '') . ' ' . ($address['city'] ?? '')))) ?></p>
                                <p><?= htmlspecialchars((string) ($address['country_code'] ?? '')) ?></p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <?php if (empty($address['is_default'])): ?>
                                        <form method="POST" action="/account/addresses/<?= (int) $address['id'] ?>/default">
                                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">
                                            <button type="submit" class="inline-flex h-9 items-center justify-center rounded-md border border-[var(--color-border)] px-3 text-xs font-semibold text-[var(--color-text)]">Make default</button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" action="/account/addresses/<?= (int) $address['id'] ?>/delete" onsubmit="return confirm('Delete this address?');">
                                        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">
                                        <button type="submit" class="inline-flex h-9 items-center justify-center rounded-md border border-rose-500/20 bg-rose-500/10 px-3 text-xs font-semibold text-rose-600">Delete</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="mt-4 text-sm text-[var(--color-muted)]">No saved addresses yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
