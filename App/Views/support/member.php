<?php
$statusClasses = [
    'open' => 'bg-green-500/10 text-green-700',
    'in_progress' => 'bg-sky-500/10 text-sky-700',
    'waiting_customer' => 'bg-amber-500/10 text-amber-700',
    'waiting_third_party' => 'bg-violet-500/10 text-violet-700',
    'on_hold' => 'bg-zinc-500/10 text-zinc-700',
    'resolved' => 'bg-emerald-500/10 text-emerald-700',
    'closed' => 'bg-zinc-500/10 text-zinc-600',
    'reopened' => 'bg-rose-500/10 text-rose-700',
];
?>
<section class="mx-auto w-full max-w-6xl space-y-6 px-4 py-10 sm:px-6 lg:px-8">
    <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-6 sm:p-8" style="box-shadow: var(--shadow-sm)">
        <h1 class="text-3xl font-bold tracking-tight text-[var(--color-text)]">Support Center</h1>
        <p class="mt-2 text-sm text-[var(--color-muted)]">Logged-in support portal. Manage your tickets here.</p>
    </div>

    <?php if (!empty($ads['support_center'])): ?>
        <?php $ad = $ads['support_center']; include dirname(__DIR__) . '/partials/_managed-ad.php'; ?>
    <?php endif; ?>

    <div class="grid gap-4 md:grid-cols-3">
        <a href="/account/tickets" class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-5 transition hover:border-[var(--color-accent)]/40">
            <h2 class="text-sm font-semibold text-[var(--color-text)]">My Tickets</h2>
            <p class="mt-1 text-xs text-[var(--color-muted)]">View all ticket conversations.</p>
        </a>
        <a href="/account/tickets/create" class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-5 transition hover:border-[var(--color-accent)]/40">
            <h2 class="text-sm font-semibold text-[var(--color-text)]">Open New Ticket</h2>
            <p class="mt-1 text-xs text-[var(--color-muted)]">Create a new support request.</p>
        </a>
        <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-5">
            <h2 class="text-sm font-semibold text-[var(--color-text)]">Open Tickets</h2>
            <p class="mt-1 text-2xl font-bold text-[var(--color-accent)]"><?= (int) ($openTickets ?? 0) ?></p>
        </div>
    </div>

    <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-5" style="box-shadow: var(--shadow-sm)">
        <h3 class="text-sm font-semibold text-[var(--color-text)]">Recent Activity</h3>

        <?php if (!empty($recentTickets)): ?>
            <div class="mt-4 space-y-2">
                <?php foreach ($recentTickets as $ticket): ?>
                    <a href="/account/tickets/<?= (int) $ticket['id'] ?>" class="block rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] p-4 transition hover:border-[var(--color-accent)]/40">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-[var(--color-text)]"><?= htmlspecialchars((string) ($ticket['subject'] ?? 'Ticket')) ?></p>
                                <p class="mt-0.5 text-xs text-[var(--color-muted)]"><?= htmlspecialchars((string) ($ticket['ticket_number'] ?? '')) ?><?php if (!empty($ticket['department_name'])): ?> · <?= htmlspecialchars((string) $ticket['department_name']) ?><?php endif; ?></p>
                            </div>
                            <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold <?= $statusClasses[$ticket['status']] ?? 'bg-zinc-500/10 text-zinc-600' ?>">
                                <?= ucwords(str_replace('_', ' ', (string) ($ticket['status'] ?? 'open'))) ?>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="mt-2 text-xs text-[var(--color-muted)]">No recent tickets yet.</p>
        <?php endif; ?>
    </div>

    <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-5" style="box-shadow: var(--shadow-sm)">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-sm font-semibold text-[var(--color-text)]">Business &amp; Wholesale Enquiries</h3>
                <p class="mt-1 text-xs text-[var(--color-muted)]">For dealer accounts, partner programs, or bulk orders contact <a class="text-[var(--color-accent)] hover:underline" href="mailto:b2b@scooterdynamics.store">b2b@scooterdynamics.store</a> — or explore our B2B programs.</p>
            </div>
            <div class="flex shrink-0 flex-wrap gap-2">
                <a href="/b2b-contact" class="inline-flex h-9 items-center rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-4 text-xs font-medium text-[var(--color-text)] transition hover:border-[var(--color-accent)] hover:text-[var(--color-accent)]">B2B Programs</a>
                <a href="/contact" class="inline-flex h-9 items-center rounded-md bg-[var(--color-accent)] px-4 text-xs font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">Contact Us</a>
            </div>
        </div>
    </div>

    <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-5" style="box-shadow: var(--shadow-sm)">
        <h3 class="text-sm font-semibold text-[var(--color-text)]">Help Topics</h3>
        <div class="mt-3 grid gap-2 sm:grid-cols-2 lg:grid-cols-3 text-sm">
            <a class="text-[var(--color-accent)] hover:underline" href="/returns-warranty">Returns &amp; Warranty</a>
            <a class="text-[var(--color-accent)] hover:underline" href="/returns-decision-tree">Returns Decision Tree</a>
            <a class="text-[var(--color-accent)] hover:underline" href="/warranty-claim">Warranty Claim Checklist</a>
            <a class="text-[var(--color-accent)] hover:underline" href="/warranty-exclusions">Warranty Exclusions</a>
            <a class="text-[var(--color-accent)] hover:underline" href="/order-issues">Order Issues Help</a>
            <a class="text-[var(--color-accent)] hover:underline" href="/vat-invoices">VAT &amp; Invoices</a>
            <a class="text-[var(--color-accent)] hover:underline" href="/faq">FAQ Hub</a>
            <a class="text-[var(--color-accent)] hover:underline" href="/installation-guides">Installation Guides</a>
            <a class="text-[var(--color-accent)] hover:underline" href="/compatibility">Compatibility Help</a>
            <a class="text-[var(--color-accent)] hover:underline" href="/availability-restock">Availability &amp; Restock</a>
        </div>
    </div>
</section>
