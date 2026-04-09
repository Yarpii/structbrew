<?php
$activeAccountTab = 'support';
$types = [
    'general'         => 'General Inquiry',
    'order_support'   => 'Order Support',
    'product_inquiry' => 'Product Inquiry',
    'technical'       => 'Technical Issue',
    'billing'         => 'Billing',
    'shipping'        => 'Shipping',
    'returns'         => 'Returns & Refunds',
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
                <h1 class="text-xl font-bold text-[var(--color-text)]">Open a Support Ticket</h1>
                <p class="text-sm text-[var(--color-muted)]">We'll get back to you as soon as possible.</p>
            </div>
        </div>

        <?php if (!empty($flashError)): ?>
        <div class="rounded-md border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"><?= htmlspecialchars($flashError) ?></div>
        <?php endif; ?>

        <div class="max-w-2xl">
            <form method="POST" action="/account/tickets" class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-6 space-y-4">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

                <div>
                    <label class="block text-xs font-semibold text-[var(--color-text)] mb-1.5">Category</label>
                    <select name="type" class="w-full h-11 rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 text-sm text-[var(--color-text)]">
                        <?php foreach ($types as $key => $label): ?>
                        <option value="<?= $key ?>" <?= (($_POST['type'] ?? '') === $key) ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if (!empty($departments)): ?>
                <div>
                    <label class="block text-xs font-semibold text-[var(--color-text)] mb-1.5">Department</label>
                    <select name="department_id" class="w-full h-11 rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 text-sm text-[var(--color-text)]">
                        <option value="">Select department (optional)</option>
                        <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div>
                    <label class="block text-xs font-semibold text-[var(--color-text)] mb-1.5">Subject <span class="text-rose-500">*</span></label>
                    <input type="text" name="subject" value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>"
                           placeholder="Brief description of your issue" required
                           class="w-full h-11 rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 text-sm text-[var(--color-text)]">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-[var(--color-text)] mb-1.5">Message <span class="text-rose-500">*</span></label>
                    <textarea name="body" rows="6" required
                              placeholder="Please describe your issue in detail..."
                              class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 py-2.5 text-sm text-[var(--color-text)] resize-y"><?= htmlspecialchars($_POST['body'] ?? '') ?></textarea>
                </div>

                <?php if (!empty($orders)): ?>
                <div>
                    <label class="block text-xs font-semibold text-[var(--color-text)] mb-1.5">Related Order (optional)</label>
                    <select name="order_id" class="w-full h-11 rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 text-sm text-[var(--color-text)]">
                        <option value="">No related order</option>
                        <?php foreach ($orders as $order): ?>
                        <option value="<?= $order['id'] ?>">#<?= htmlspecialchars($order['order_number']) ?> — <?= date('d M Y', strtotime($order['created_at'])) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="inline-flex h-11 items-center rounded-md bg-[var(--color-accent)] px-6 text-sm font-semibold text-white transition hover:opacity-90">
                        Submit Ticket
                    </button>
                    <a href="/account/tickets"
                       class="inline-flex h-11 items-center rounded-md border border-[var(--color-border)] px-4 text-sm font-medium text-[var(--color-text)] transition hover:bg-[var(--color-surface)]">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>
