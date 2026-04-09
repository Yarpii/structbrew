<!-- USP Bar -->
<div class="border-t border-[var(--color-border)] bg-[var(--color-surface)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-6">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="flex items-start gap-3 p-3 rounded-card border border-[var(--color-border)] bg-[var(--color-bg)]">
                <div class="shrink-0 w-9 h-9 rounded-lg bg-[var(--color-accent)]/10 flex items-center justify-center text-[var(--color-accent)]">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-[var(--color-text)]">Free Shipping</p>
                    <p class="text-xs text-[var(--color-muted)]">On orders over $50</p>
                </div>
            </div>
            <div class="flex items-start gap-3 p-3 rounded-card border border-[var(--color-border)] bg-[var(--color-bg)]">
                <div class="shrink-0 w-9 h-9 rounded-lg bg-[var(--color-accent)]/10 flex items-center justify-center text-[var(--color-accent)]">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-[var(--color-text)]">Secure Payment</p>
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
                    <p class="text-sm font-semibold text-[var,--color-text)]">24/7 Support</p>
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
        <div class="grid gap-6 lg:grid-cols-2 lg:items-center">
            <div>
                <a href="/" class="flex items-center gap-2.5 mb-3" aria-label="Scooter Dynamics">
                    <img src="/assets/images/logo-light.svg" alt="Scooter Dynamics" class="h-12 w-auto">
                </a>
                <p class="text-[0.9375rem] text-gray-400">Your one-stop shop for premium tech and accessories.</p>
                <div class="mt-3 flex flex-wrap gap-4 text-[0.9375rem]">
                    <a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="#">Instagram</a>
                    <a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="#">YouTube</a>
                    <a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="#">TikTok</a>
                </div>
            </div>
            <div>
                <h2 class="mb-2 text-base font-bold text-white">Subscribe</h2>
                <p class="mb-3 text-[0.9375rem] text-gray-400">Get notified about new products, deals, and exclusive offers.</p>
                <form class="flex flex-wrap gap-2" x-data="{ email: '' }" @submit.prevent="email = ''; alert('Thanks for subscribing!')">
                    <input type="email" x-model="email" required placeholder="Enter your email"
                           class="min-w-[14rem] grow rounded-[var(--radius-input)] border border-white/20 bg-white/10 px-3.5 py-2.5 text-[0.9375rem] text-white placeholder-gray-400 focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)]">
                    <button type="submit" class="rounded-[var(--radius-button)] bg-[var(--color-accent)] px-4 py-2.5 text-[0.9375rem] font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">Subscribe</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Link Columns -->
    <div class="border-y border-white/10">
        <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-8">
            <div class="grid grid-cols-1 gap-7 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                <!-- Shop -->
                <section>
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-[0.12em] text-gray-500">Shop</h3>
                    <ul class="grid gap-2.5 text-[0.9375rem]">
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/shop">All Products</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/shop?category=electronics">Electronics</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/shop?category=audio">Audio</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/shop?category=accessories">Accessories</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/shop?category=gaming">Gaming</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/shop?category=wearables">Wearables</a></li>
                    </ul>
                </section>

                <!-- Business -->
                <section>
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-[0.12em] text-gray-500">For Business</h3>
                    <ul class="grid gap-2.5 text-[0.9375rem]">
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/wholesale-partnerships">Wholesale</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/dealer-onboarding">Dealer Onboarding</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var,--color-accent)]" href="/b2b-contact">B2B Contact & Intake</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/advertise">Advertise with Us</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/priority-support">Priority Support</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/bulk-ordering">Bulk Ordering Guide</a></li>
                    </ul>
                </section>

                <!-- Account & Orders -->
                <section>
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-[0.12em] text-gray-500">Account & Orders</h3>
                    <ul class="grid gap-2.5 text-[0.9375rem]">
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/login">My Account</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/register">Create Account</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/cart">Shopping Cart</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/payment-methods">Payment Methods</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/returns-warranty">Returns & Warranty</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/order-issues">Order Issues Help</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/vat-invoices">VAT & Invoices</a></li>
                    </ul>
                </section>

                <!-- Support -->
                <section>
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-[0.12em] text-gray-500">Support</h3>
                    <ul class="grid gap-2.5 text-[0.9375rem]">
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/contact">Contact Us</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/faq">FAQ Hub</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/installation-guides">Installation Guides</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/compatibility">Compatibility Help</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/availability-restock">Availability & Restock</a></li>
                    </ul>
                </section>

                <!-- International -->
                <section>
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-[0.12em] text-gray-500">International</h3>
                    <ul class="grid gap-2.5 text-[0.9375rem]">
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/shipping-by-country">Shipping by Country</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/incoterms">Incoterms: DAP & DDP</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/customs-duties">Customs & Duties FAQ</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var,--color-accent)]" href="/international-returns">International Returns</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/customs-checklist">Customs Checklist</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/shipping-restrictions">Shipping Restrictions</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/exchange-policy-international">International Exchange Policy</a></li>
                    </ul>
                </section>

                <!-- Policies -->
                <section>
                    <h3 class="mb-3 text-xs font-semibold uppercase tracking-[0.12em] text-gray-500">Policies</h3>
                    <ul class="grid gap-2.5 text-[0.9375rem]">
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/terms-and-conditions">Terms & Conditions</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/privacy-policy">Privacy Policy</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/pre-order-policy">Pre-Order Policy</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/cookie-policy">Cookie Policy</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/returns-decision-tree">Returns Decision Tree</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/warranty-claim">Warranty Claim Checklist</a></li>
                        <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/warranty-exclusions">Warranty Exclusions</a></li>
                    </ul>
                </section>
            </div>

            <!-- Top Categories -->
            <div class="mt-7 border-t border-white/10 pt-6">
                <h3 class="mb-3 text-xs font-semibold uppercase tracking-[0.12em] text-gray-500">Top Categories</h3>
                <ul class="grid grid-cols-1 gap-x-8 gap-y-2.5 text-[0.9375rem] sm:grid-cols-2 lg:grid-cols-4">
                    <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/shop?category=audio">Audio & Headphones</a></li>
                    <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/shop?category=gaming">Gaming Peripherals</a></li>
                    <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/shop?category=accessories">Laptop Accessories</a></li>
                    <li><a class="text-gray-400 transition-colors hover:text-[var,--color-accent)]" href="/shop?category=wearables">Smartwatches & Trackers</a></li>
                    <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/shop?category=electronics">Monitors & Displays</a></li>
                    <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/shop?category=home">Smart Home</a></li>
                    <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/shop?category=accessories">Cables & Chargers</a></li>
                    <li><a class="text-gray-400 transition-colors hover:text-[var(--color-accent)]" href="/shop?category=accessories">Bags & Cases</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Region / Legal Bar -->
    <div class="border-b border-white/10">
        <div class="mx-auto flex w-[92%] flex-col gap-4 py-4 text-sm sm:w-[90%] md:w-[88%] lg:w-[85%] lg:flex-row lg:items-center lg:justify-between">
            <div class="flex flex-wrap items-center gap-3 text-gray-500">
                <span class="font-semibold text-white">Region</span>
                <span class="text-gray-400">Worldwide</span>
                <span class="font-semibold text-white">Currency</span>
                <span class="text-gray-400">USD ($)</span>
            </div>
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-gray-400">
                <a class="transition-colors hover:text-[var(--color-accent)]" href="/privacy-policy">Privacy</a>
                <a class="transition-colors hover:text-[var(--color-accent)]" href="/terms-and-conditions">Terms</a>
                <a class="transition-colors hover:text-[var(--color-accent)]" href="/cookie-policy">Cookies</a>
            </div>
        </div>
    </div>

    <!-- Copyright + Payment -->
    <div class="py-4 text-sm text-gray-500">
        <div class="mx-auto flex w-[92%] flex-wrap items-center justify-between gap-4 sm:w-[90%] md:w-[88%] lg:w-[85%]">
            <span>&copy; <?= date('Y') ?> Scooter Dynamics. All rights reserved. Powered by Structbrew Framework.</span>
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
