<section class="mx-auto w-full max-w-6xl space-y-6 px-4 py-10 sm:px-6 lg:px-8">
    <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-6 sm:p-8" style="box-shadow: var(--shadow-sm)">
        <h1 class="text-3xl font-bold tracking-tight text-[var(--color-text)]">Support Center</h1>
        <p class="mt-2 text-sm text-[var(--color-muted)]">Guest support page. If you already have an account, log in to manage tickets directly.</p>
    </div>

    <?php if (!empty($ads['support_center'])): ?>
        <?php $ad = $ads['support_center']; include dirname(__DIR__) . '/partials/_managed-ad.php'; ?>
    <?php endif; ?>

    <div class="grid gap-4 md:grid-cols-3">
        <a href="/login" class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-5 transition hover:border-[var(--color-accent)]/40">
            <h2 class="text-sm font-semibold text-[var(--color-text)]">Login to Ticket Portal</h2>
            <p class="mt-1 text-xs text-[var(--color-muted)]">Track and reply to tickets from your account dashboard.</p>
        </a>
        <a href="/register" class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-5 transition hover:border-[var(--color-accent)]/40">
            <h2 class="text-sm font-semibold text-[var(--color-text)]">Create Account</h2>
            <p class="mt-1 text-xs text-[var(--color-muted)]">Register to get full ticket history and updates in one place.</p>
        </a>
        <a href="/contact" class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-5 transition hover:border-[var(--color-accent)]/40">
            <h2 class="text-sm font-semibold text-[var(--color-text)]">Contact Departments</h2>
            <p class="mt-1 text-xs text-[var(--color-muted)]">Send a message to the correct department.</p>
        </a>
    </div>

    <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-5" style="box-shadow: var(--shadow-sm)">
        <h3 class="text-sm font-semibold text-[var(--color-text)]">Available Departments</h3>
        <?php if (!empty($departments)): ?>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($departments as $department): ?>
                    <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] p-4">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-[var(--color-text)]"><?= htmlspecialchars($department['name']) ?></p>
                            <span class="inline-block h-3 w-3 rounded-full" style="background:<?= htmlspecialchars($department['color'] ?? '#3b82f6') ?>"></span>
                        </div>
                        <p class="mt-2 text-xs text-[var(--color-muted)]"><?= htmlspecialchars((string) ($department['description'] ?? '')) ?></p>
                        <p class="mt-2 text-xs text-[var(--color-accent)]"><?= htmlspecialchars($department['contact_email'] ?? 'No mailbox configured') ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="mt-2 text-xs text-[var(--color-muted)]">No departments configured yet.</p>
        <?php endif; ?>
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

    <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-5" style="box-shadow: var(--shadow-sm)">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-sm font-semibold text-[var(--color-text)]">Business &amp; Wholesale Enquiries</h3>
                <p class="mt-1 text-xs text-[var(--color-muted)]">Dealer accounts, partner programs, and bulk orders have a dedicated team. Contact us at <a class="text-[var(--color-accent)] hover:underline" href="mailto:b2b@scooterdynamics.store">b2b@scooterdynamics.store</a> or explore our programs.</p>
            </div>
            <div class="flex shrink-0 flex-wrap gap-2">
                <a href="/b2b-contact" class="inline-flex h-9 items-center rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-4 text-xs font-medium text-[var(--color-text)] transition hover:border-[var(--color-accent)] hover:text-[var(--color-accent)]">B2B Programs</a>
                <a href="/contact" class="inline-flex h-9 items-center rounded-md bg-[var(--color-accent)] px-4 text-xs font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">Contact Us</a>
            </div>
        </div>
    </div>
</section>
