<section class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-7 md:py-10" x-data="{ showPass: false, loading: false }">
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
        <!-- Login Form -->
        <div class="rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-5 md:p-7" style="box-shadow: var(--shadow-sm)">
            <div class="mb-5">
                <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-accent)]">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                </div>
                <h1 class="text-2xl font-extrabold tracking-tight md:text-3xl text-[var(--color-text)]">Login</h1>
                <p class="mt-2 text-[var(--color-muted)]">Welcome back! Log in to your account to continue.</p>
            </div>

            <form class="space-y-4" @submit.prevent="loading = true; setTimeout(() => { loading = false; alert('Login is a demo feature'); }, 1000)">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-[var(--color-text)]" for="login-email">Email Address</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-muted)]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <input id="login-email" type="email" required autocomplete="email" placeholder="name@example.com" spellcheck="false"
                               class="h-11 w-full rounded-[var(--radius-input)] border border-[var(--color-border)] bg-[var(--color-bg)] pl-10 pr-3.5 text-[var(--color-text)] focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
                    </div>
                </div>

                <div>
                    <div class="mb-1.5 flex items-center justify-between gap-2">
                        <label class="block text-sm font-semibold text-[var(--color-text)]" for="login-pass">Password</label>
                        <a class="text-sm text-[var(--color-accent)] hover:underline" href="#">Forgot password?</a>
                    </div>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-muted)]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <input id="login-pass" :type="showPass ? 'text' : 'password'" required autocomplete="current-password" placeholder="Your password"
                               class="h-11 w-full rounded-[var(--radius-input)] border border-[var(--color-border)] bg-[var(--color-bg)] pl-10 pr-11 text-[var(--color-text)] focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
                        <button type="button" @click="showPass = !showPass"
                                class="absolute right-1.5 top-1/2 inline-flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full border border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-muted)] transition hover:border-[var(--color-accent)] hover:text-[var(--color-accent)]">
                            <svg x-show="!showPass" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-show="showPass" x-cloak width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>

                <button class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-[var(--radius-button)] bg-[var(--color-accent)] px-4 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)] disabled:cursor-not-allowed disabled:opacity-75" type="submit" :disabled="loading">
                    <span x-show="!loading">Login</span>
                    <span x-show="loading" x-cloak class="animate-pulse">Signing in...</span>
                    <svg x-show="!loading" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>
            </form>
        </div>

        <!-- Benefits -->
        <div class="rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-5 md:p-7" style="box-shadow: var(--shadow-sm)">
            <div class="mb-5">
                <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-accent)]">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                </div>
                <h2 class="text-2xl font-extrabold tracking-tight md:text-3xl text-[var(--color-text)]">New here?</h2>
                <p class="mt-2 text-[var(--color-muted)]">Create an account and enjoy exclusive benefits.</p>
            </div>

            <ul class="space-y-2.5 text-sm">
                <li class="flex items-start gap-2.5 rounded-lg border border-[var(--color-border)] bg-[var(--color-bg)] px-3 py-2.5">
                    <svg class="shrink-0 mt-0.5" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    <span class="text-[var(--color-text)]">Order history and tracking</span>
                </li>
                <li class="flex items-start gap-2.5 rounded-lg border border-[var(--color-border)] bg-[var(--color-bg)] px-3 py-2.5">
                    <svg class="shrink-0 mt-0.5" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    <span class="text-[var(--color-text)]">Address book for fast checkout</span>
                </li>
                <li class="flex items-start gap-2.5 rounded-lg border border-[var(--color-border)] bg-[var(--color-bg)] px-3 py-2.5">
                    <svg class="shrink-0 mt-0.5" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    <span class="text-[var(--color-text)]">Wishlist and product alerts</span>
                </li>
                <li class="flex items-start gap-2.5 rounded-lg border border-[var(--color-border)] bg-[var(--color-bg)] px-3 py-2.5">
                    <svg class="shrink-0 mt-0.5" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    <span class="text-[var(--color-text)]">Exclusive offers and discounts</span>
                </li>
            </ul>

            <a class="mt-5 inline-flex items-center gap-2 rounded-[var(--radius-button)] border border-[var(--color-accent)] bg-[var(--color-accent)] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]" href="/register">
                Create Account
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
    </div>
</section>
