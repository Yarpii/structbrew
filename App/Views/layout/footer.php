<!-- USP Bar -->
<div class="border-t border-[var(--color-border)] bg-[var(--color-surface)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-6">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="flex items-start gap-3 p-3 rounded-card border border-[var(--color-border)] bg-[var(--color-bg)]">
                <div class="shrink-0 w-9 h-9 rounded-lg bg-[var(--color-accent)]/10 flex items-center justify-center text-[var(--color-accent)]">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-[var(--color-text)]"><?= htmlspecialchars(__('free_shipping')) ?></p>
                    <p class="text-xs text-[var(--color-muted)]">On orders over $50</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-3 rounded-card border border-[var(--color-border)] bg-[var(--color-bg)]">
                <div class="shrink-0 w-9 h-9 rounded-lg bg-[var(--color-accent)]/10 flex items-center justify-center text-[var(--color-accent)]">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-[var(--color-text)]"><?= htmlspecialchars(__('secure_payment')) ?></p>
                    <p class="text-xs text-[var(--color-muted)]">SSL encrypted checkout</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-3 rounded-card border border-[var(--color-border)] bg-[var(--color-bg)]">
                <div class="shrink-0 w-9 h-9 rounded-lg bg-[var(--color-accent)]/10 flex items-center justify-center text-[var(--color-accent)]">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-[var(--color-text)]">Easy Returns</p>
                    <p class="text-xs text-[var(--color-muted)]">30-day return policy</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-3 rounded-card border border-[var(--color-border)] bg-[var(--color-bg)]">
                <div class="shrink-0 w-9 h-9 rounded-lg bg-[var(--color-accent)]/10 flex items-center justify-center text-[var(--color-accent)]">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-[var(--color-text)]">24/7 Support</p>
                    <p class="text-xs text-[var(--color-muted)]">Dedicated help center</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Footer -->
