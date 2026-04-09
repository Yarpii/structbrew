<?php $activeAccountTab = 'profile'; ?>
<section class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-7 md:py-10">
    <div class="space-y-4">
        <?php include __DIR__ . '/_nav.php'; ?>

        <?php if (!empty($flashError)): ?>
            <div class="rounded-md border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-700 dark:text-rose-200"><?= htmlspecialchars((string) $flashError) ?></div>
        <?php endif; ?>
        <?php if (!empty($flashSuccess)): ?>
            <div class="rounded-md border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-200"><?= htmlspecialchars((string) $flashSuccess) ?></div>
        <?php endif; ?>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-5">
                <p class="text-sm text-[var(--color-muted)]">Account type</p>
                <p class="mt-2 text-xl font-semibold text-[var(--color-text)]"><?= htmlspecialchars((string) ($customerGroupLabel ?? 'Normal Client')) ?></p>
            </div>
            <div class="rounded-lg border border-[var(--color-border)] bg-[var,--color-surface)] p-5">
                <p class="text-sm text-[var(--color-muted)]">Total orders</p>
                <p class="mt-2 text-xl font-semibold text-[var,--color-text)]"><?= count($orders ?? []) ?></p>
            </div>
            <div class="rounded-lg border border-[var(--color-border)] bg-[var,--color-surface)] p-5">
                <p class="text-sm text-[var(--color-muted)]">Open orders</p>
                <p class="mt-2 text-xl font-semibold text-[var,--color-text)]"><?= (int) ($activeOrders ?? 0) ?></p>
            </div>
        </div>

        <div class="rounded-lg border border-[var(--color-border)] bg-[var,--color-surface)] p-5 md:p-6">
            <h1 class="text-2xl font-bold text-[var,--color-text)]">My profile</h1>
            <p class="mt-1 text-sm text-[var(--color-muted)]">Manage your account details.</p>

            <form method="POST" action="/account/profile" class="mt-4 space-y-4">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">
                <?php if (!empty($supportsCustomerGroups) && !empty($customerGroups)): ?>
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-[var,--color-text)]">Account type</label>
                        <select name="customer_group" class="h-11 w-full rounded-md border border-[var,--color-border)] bg-[var,--color-bg)] px-3.5 text-[var,--color-text)]">
                            <?php foreach ($customerGroups as $groupKey => $group): ?>
                                <option value="<?= htmlspecialchars((string) $groupKey) ?>" <?= (($customerGroup ?? 'retail') === $groupKey) ? 'selected' : '' ?>><?= htmlspecialchars((string) $group['label']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <input type="text" name="first_name" value="<?= htmlspecialchars((string) ($customer['first_name'] ?? '')) ?>" required placeholder="First name" class="h-11 w-full rounded-md border border-[var,--color-border)] bg-[var,--color-bg)] px-3.5 text-[var,--color-text)]">
                    <input type="text" name="last_name" value="<?= htmlspecialchars((string) ($customer['last_name'] ?? '')) ?>" required placeholder="Last name" class="h-11 w-full rounded-md border border-[var,--color-border)] bg-[var,--color-bg)] px-3.5 text-[var,--color-text)]">
                </div>
                <input type="email" name="email" value="<?= htmlspecialchars((string) ($customer['email'] ?? '')) ?>" required placeholder="Email address" class="h-11 w-full rounded-md border border-[var,--color-border)] bg-[var,--color-bg)] px-3.5 text-[var,--color-text)]">
                <input type="text" name="phone" value="<?= htmlspecialchars((string) ($customer['phone'] ?? '')) ?>" placeholder="Phone" class="h-11 w-full rounded-md border border-[var,--color-border)] bg-[var,--color-bg)] px-3.5 text-[var,--color-text)]">
                <input type="date" name="date_of_birth" value="<?= htmlspecialchars((string) ($customer['date_of_birth'] ?? '')) ?>" class="h-11 w-full rounded-md border border-[var,--color-border)] bg-[var,--color-bg)] px-3.5 text-[var,--color-text)]">
                <div class="flex items-center justify-between gap-3">
                    <p class="text-sm text-[var(--color-muted)]">Customer since <?= !empty($customer['created_at']) ? htmlspecialchars(\App\Core\StoreResolver::formatDate((string) $customer['created_at'])) : '—' ?></p>
                    <button type="submit" class="inline-flex h-11 items-center justify-center rounded-md bg-[var,--color-accent)] px-4 text-sm font-semibold text-white">Save profile</button>
                </div>
            </form>
        </div>

        <div class="rounded-lg border border-[var(--color-border)] bg-[var,--color-surface)] p-5 md:p-6">
            <h2 class="text-xl font-semibold text-[var,--color-text)]">Two-factor authentication</h2>

            <?php if (empty($twoFactorFeatureEnabled)): ?>
                <p class="mt-2 text-sm text-[var(--color-muted)]">Two-factor authentication is currently disabled by the administrator.</p>
            <?php else: ?>
                <p class="mt-1 text-sm text-[var(--color-muted)]">
                    Status:
                    <span class="font-semibold <?= !empty($twoFactorEnabled) ? 'text-emerald-600' : 'text-amber-600' ?>">
                        <?= !empty($twoFactorEnabled) ? 'Enabled' : 'Disabled' ?>
                    </span>
                </p>

                <?php if (empty($twoFactorSecret)): ?>
                    <form method="POST" action="/account/profile/2fa/setup" class="mt-4">
                        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">
                        <button type="submit" class="inline-flex h-10 items-center justify-center rounded-md border border-[var(--color-border)] bg-[var,--color-bg)] px-4 text-sm font-semibold text-[var,--color-text)]">
                            Generate 2FA secret
                        </button>
                    </form>
                <?php else: ?>
                    <div class="mt-4 space-y-3 rounded-md border border-[var,--color-border)] bg-[var,--color-bg)] p-4">
                        <p class="text-sm text-[var,--color-muted)]">Secret key (manual setup)</p>
                        <p class="font-mono text-sm text-[var,--color-text)] break-all"><?= htmlspecialchars((string) $twoFactorSecret) ?></p>
                        <?php if (!empty($twoFactorProvisioningUri)): ?>
                            <p class="text-xs text-[var,--color-muted)]">OTP URI:</p>
                            <p class="font-mono text-xs text-[var,--color-muted)] break-all"><?= htmlspecialchars((string) $twoFactorProvisioningUri) ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if (empty($twoFactorEnabled)): ?>
                        <form method="POST" action="/account/profile/2fa/enable" class="mt-4 flex flex-col gap-3 md:flex-row md:items-end">
                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">
                            <div class="md:w-56">
                                <label class="mb-1.5 block text-sm font-semibold text-[var,--color-text)]">Authenticator code</label>
                                <input type="text" name="two_factor_code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" minlength="6" required class="h-11 w-full rounded-md border border-[var,--color-border)] bg-[var,--color-bg)] px-3.5 text-[var,--color-text)] tracking-[0.2em]">
                            </div>
                            <button type="submit" class="inline-flex h-11 items-center justify-center rounded-md bg-[var,--color-accent)] px-4 text-sm font-semibold text-white">Enable 2FA</button>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="/account/profile/2fa/disable" class="mt-4 flex flex-col gap-3 md:flex-row md:items-end">
                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">
                            <div class="md:w-56">
                                <label class="mb-1.5 block text-sm font-semibold text-[var,--color-text)]">Authenticator code</label>
                                <input type="text" name="two_factor_code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" minlength="6" required class="h-11 w-full rounded-md border border-[var,--color-border)] bg-[var,--color-bg)] px-3.5 text-[var,--color-text)] tracking-[0.2em]">
                            </div>
                            <button type="submit" class="inline-flex h-11 items-center justify-center rounded-md border border-rose-300 bg-rose-50 px-4 text-sm font-semibold text-rose-700">Disable 2FA</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
