<section class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-7 md:py-10" x-data="{ showPass: false, showConfirm: false, loading: false }">
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-[1.2fr_0.8fr]">
        <!-- Registration Form -->
        <div class="rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-5 md:p-7" style="box-shadow: var(--shadow-sm)">
            <div class="mb-5">
                <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-accent)]">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                </div>
                <h1 class="text-2xl font-extrabold tracking-tight md:text-3xl text-[var(--color-text)]">Create Account</h1>
                <p class="mt-2 text-[var(--color-muted)]">Fill in your details and create your account.</p>
            </div>

            <form class="space-y-4" @submit.prevent="loading = true; setTimeout(() => { loading = false; alert('Registration is a demo feature'); }, 1000)">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-[var(--color-text)]" for="reg-firstname">First Name</label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-muted)]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <input id="reg-firstname" type="text" required placeholder="John" autocomplete="given-name"
                                   class="h-11 w-full rounded-[var(--radius-input)] border border-[var(--color-border)] bg-[var(--color-bg)] pl-10 pr-3.5 text-[var(--color-text)] focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
                        </div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-[var(--color-text)]" for="reg-lastname">Last Name</label>
                        <div class="relative">
                            <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-muted)]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <input id="reg-lastname" type="text" required placeholder="Smith" autocomplete="family-name"
                                   class="h-11 w-full rounded-[var(--radius-input)] border border-[var(--color-border)] bg-[var(--color-bg)] pl-10 pr-3.5 text-[var(--color-text)] focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-[var(--color-text)]" for="reg-email">Email Address</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-muted)]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <input id="reg-email" type="email" required placeholder="name@example.com" autocomplete="email" spellcheck="false"
                               class="h-11 w-full rounded-[var(--radius-input)] border border-[var(--color-border)] bg-[var(--color-bg)] pl-10 pr-3.5 text-[var(--color-text)] focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-[var(--color-text)]" for="reg-pass">Password</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-muted)]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <input id="reg-pass" :type="showPass ? 'text' : 'password'" required placeholder="Minimum 8 characters" autocomplete="new-password" minlength="8"
                               class="h-11 w-full rounded-[var(--radius-input)] border border-[var(--color-border)] bg-[var(--color-bg)] pl-10 pr-11 text-[var(--color-text)] focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
                        <button type="button" @click="showPass = !showPass" class="absolute right-1.5 top-1/2 inline-flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full border border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-muted)] transition hover:border-[var(--color-accent)] hover:text-[var(--color-accent)]">
                            <svg x-show="!showPass" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-show="showPass" x-cloak width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-[var(--color-text)]" for="reg-pass-confirm">Confirm Password</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-muted)]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <input id="reg-pass-confirm" :type="showConfirm ? 'text' : 'password'" required placeholder="Repeat your password" autocomplete="new-password"
                               class="h-11 w-full rounded-[var(--radius-input)] border border-[var(--color-border)] bg-[var(--color-bg)] pl-10 pr-11 text-[var(--color-text)] focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
                        <button type="button" @click="showConfirm = !showConfirm" class="absolute right-1.5 top-1/2 inline-flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full border border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-muted)] transition hover:border-[var(--color-accent)] hover:text-[var(--color-accent)]">
                            <svg x-show="!showConfirm" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-show="showConfirm" x-cloak width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>

                <label class="inline-flex items-center gap-2 text-sm text-[var(--color-muted)]">
                    <input type="checkbox" name="newsletter" value="1" class="accent-[var(--color-accent)]">
                    <span>Subscribe to our newsletter for deals and updates</span>
                </label>

                <button class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-[var(--radius-button)] bg-[var(--color-accent)] px-4 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)] disabled:cursor-not-allowed disabled:opacity-75" type="submit" :disabled="loading">
                    <span x-show="!loading">Create Account</span>
                    <span x-show="loading" x-cloak class="animate-pulse">Creating...</span>
                    <svg x-show="!loading" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>

                <p class="text-sm text-[var(--color-muted)]">
                    Already have an account?
                    <a class="font-semibold text-[var(--color-accent)] hover:underline" href="/login">Log in here</a>
                </p>
            </form>
        </div>

        <!-- Security Benefits -->
        <div class="rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-5 md:p-7" style="box-shadow: var(--shadow-sm)">
            <div class="mb-5">
                <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-accent)]">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h2 class="text-2xl font-extrabold tracking-tight md:text-3xl text-[var(--color-text)]">Safe & Trusted</h2>
                <p class="mt-2 text-[var(--color-muted)]">Your data is in good hands.</p>
            </div>

            <ul class="space-y-2.5 text-sm">
                <li class="flex items-start gap-2.5 rounded-lg border border-[var(--color-border)] bg-[var(--color-bg)] px-3 py-2.5">
                    <svg class="shrink-0 mt-0.5" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    <span class="text-[var(--color-text)]">Secure SSL connection</span>
                </li>
                <li class="flex items-start gap-2.5 rounded-lg border border-[var(--color-border)] bg-[var(--color-bg)] px-3 py-2.5">
                    <svg class="shrink-0 mt-0.5" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    <span class="text-[var(--color-text)]">GDPR compliant data handling</span>
                </li>
                <li class="flex items-start gap-2.5 rounded-lg border border-[var(--color-border)] bg-[var(--color-bg)] px-3 py-2.5">
                    <svg class="shrink-0 mt-0.5" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    <span class="text-[var(--color-text)]">Fast checkout with saved addresses</span>
                </li>
                <li class="flex items-start gap-2.5 rounded-lg border border-[var(--color-border)] bg-[var(--color-bg)] px-3 py-2.5">
                    <svg class="shrink-0 mt-0.5" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    <span class="text-[var(--color-text)]">Exclusive deals for account holders</span>
                </li>
                <li class="flex items-start gap-2.5 rounded-lg border border-[var(--color-border)] bg-[var(--color-bg)] px-3 py-2.5">
                    <svg class="shrink-0 mt-0.5" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    <span class="text-[var(--color-text)]">Full insight into your orders</span>
                </li>
            </ul>
        </div>
    </div>
</section>