<footer class="border-t border-[var(--color-border)] bg-zinc-900 text-gray-300">

    <!-- Logo + Social + Newsletter -->
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <a href="/" class="flex items-center gap-2.5 mb-4" aria-label="Scooter Dynamics">
                    <img src="/assets/images/logo-light.svg" alt="Scooter Dynamics" class="h-16 w-auto">
                </a>
                <p class="max-w-[22rem] text-sm text-gray-400 leading-relaxed">Your specialist for performance scooter parts, accessories and upgrades. From engine tuning to wheels — we stock the brands riders trust.</p>
                <div class="mt-4 flex items-center gap-3">
                    <a class="flex h-8 w-8 items-center justify-center rounded-md bg-white/10 text-gray-400 transition hover:bg-[var(--color-accent)] hover:text-white" href="#" aria-label="Instagram">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                    </a>
                    <a class="flex h-8 w-8 items-center justify-center rounded-md bg-white/10 text-gray-400 transition hover:bg-[var(--color-accent)] hover:text-white" href="#" aria-label="YouTube">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 0 0 1.46 6.42 29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.95 1.96C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="currentColor" stroke="none"/></svg>
                    </a>
                    <a class="flex h-8 w-8 items-center justify-center rounded-md bg-white/10 text-gray-400 transition hover:bg-[var(--color-accent)] hover:text-white" href="#" aria-label="TikTok">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.33 6.33 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.18 8.18 0 0 0 4.78 1.52V6.78a4.85 4.85 0 0 1-1.01-.09z"/></svg>
                    </a>
                </div>
            </div>
            <div class="flex flex-col gap-3">
                <div>
                    <p class="text-sm font-bold text-white">Stay up to date</p>
                    <p class="mt-0.5 text-sm text-gray-400">New parts, deals and restocks — straight to your inbox.</p>
                </div>
                <form class="flex gap-2" x-data="{ email: '' }" @submit.prevent="email = ''; alert('Thanks for subscribing!')">
                    <input type="email" x-model="email" required placeholder="Your email address"
                           class="w-56 rounded-[var(--radius-input)] border border-white/20 bg-white/10 px-3.5 py-2.5 text-sm text-white placeholder-gray-400 focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)]">
                    <button type="submit" class="shrink-0 rounded-[var(--radius-button)] bg-[var(--color-accent)] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">Subscribe</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Link Columns -->
    <div class="border-y border-white/10">
        <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-8">
            <div class="grid grid-cols-1 gap-7 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">

                <!-- Shop -->
                <section>
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-[0.12em] text-gray-500"><?= htmlspecialchars(__('shop')) ?></h3>
                    <ul class="grid gap-2.5 text-[0.9375rem]">
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/shop">All Products</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/categories">Browse Categories</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/brands">Browse Brands</a></li>
                    </ul>
                </section>

                <!-- Business -->
                <section>
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-[0.12em] text-gray-500">For Business</h3>
                    <ul class="grid gap-2.5 text-[0.9375rem]">
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/b2b-contact">B2B Overview</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/dealer-onboarding">Become a Dealer</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/partner-program">Partner Program</a></li>
                    </ul>
                </section>

                <!-- Account & Orders -->
                <section>
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-[0.12em] text-gray-500"><?= htmlspecialchars(__('my_account')) ?></h3>
                    <ul class="grid gap-2.5 text-[0.9375rem]">
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/login"><?= htmlspecialchars(__('my_account')) ?></a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/register"><?= htmlspecialchars(__('create_account')) ?></a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/cart"><?= htmlspecialchars(__('shopping_cart')) ?></a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/payment-methods"><?= htmlspecialchars(__('payment_methods')) ?></a></li>
                    </ul>
                </section>

                <!-- Support -->
                <section>
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-[0.12em] text-gray-500"><?= htmlspecialchars(__('customer_service')) ?></h3>
                    <ul class="grid gap-2.5 text-[0.9375rem]">
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/support"><?= htmlspecialchars(__('customer_service')) ?></a></li>
                        <?php if (!empty($isLoggedIn) && !empty($currentCustomer)): ?>
                            <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/account/tickets">My Tickets</a></li>
                            <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/account/tickets/create">Open Ticket</a></li>
                        <?php else: ?>
                            <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/login">Ticket Portal Login</a></li>
                        <?php endif; ?>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/contact"><?= htmlspecialchars(__('contact_us')) ?></a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/returns-warranty">Returns &amp; Warranty</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/faq"><?= htmlspecialchars(__('faq')) ?></a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/shipping-by-country">Shipping by Country</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/international-returns">International Returns</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/incoterms">Incoterms: DAP &amp; DDP</a></li>
                    </ul>
                </section>

                <!-- Policies -->
                <section>
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-[0.12em] text-gray-500">Policies</h3>
                    <ul class="grid gap-2.5 text-[0.9375rem]">
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/terms-and-conditions">Terms &amp; Conditions</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/privacy-policy">Privacy Policy</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/pre-order-policy">Pre-Order Policy</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/cookie-policy">Cookie Policy</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/returns-decision-tree">Returns Decision Tree</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/warranty-claim">Warranty Claim Checklist</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/warranty-exclusions">Warranty Exclusions</a></li>
                    </ul>
                </section>

            </div>
        </div>
    </div>

    <!-- Region / Legal Bar -->
    <div class="border-b border-white/10">
        <div class="mx-auto flex w-[92%] flex-col gap-4 py-4 text-sm sm:w-[90%] md:w-[88%] lg:w-[85%] lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-wrap items-center gap-3 text-gray-500">
                <span class="font-semibold text-white"><?= htmlspecialchars(__('country')) ?></span>
                <span class="text-gray-400"><?= htmlspecialchars($currentCountry ?? 'US') ?></span>
                <span class="font-semibold text-white"><?= htmlspecialchars(__('currency')) ?></span>
                <span class="text-gray-400"><?= htmlspecialchars(($currentCurrency ?? 'USD') . ' (' . ($currentCurrencySymbol ?? '$') . ')') ?></span>
                <span class="font-semibold text-white"><?= htmlspecialchars(__('language')) ?></span>
                <span class="text-gray-400"><?= htmlspecialchars($currentLanguage ?? 'en') ?></span>
            </div>
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-gray-400">
                <a class="transition-colors hover:text-[var(--color-accent)]" href="/privacy-policy"><?= htmlspecialchars(__('privacy_policy')) ?></a>
                <a class="transition-colors hover:text-[var(--color-accent)]" href="/terms-and-conditions"><?= htmlspecialchars(__('terms_conditions')) ?></a>
                <a class="transition-colors hover:text-[var(--color-accent)]" href="/cookie-policy">Cookies</a>
            </div>
        </div>
    </div>

    <!-- Copyright + Payment -->
    <div class="py-4 text-sm text-gray-500">
        <div class="mx-auto flex w-[92%] flex-wrap items-center justify-between gap-4 sm:w-[90%] md:w-[88%] lg:w-[85%]">
            <span>&copy; <?= date('Y') ?> Scooter Dynamics. <?= htmlspecialchars(__('copyright')) ?> Powered by Structbrew Framework.</span>
            <span class="inline-flex items-center gap-2">
                <svg width="34" height="20" viewBox="0 0 34 20" aria-label="Visa"><rect width="34" height="20" rx="3" fill="#1e1e1e" stroke="rgba(255,255,255,0.16)"/><text x="17" y="13" font-size="8" font-weight="600" text-anchor="middle" fill="#a1a1aa">VISA</text></svg>
                <svg width="34" height="20" viewBox="0 0 34 20" aria-label="Mastercard"><rect width="34" height="20" rx="3" fill="#1e1e1e" stroke="rgba(255,255,255,0.16)"/><circle cx="14" cy="10" r="5" fill="none" stroke="#a1a1aa" stroke-width="1.2"/><circle cx="20" cy="10" r="5" fill="none" stroke="#a1a1aa" stroke-width="1.2"/></svg>
                <svg width="34" height="20" viewBox="0 0 34 20" aria-label="PayPal"><rect width="34" height="20" rx="3" fill="#1e1e1e" stroke="rgba(255,255,255,0.16)"/><text x="17" y="13" font-size="6" font-weight="600" text-anchor="middle" fill="#a1a1aa">PayPal</text></svg>
                <svg width="34" height="20" viewBox="0 0 34 20" aria-label="Klarna"><rect width="34" height="20" rx="3" fill="#1e1e1e" stroke="rgba(255,255,255,0.16)"/><text x="17" y="13" font-size="6" font-weight="600" text-anchor="middle" fill="#a1a1aa">Klarna</text></svg>
                <svg width="34" height="20" viewBox="0 0 34 20" aria-label="Apple Pay"><rect width="34" height="20" rx="3" fill="#1e1e1e" stroke="rgba(255,255,255,0.16)"/><text x="17" y="13" font-size="6" font-weight="600" text-anchor="middle" fill="#a1a1aa">Apple</text></svg>
            </span>
        </div>
    </div>
</footer>
