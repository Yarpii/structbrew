<section class="bg-[var(--color-bg)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-12 md:py-16">
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-[var(--color-text)]">B2B Contact & Intake</h1>
        <p class="mt-3 text-[var(--color-muted)] max-w-2xl">Interested in a business partnership? Tell us about your company and we'll get back to you within 2 business days.</p>

        <div class="mt-10 grid gap-8 lg:grid-cols-[1fr,20rem]">
            <form class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-6 space-y-5"
                  x-data="{ submitted: false }" @submit.prevent="submitted = true">
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Full Name *</label>
                        <input type="text" required class="h-10 w-full rounded-[var(--radius-input)] border border-[var(--color-border)] bg-[var(--color-bg)] px-3 text-sm focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Company Name *</label>
                        <input type="text" required class="h-10 w-full rounded-[var(--radius-input)] border border-[var(--color-border)] bg-[var(--color-bg)] px-3 text-sm focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
                    </div>
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Email Address *</label>
                        <input type="email" required class="h-10 w-full rounded-[var(--radius-input)] border border-[var(--color-border)] bg-[var(--color-bg)] px-3 text-sm focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Phone</label>
                        <input type="tel" class="h-10 w-full rounded-[var(--radius-input)] border border-[var(--color-border)] bg-[var(--color-bg)] px-3 text-sm focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Partnership Type *</label>
                    <select required class="h-10 w-full rounded-[var(--radius-input)] border border-[var(--color-border)] bg-[var(--color-bg)] px-3 text-sm focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
                        <option value="">Select an option</option>
                        <option>Wholesale</option>
                        <option>Dealer / Reseller</option>
                        <option>Bulk Ordering</option>
                        <option>Advertising / Sponsorship</option>
                        <option>Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1">Message *</label>
                    <textarea required rows="4" class="w-full rounded-[var(--radius-input)] border border-[var,--color-border)] bg-[var(--color-bg)] px-3 py-2 text-sm focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10"></textarea>
                </div>
                <button type="submit" x-show="!submitted" class="inline-flex h-10 items-center gap-2 rounded-[var(--radius-button)] bg-[var(--color-accent)] px-5 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">
                    Submit Inquiry
                </button>
                <p x-show="submitted" x-cloak class="text-sm font-semibold text-emerald-600">Thank you! We'll be in touch within 2 business days.</p>
            </form>

            <div class="space-y-4">
                <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-5">
                    <h3 class="text-sm font-bold text-[var(--color-text)] mb-2">Direct Contact</h3>
                    <p class="text-sm text-[var(--color-muted)]">b2b@scooterdynamics.store</p>
                    <p class="text-sm text-[var,--color-muted)]">+1 800 555 1234 ext. 2</p>
                </div>
                <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-5">
                    <h3 class="text-sm font-bold text-[var(--color-text)] mb-2">Response Time</h3>
                    <p class="text-sm text-[var(--color-muted)]">We respond to all B2B inquiries within 2 business days.</p>
                </div>
            </div>
        </div>
    </div>
</section>
