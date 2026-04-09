<?php
$activeAccountTab = 'support';
$statusClasses = [
    'open'                => 'bg-green-500/10 text-green-600 dark:text-green-300',
    'in_progress'         => 'bg-sky-500/10 text-sky-600 dark:text-sky-300',
    'waiting_customer'    => 'bg-amber-500/10 text-amber-600 dark:text-amber-300',
    'waiting_third_party' => 'bg-violet-500/10 text-violet-600 dark:text-violet-300',
    'on_hold'             => 'bg-zinc-500/10 text-zinc-600 dark:text-zinc-300',
    'resolved'            => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-300',
    'closed'              => 'bg-zinc-500/10 text-zinc-500 dark:text-zinc-400',
    'reopened'            => 'bg-rose-500/10 text-rose-600 dark:text-rose-300',
];
$priorityDot = [
    'low'      => 'bg-slate-400',
    'normal'   => 'bg-blue-500',
    'high'     => 'bg-amber-500',
    'critical' => 'bg-orange-500',
    'urgent'   => 'bg-red-500',
];
?>
<section class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-7 md:py-10">
    <div class="space-y-4">
        <?php include __DIR__ . '/_nav.php'; ?>

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-[var(--color-text)]">Support Tickets</h1>
                <p class="mt-1 text-sm text-[var(--color-muted)]">Track your support requests and messages.</p>
            </div>
            <a href="/account/tickets/create"
               class="inline-flex h-10 items-center gap-2 rounded-md bg-[var(--color-accent)] px-4 text-sm font-semibold text-white transition hover:opacity-90">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Ticket
            </a>
        </div>

        <?php if (!empty($flashSuccess)): ?>
        <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700"><?= htmlspecialchars($flashSuccess) ?></div>
        <?php endif; ?>

        <?php if (!empty($tickets)): ?>
        <div class="space-y-2">
            <?php foreach ($tickets as $t): ?>
            <a href="/account/tickets/<?= (int) $t['id'] ?>"
               class="block rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-4 transition hover:border-[var(--color-accent)]/40">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="w-2 h-2 rounded-full flex-shrink-0 <?= $priorityDot[$t['priority']] ?? 'bg-slate-400' ?>"></span>
                            <p class="font-semibold text-[var(--color-text)] truncate"><?= htmlspecialchars($t['subject']) ?></p>
                        </div>
                        <p class="mt-0.5 text-xs text-[var(--color-muted)]">
                            <?= htmlspecialchars($t['ticket_number']) ?>
                            <?php if (!empty($t['department_name'])): ?>
                            &middot; <?= htmlspecialchars($t['department_name']) ?>
                            <?php endif; ?>
                            &middot; Opened <?= date('d M Y', strtotime($t['created_at'])) ?>
                        </p>
                    </div>
                    <span class="flex-shrink-0 inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold <?= $statusClasses[$t['status']] ?? 'bg-zinc-500/10 text-zinc-600' ?>">
                        <?= ucwords(str_replace('_', ' ', $t['status'])) ?>
                    </span>
                </div>
                <?php if (!empty($t['last_reply_at'])): ?>
                <p class="mt-2 text-xs text-[var(--color-muted)]">Last reply <?= date('d M Y H:i', strtotime($t['last_reply_at'])) ?></p>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-10 text-center">
            <svg class="w-10 h-10 mx-auto mb-3 text-[var(--color-muted)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-sm font-medium text-[var(--color-text)]">No tickets yet</p>
            <p class="mt-1 text-xs text-[var(--color-muted)]">Open a ticket if you need help with your order or product.</p>
            <a href="/account/tickets/create" class="mt-4 inline-flex h-9 items-center rounded-md bg-[var(--color-accent)] px-4 text-sm font-semibold text-white">
                Open a ticket
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>
