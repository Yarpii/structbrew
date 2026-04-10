<?php
$activeAccountTab = 'partner';
$partnerAccount   = $partnerAccount ?? null;
$application      = $application ?? null;
$referrals        = $referrals ?? [];
$baseUrl          = $baseUrl ?? '';
$customer         = $customer ?? [];
?>
<section class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-7 md:py-10">
    <div class="space-y-4">
        <?php include __DIR__ . '/_nav.php'; ?>

        <?php if (!empty($flashSuccess)): ?>
            <div class="rounded-md border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-200"><?= htmlspecialchars((string) $flashSuccess) ?></div>
        <?php endif; ?>
        <?php if (!empty($flashError)): ?>
            <div class="rounded-md border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-700 dark:text-rose-200"><?= htmlspecialchars((string) $flashError) ?></div>
        <?php endif; ?>

        <!-- Header -->
        <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-5 md:p-6">
            <p class="text-sm font-semibold text-[var(--color-accent)]">Partner Program</p>
            <h1 class="mt-1 text-2xl font-bold md:text-3xl text-[var(--color-text)]">Partner Dashboard</h1>
            <p class="mt-2 text-sm text-[var(--color-muted)]">Track your referral link performance, clicks, conversions and commission earnings.</p>
        </div>

        <?php if ($partnerAccount): ?>

        <?php
        $statusColors = [
            'active'    => 'bg-emerald-500/10 text-emerald-700 border-emerald-500/20',
            'paused'    => 'bg-amber-500/10 text-amber-700 border-amber-500/20',
            'suspended' => 'bg-rose-500/10 text-rose-700 border-rose-500/20',
        ];
        $statusColor = $statusColors[$partnerAccount['status'] ?? 'active'] ?? $statusColors['active'];
        ?>

        <!-- Status + Referral Link -->
        <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-5 md:p-6">
            <div class="flex items-center justify-between mb-4 gap-3 flex-wrap">
                <h2 class="text-base font-semibold text-[var(--color-text)]">Your Referral Link</h2>
                <span class="inline-flex items-center rounded border px-2.5 py-1 text-xs font-semibold capitalize <?= $statusColor ?>">
                    <?= htmlspecialchars((string) $partnerAccount['status']) ?>
                </span>
            </div>
            <?php
            $referralUrl = rtrim($baseUrl, '/') . '/r/' . htmlspecialchars((string) $partnerAccount['referral_code']);
            ?>
            <div class="flex gap-2 flex-wrap items-center" x-data="{ copied: false }">
                <input type="text" readonly value="<?= $referralUrl ?>"
                       class="flex-1 min-w-[200px] rounded-[var(--radius-input)] border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 py-2.5 text-sm text-[var(--color-text)] font-mono select-all focus:outline-none">
                <button type="button"
                        @click="navigator.clipboard.writeText('<?= $referralUrl ?>'); copied = true; setTimeout(() => copied = false, 2000)"
                        class="inline-flex h-10 items-center gap-2 rounded-[var(--radius-button)] border border-[var(--color-border)] bg-[var(--color-surface)] px-4 text-sm font-medium text-[var(--color-text)] transition hover:border-[var(--color-accent)]">
                    <template x-if="!copied">
                        <span>Copy</span>
                    </template>
                    <template x-if="copied">
                        <span class="text-emerald-600">Copied!</span>
                    </template>
                </button>
            </div>
            <p class="mt-2 text-xs text-[var(--color-muted)]">Referral code: <strong class="font-mono text-[var(--color-text)]"><?= htmlspecialchars((string) $partnerAccount['referral_code']) ?></strong> &mdash; 30-day cookie attribution window</p>
        </div>

        <!-- Stats -->
        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-5">
                <p class="text-sm text-[var(--color-muted)]">Total clicks</p>
                <p class="mt-2 text-2xl font-bold text-[var(--color-text)]"><?= number_format((int) $partnerAccount['total_clicks']) ?></p>
            </div>
            <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-5">
                <p class="text-sm text-[var(--color-muted)]">Conversions</p>
                <p class="mt-2 text-2xl font-bold text-[var(--color-text)]"><?= number_format((int) $partnerAccount['total_conversions']) ?></p>
            </div>
            <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-5">
                <p class="text-sm text-[var(--color-muted)]">Total earned</p>
                <p class="mt-2 text-2xl font-bold text-[var(--color-text)]">$<?= number_format((float) $partnerAccount['total_commission_earned'], 2) ?></p>
            </div>
            <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-5">
                <p class="text-sm text-[var(--color-muted)]">Pending balance</p>
                <p class="mt-2 text-2xl font-bold text-[var(--color-accent)]">$<?= number_format((float) $partnerAccount['balance'], 2) ?></p>
                <p class="mt-1 text-xs text-[var(--color-muted)]">Paid out monthly</p>
            </div>
        </div>

        <!-- Commission rate info -->
        <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-5 flex items-center gap-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-[var(--color-accent)]/10 text-[var(--color-accent)]">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="5" x2="5" y2="19"/><circle cx="6.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/></svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-[var(--color-text)]">Your commission rate: <?= number_format((float) $partnerAccount['commission_rate'], 2) ?>%</p>
                <p class="text-xs text-[var(--color-muted)]">Applied to the net order value of every qualifying referred purchase.</p>
            </div>
        </div>

        <!-- Referrals Table -->
        <?php if (!empty($referrals)): ?>
        <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] overflow-hidden">
            <div class="px-5 py-4 border-b border-[var(--color-border)]">
                <h2 class="text-sm font-semibold text-[var(--color-text)]">Referral Conversions</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-[var(--color-border)] bg-[var(--color-bg)]">
                            <th class="text-left px-5 py-3 text-xs font-medium text-[var(--color-muted)] uppercase">Date</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-[var(--color-muted)] uppercase">Order</th>
                            <th class="text-right px-5 py-3 text-xs font-medium text-[var(--color-muted)] uppercase">Order Total</th>
                            <th class="text-right px-5 py-3 text-xs font-medium text-[var(--color-muted)] uppercase">Commission</th>
                            <th class="text-center px-5 py-3 text-xs font-medium text-[var(--color-muted)] uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-border)]">
                        <?php
                        $refStatusColors = [
                            'pending'  => 'bg-amber-500/10 text-amber-700 border-amber-500/20',
                            'approved' => 'bg-blue-500/10 text-blue-700 border-blue-500/20',
                            'paid'     => 'bg-emerald-500/10 text-emerald-700 border-emerald-500/20',
                            'rejected' => 'bg-rose-500/10 text-rose-700 border-rose-500/20',
                        ];
                        foreach ($referrals as $ref):
                            $sc = $refStatusColors[$ref['status'] ?? 'pending'] ?? $refStatusColors['pending'];
                        ?>
                        <tr class="hover:bg-[var(--color-bg)]">
                            <td class="px-5 py-3 text-[var(--color-muted)] text-xs"><?= htmlspecialchars(substr((string) $ref['created_at'], 0, 10)) ?></td>
                            <td class="px-5 py-3 text-[var(--color-text)]">
                                <?php if ($ref['order_id']): ?>
                                    <span class="font-mono text-xs">#<?= (int) $ref['order_id'] ?></span>
                                <?php else: ?>
                                    <span class="text-[var(--color-muted)]">—</span>
                                <?php endif; ?>
                                <?php if (!empty($ref['note'])): ?>
                                    <span class="ml-2 text-xs text-[var(--color-muted)]"><?= htmlspecialchars((string) $ref['note']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-3 text-right text-[var(--color-text)]">$<?= number_format((float) $ref['order_total'], 2) ?></td>
                            <td class="px-5 py-3 text-right font-semibold text-[var(--color-text)]">$<?= number_format((float) $ref['commission_amount'], 2) ?></td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center rounded border px-2 py-0.5 text-xs font-medium capitalize <?= $sc ?>">
                                    <?= htmlspecialchars((string) $ref['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php else: ?>
        <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-8 text-center">
            <p class="text-sm text-[var(--color-muted)]">No referral conversions yet. Share your referral link to start earning.</p>
        </div>
        <?php endif; ?>

        <?php elseif ($application): ?>

        <!-- Application pending/rejected state -->
        <?php if ($application['status'] === 'pending'): ?>
        <div class="rounded-lg border border-amber-500/20 bg-amber-500/10 p-6">
            <p class="text-sm font-semibold text-amber-700">Application under review</p>
            <p class="mt-1 text-sm text-amber-600">We received your application on <?= htmlspecialchars(substr((string) $application['created_at'], 0, 10)) ?>. Our team will review it within 2–3 business days and notify you by email.</p>
        </div>
        <?php elseif ($application['status'] === 'rejected'): ?>
        <div class="rounded-lg border border-rose-500/20 bg-rose-500/10 p-6">
            <p class="text-sm font-semibold text-rose-700">Application not approved</p>
            <p class="mt-1 text-sm text-rose-600">Unfortunately your application was not approved at this time. You are welcome to apply again.</p>
            <a href="/partner-program#apply" class="mt-3 inline-flex h-9 items-center gap-2 rounded-[var(--radius-button)] bg-rose-600 px-4 text-sm font-semibold text-white transition hover:bg-rose-700">Apply Again</a>
        </div>
        <?php endif; ?>

        <?php else: ?>

        <!-- No application state -->
        <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-8 text-center">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-[var(--color-accent)]/10 text-[var(--color-accent)]">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <h2 class="text-base font-semibold text-[var(--color-text)] mb-2">Not yet a partner</h2>
            <p class="text-sm text-[var(--color-muted)] mb-4">Apply to our partner program to start earning commission on every sale you refer.</p>
            <a href="/partner-program#apply" class="inline-flex h-10 items-center gap-2 rounded-[var(--radius-button)] bg-[var(--color-accent)] px-5 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">
                Apply Now
            </a>
        </div>

        <?php endif; ?>
    </div>
</section>
