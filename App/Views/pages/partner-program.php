<?php
$flashSuccess = $flashSuccess ?? null;
$flashError   = $flashError ?? null;
$csrfToken    = $csrfToken ?? '';
?>
<!-- Hero -->
<section class="bg-[var(--color-surface)] border-b border-[var(--color-border)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-14 md:py-20">
        <div class="max-w-2xl">
            <span class="inline-flex items-center gap-1.5 rounded border border-[var(--color-accent)]/30 bg-[var(--color-accent)]/10 px-3 py-1 text-xs font-semibold text-[var(--color-accent)] uppercase tracking-wider mb-4">Partner Program</span>
            <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight text-[var(--color-text)] leading-tight">Earn commission by<br>referring customers</h1>
            <p class="mt-4 text-lg text-[var(--color-muted)] max-w-xl">Join our partner program and earn up to <strong class="text-[var(--color-text)]">15% commission</strong> on every sale you refer. Share your unique link, track conversions in real time, and get paid monthly.</p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="/contact" class="inline-flex h-11 items-center gap-2 rounded-[var(--radius-button)] bg-[var(--color-accent)] px-6 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">
                    Apply Now
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
                <?php if (!empty($isLoggedIn)): ?>
                <a href="/account/partner" class="inline-flex h-11 items-center gap-2 rounded-[var(--radius-button)] border border-[var(--color-border)] bg-[var(--color-surface)] px-6 text-sm font-semibold text-[var(--color-text)] transition hover:border-[var(--color-accent)]">
                    My Partner Dashboard
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Stats Bar -->
<section class="border-b border-[var(--color-border)] bg-[var(--color-bg)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div>
                <p class="text-3xl font-extrabold text-[var(--color-text)]">15%</p>
                <p class="mt-1 text-sm text-[var(--color-muted)]">Max commission rate</p>
            </div>
            <div>
                <p class="text-3xl font-extrabold text-[var(--color-text)]">30 days</p>
                <p class="mt-1 text-sm text-[var(--color-muted)]">Cookie window</p>
            </div>
            <div>
                <p class="text-3xl font-extrabold text-[var(--color-text)]">Monthly</p>
                <p class="mt-1 text-sm text-[var(--color-muted)]">Payout schedule</p>
            </div>
            <div>
                <p class="text-3xl font-extrabold text-[var(--color-text)]">Real-time</p>
                <p class="mt-1 text-sm text-[var(--color-muted)]">Click & conversion tracking</p>
            </div>
        </div>
    </div>
</section>

<!-- How it works -->
<section class="bg-[var(--color-bg)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-14">
        <h2 class="text-2xl font-bold text-[var(--color-text)] mb-8">How it works</h2>
        <div class="grid gap-6 md:grid-cols-3">
            <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-6">
                <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-[var(--color-accent)]/10 text-[var(--color-accent)] font-bold text-lg">1</div>
                <h3 class="font-semibold text-[var(--color-text)] mb-2">Apply below</h3>
                <p class="text-sm text-[var(--color-muted)]">Fill out the short application form. Our team reviews every application within 2–3 business days.</p>
            </div>
            <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-6">
                <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-[var(--color-accent)]/10 text-[var(--color-accent)] font-bold text-lg">2</div>
                <h3 class="font-semibold text-[var(--color-text)] mb-2">Get your referral link</h3>
                <p class="text-sm text-[var(--color-muted)]">Once approved, you get a unique referral link and access to your partner dashboard with live stats.</p>
            </div>
            <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-6">
                <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-lg bg-[var(--color-accent)]/10 text-[var(--color-accent)] font-bold text-lg">3</div>
                <h3 class="font-semibold text-[var(--color-text)] mb-2">Earn commission</h3>
                <p class="text-sm text-[var(--color-muted)]">Every purchase made via your link earns you a commission. Payouts are processed monthly to your preferred method.</p>
            </div>
        </div>
    </div>
</section>

<!-- Benefits -->
<section class="border-t border-[var(--color-border)] bg-[var(--color-surface)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-14">
        <div class="grid gap-8 md:grid-cols-2">
            <div>
                <h2 class="text-2xl font-bold text-[var(--color-text)] mb-6">What you get</h2>
                <ul class="space-y-3">
                    <?php
                    $benefits = [
                        'Competitive commission rate (up to 15%) on every referred sale',
                        'Dedicated partner dashboard with real-time click & conversion stats',
                        'Unique referral link with 30-day cookie attribution',
                        'Monthly payout via bank transfer, PayPal or store credit',
                        'Early access to new product launches and promotions',
                        'Priority support channel for all partner enquiries',
                        'Co-marketing opportunities for high-volume partners',
                    ];
                    foreach ($benefits as $b):
                    ?>
                    <li class="flex items-start gap-3">
                        <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[var(--color-accent)]/15 text-[var(--color-accent)]">
                            <svg width="11" height="11" viewBox="0 0 12 12" fill="none"><path d="M2 6l3 3 5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </span>
                        <span class="text-sm text-[var(--color-muted)]"><?= htmlspecialchars($b) ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-[var(--color-text)] mb-6">Who it's for</h2>
                <div class="space-y-3">
                    <?php
                    $audience = [
                        ['title' => 'Content creators & reviewers', 'desc' => 'YouTube, TikTok, Instagram or blog creators in the tech space.'],
                        ['title' => 'Comparison & review sites', 'desc' => 'Affiliate sites and product comparison platforms.'],
                        ['title' => 'Tech communities & forums', 'desc' => 'Moderators, community managers or niche influencers.'],
                        ['title' => 'Businesses & resellers', 'desc' => 'B2B partners who regularly refer clients to our store.'],
                    ];
                    foreach ($audience as $a):
                    ?>
                    <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-bg)] p-4">
                        <p class="text-sm font-semibold text-[var(--color-text)]"><?= htmlspecialchars($a['title']) ?></p>
                        <p class="mt-0.5 text-xs text-[var(--color-muted)]"><?= htmlspecialchars($a['desc']) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Apply CTA -->
<section class="border-t border-[var(--color-border)] bg-[var(--color-bg)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-12">
        <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-6 md:p-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between" style="box-shadow: var(--shadow-sm)">
            <div>
                <h2 class="text-lg font-bold text-[var(--color-text)]">Ready to apply?</h2>
                <p class="mt-1 text-sm text-[var(--color-muted)]">Fill out the Partner Program application on our contact page. Our team reviews every application within 2&ndash;3 business days.</p>
            </div>
            <a href="/contact" class="shrink-0 inline-flex h-10 items-center gap-2 rounded-md bg-[var(--color-accent)] px-6 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">
                Apply Now
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
    </div>
</section>
