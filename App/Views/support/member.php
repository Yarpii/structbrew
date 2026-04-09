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
        <h3 class="text-sm font-semibold text-[var,--
