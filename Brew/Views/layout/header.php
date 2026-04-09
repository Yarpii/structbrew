<!-- Topbar -->
<div class="hidden xs:block border-b border-white/15 bg-zinc-900 text-[0.8125rem] text-white">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] flex items-center justify-between min-h-[2.5rem] py-0.5 gap-4 flex-wrap">
        <div class="hidden md:flex items-center gap-4">
            <span class="text-white/80">Free shipping on orders over $50</span>
        </div>
        <div class="flex items-center gap-3 ml-auto">
            <a href="/contact" class="hidden lg:inline text-white/80 hover:text-white transition-colors">Support: +1 800 555 1234</a>
            <a href="/login" class="text-white/80 hover:text-white transition-colors">Login / Account</a>
            <button
                type="button"
                class="inline-flex h-8 items-center gap-1.5 rounded-md border border-white/25 bg-white/10 px-2 text-white/85 transition-colors hover:border-white/50 hover:text-white"
                @click="$store.theme.toggle()"
                :aria-label="$store.theme.dark ? 'Switch to light mode' : 'Switch to dark mode'"
            >
                <template x-if="$store.theme.dark">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                </template>
                <template x-if="!$store.theme.dark">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </template>
            </button>
        </div>
    </div>
</div>

<!-- Main Header -->
<header
    x-data="{ mobileOpen: false, searchOpen: false }"
    class="sticky top-0 z-50 border-b bg-[var(--color-surface)] border-[var(--color-border)]"
    style="box-shadow: var(--shadow-sm)"
>
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] flex items-center justify-between gap-3 h-16">
        <!-- Logo -->
        <a href="/" class="flex items-center gap-2.5 shrink-0" aria-label="Scooter Dynamics">
            <img src="/assets/images/logo-dark.svg" alt="Scooter Dynamics" class="h-12 w-auto dark:hidden">
            <img src="/assets/images/logo-light.svg" alt="Scooter Dynamics" class="h-12 w-auto hidden dark:block">
        </a>

        <!-- Search (desktop) -->
        <div class="hidden sm:flex flex-1 max-w-xl mx-4">
            <form action="/shop" method="get" class="relative w-full">
                <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-muted)]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input
                    type="search" name="q"
                    placeholder="Search products..."
                    class="h-10 w-full rounded-[var(--radius-input)] border border-[var(--color-border)] bg-[var(--color-bg)] pl-10 pr-4 text-sm focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10"
                >
            </form>
        </div>

        <!-- Navigation (desktop) -->
        <nav class="hidden lg:flex items-center gap-5 text-sm font-medium shrink-0">
            <a href="/" class="text-[var(--color-text)] hover:text-[var(--color-accent)] transition-colors">Home</a>
            <a href="/shop" class="text-[var(--color-text)] hover:text-[var(--color-accent)] transition-colors">Shop</a>
            <a href="/about" class="text-[var(--color-text)] hover:text-[var(--color-accent]_transition-colors">About</a>
            <a href="/contact" class="text-[var(--color-text)] hover:text-[var(--color-accent)] transition-colors">Contact</a>
        </nav>

        <!-- Right actions -->
        <div class="flex items-center gap-2 shrink-0">
            <!-- Mobile search toggle -->
            <button
                @click="searchOpen = !searchOpen"
                class="sm:hidden inline-flex h-10 w-10 items-center justify-center rounded-[var(--radius-button)] border border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-muted)] hover:text-[var(--color-accent)] transition-colors"
            >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </button>

            <!-- Cart -->
            <a
                href="/cart"
                class="relative inline-flex h-10 items-center gap-2 rounded-[var(--radius-button)] bg-[var(--color-accent)] px-3.5 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]"
            >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                <span class="hidden xs:inline">Cart</span>
                <template x-if="$store.cart.count > 0">
                    <span class="absolute -top-1.5 -right-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-white text-[var(--color-accent)] text-[11px] font-bold shadow" x-text="$store.cart.count"></span>
                </template>
            </a>

            <!-- Mobile menu toggle -->
            <button
                @click="mobileOpen = !mobileOpen"
                class="lg:hidden inline-flex h-10 w-10 items-center justify-center rounded-[var(--radius-button)] border border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-text)] transition hover:border-[var(--color-accent)]"
            >
                <svg x-show="!mobileOpen" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                <svg x-show="mobileOpen" x-cloak width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
    </div>

    <!-- Mobile search bar -->
    <div x-show="searchOpen" x-cloak x-transition class="sm:hidden border-t border-[var(--color-border)] p-3">
        <form action="/shop" method="get" class="relative">
            <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-muted)]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="search" name="q" placeholder="Search products..." class="h-10 w-full rounded-[var(--radius-input)] border border-[var(--color-border)] bg-[var(--color-bg)] pl-10 pr-4 text-sm focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
        </form>
    </div>

    <!-- Mobile nav -->
    <nav x-show="mobileOpen" x-cloak x-transition.opacity.duration.150ms class="lg:hidden border-t border-[var(--color-border)] bg-[var(--color-surface)]">
        <div class="mx-auto w-[92%] py-3 space-y-1">
            <a href="/" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-[var(--color-text)] hover:bg-[var(--color-bg)] transition-colors">Home</a>
            <a href="/shop" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-[var(--color-text)] hover:bg-[var(--color-bg)] transition-colors">Shop</a>
            <a href="/about" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-[var(--color-text)] hover:bg-[var(--color-bg)] transition-colors">About</a>
            <a href="/contact" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-[var(--color-text)] hover:bg-[var(--color-bg)] transition-colors">Contact</a>
            <a href="/login" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-[var(--color-text)] hover:bg-[var(--color-bg)] transition-colors">Login</a>
            <a href="/register" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-[var(--color-accent)] hover:bg-[var(--color-bg)] transition-colors">Create Account</a>
        </div>
    </nav>
</header>
