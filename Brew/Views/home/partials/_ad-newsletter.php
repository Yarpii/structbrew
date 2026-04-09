<!-- Ad Banner: Newsletter / Seasonal -->
<section class="relative overflow-hidden bg-[var(--color-surface)] border-y border-[var(--color-border)]">
    <div class="absolute inset-0 opacity-5">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-[var(--color-accent)] rounded-full blur-[150px]"></div>
    </div>
    <div class="relative mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-14 text-center">
        <div class="max-w-xl mx-auto">
            <div class="mx-auto w-14 h-14 rounded-2xl bg-[var(--color-accent)]/10 flex items-center justify-center text-[var(--color-accent)] mb-5">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </div>
            <h2 class="text-2xl font-bold text-[var(--color-text)]">Stay in the Loop</h2>
            <p class="mt-2 text-sm text-[var(--color-muted)] leading-relaxed">
                Get exclusive deals, new product drops, and tech tips — straight to your inbox. No spam, unsubscribe anytime.
            </p>
            <form class="mt-6 flex flex-col sm:flex-row gap-3 max-w-md mx-auto" @submit.prevent="alert('Subscribed! (demo)')">
                <input type="email" required placeholder="your@email.com" autocomplete="email" spellcheck="false"
                       class="flex-1 rounded-[var(--radius-button)] border border-[var(--color-border)] bg-[var(--color-bg)] px-4 py-3 text-sm text-[var(--color-text)] transition focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
                <button type="submit" class="inline-flex h-12 items-center justify-center rounded-[var(--radius-button)] bg-[var(--color-accent)] px-6 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)] shrink-0">
                    Subscribe
                </button>
            </form>
            <p class="mt-3 text-xs text-[var(--color-muted)]">Join 12,000+ subscribers. Unsubscribe anytime.</p>
        </div>
    </div>
</section>
