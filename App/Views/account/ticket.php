<?php
$activeAccountTab = 'support';
$statusClasses = [
    'open'                => 'bg-green-500/10 text-green-600',
    'in_progress'         => 'bg-sky-500/10 text-sky-600',
    'waiting_customer'    => 'bg-amber-500/10 text-amber-600',
    'waiting_third_party' => 'bg-violet-500/10 text-violet-600',
    'on_hold'             => 'bg-zinc-500/10 text-zinc-600',
    'resolved'            => 'bg-emerald-500/10 text-emerald-600',
    'closed'              => 'bg-zinc-500/10 text-zinc-500',
    'reopened'            => 'bg-rose-500/10 text-rose-600',
];
?>
<section class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-7 md:py-10">
    <div class="space-y-4">
        <?php include __DIR__ . '/_nav.php'; ?>

        <div class="flex items-center gap-3">
            <a href="/account/tickets" class="text-[var(--color-muted)] hover:text-[var(--color-text)] transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-xl font-bold text-[var(--color-text)]"><?= htmlspecialchars($ticket['subject']) ?></h1>
                    <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold <?= $statusClasses[$ticket['status']] ?? 'bg-zinc-500/10 text-zinc-600' ?>">
                        <?= ucwords(str_replace('_', ' ', $ticket['status'])) ?>
                    </span>
                </div>
                <p class="text-xs text-[var(--color-muted)] mt-0.5"><?= htmlspecialchars($ticket['ticket_number']) ?> &middot; Opened <?= date('d M Y', strtotime($ticket['created_at'])) ?></p>
            </div>
        </div>

        <?php if (!empty($flashSuccess)): ?>
        <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700"><?= htmlspecialchars($flashSuccess) ?></div>
        <?php endif; ?>
        <?php if (!empty($flashError)): ?>
        <div class="rounded-md border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"><?= htmlspecialchars($flashError) ?></div>
        <?php endif; ?>

        <!-- Reply Thread -->
        <div class="space-y-3">
            <?php foreach ($replies as $r): ?>
            <?php $isAgent = $r['author_type'] === 'admin'; ?>
            <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-4 <?= $isAgent ? 'border-l-2 border-l-[var(--color-accent)]' : '' ?>">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold
                            <?= $isAgent ? 'bg-[var(--color-accent)] text-white' : 'bg-[var(--color-border)] text-[var(--color-text)]' ?>">
                            <?= strtoupper(substr($r['author_name'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-[var(--color-text)]">
                                <?= $isAgent ? 'Support Team' : 'You' ?>
                            </p>
                        </div>
                    </div>
                    <span class="text-xs text-[var(--color-muted)]"><?= date('d M Y H:i', strtotime($r['created_at'])) ?></span>
                </div>
                <div class="text-sm text-[var(--color-text)] leading-relaxed whitespace-pre-wrap"><?= htmlspecialchars($r['body']) ?></div>
            </div>
            <?php endforeach; ?>

            <?php if (empty($replies)): ?>
            <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-6 text-center text-sm text-[var(--color-muted)]">
                No messages yet. Your ticket has been received.
            </div>
            <?php endif; ?>
        </div>

        <!-- Reply Form -->
        <?php if (!in_array($ticket['status'], ['closed', 'resolved'])): ?>
        <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-5">
            <h3 class="text-sm font-semibold text-[var(--color-text)] mb-3">Reply</h3>
            <form method="POST" action="/account/tickets/<?= (int) $ticket['id'] ?>/reply">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                <textarea name="body" rows="5" placeholder="Type your message..."
                          class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 py-2.5 text-sm text-[var(--color-text)] focus:outline-none focus:ring-1 focus:ring-[var(--color-accent)] resize-y"
                          required></textarea>
                <div class="mt-3 flex items-center justify-between">
                    <form method="POST" action="/account/tickets/<?= (int) $ticket['id'] ?>/close" class="inline">
                        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                        <button type="submit" onclick="return confirm('Mark this ticket as resolved?')"
                                class="text-sm text-[var(--color-muted)] hover:text-[var(--color-text)] underline transition">
                            Mark as resolved
                        </button>
                    </form>
                    <button type="submit" form="replyForm"
                            class="inline-flex h-10 items-center rounded-md bg-[var(--color-accent)] px-5 text-sm font-semibold text-white transition hover:opacity-90">
                        Send
                    </button>
                </div>
            </form>
            <form id="replyForm" method="POST" action="/account/tickets/<?= (int) $ticket['id'] ?>/reply" class="hidden">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
            </form>
        </div>
        <?php else: ?>
        <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-5 text-center">
            <p class="text-sm text-[var(--color-muted)]">This ticket is <?= htmlspecialchars($ticket['status'] ?? '') ?>.</p>
            <?php if ($ticket['status'] === 'resolved' || $ticket['status'] === 'closed'): ?>
            <form method="POST" action="/account/tickets/<?= (int) $ticket['id'] ?>/reopen" class="mt-3">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                <button type="submit" class="inline-flex h-9 items-center rounded-md border border-[var(--color-border)] px-4 text-sm font-medium text-[var(--color-text)] transition hover:bg-[var(--color-surface)]">
                    Reopen Ticket
                </button>
            </form>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>
