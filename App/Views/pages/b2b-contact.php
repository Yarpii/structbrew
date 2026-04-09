<section class="bg-[var(--color-bg)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-12 md:py-16 space-y-8">
        <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-6 md:p-8" style="box-shadow: var(--shadow-sm)">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[var(--color-accent)]">B2B Intake</p>
            <h1 class="mt-2 text-3xl md:text-4xl font-extrabold tracking-tight text-[var(--color-text)]">B2B Contact & Intake</h1>
            <p class="mt-3 max-w-3xl text-[var(--color-muted)]">Interested in wholesale, dealership, or strategic collaboration? Share your business profile and goals, and our partnerships team will respond within 2 business days.</p>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.35fr_1fr]">
            <form class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-6 space-y-5"
                  style="box-shadow: var(--shadow-sm)"
                  x-data="{ submitted: false }" @submit.prevent="submitted = true">
                <h2 class="text-lg font-bold text-[var,--color-text)]">Business Inquiry Form</h2>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-[var(--color-text)]">Full Name *</label>
                        <input type="text" required class="h-10 w-full rounded-md border border-[var(--color-border)] bg-[var,--color-bg)] px-3 text-sm focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-[var(--color-text)]">Company Name *</label>
                        <input type="text" required class="h-10 w-full rounded-md border border-[var(--color-border)] bg-[var,--color-bg)] px-3 text-sm focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-[var(--color-text)]">Email Address *</label>
                        <input type="email" required class="h-10 w-full rounded-md border border-[var(--color-border)] bg-[var,--color-bg)] px-3 text-sm focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-[var(--color-text)]">Phone</label>
                        <input type="tel" class="h-10 w-full rounded-md border border-[var(--color-border)] bg-[var,--color-bg)] px-3 text-sm focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var,--color-accent)]/10">
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-[var(--color-text)]">Country / Region *</label>
                        <input type="text" required class="h-10 w-full rounded-md border border-[var(--color-border)] bg-[var,--color-bg)] px-3 text-sm focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-[var(--color-text)]">Partnership Type *</label>
                        <select required class="h-10 w-full rounded-md border border-[var(--color-border)] bg-[var,--color-bg)] px-3 text-sm focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var,--color-accent)]/10">
                            <option value="">Select an option</option>
                            <option>Wholesale</option>
                            <option>Dealer / Reseller</option>
                            <option>Bulk Ordering</option>
                            <option>Advertising / Sponsorship</option>
                            <option>Other</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-[var(--color-text)]">Message *</label>
                    <textarea required rows="5" class="w-full rounded-md border border-[var,--color-border)] bg-[var,--color-bg)] px-3 py-2 text-sm focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10"></textarea>
                </div>

                <button type="submit" x-show="!submitted" class="inline-flex h-10 items-center gap-2 rounded-md bg-[var(--color-accent)] px-5 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">
                    Submit Inquiry
                </button>
                <p x-show="submitted" x-cloak class="text-sm font-semibold text-emerald-600">Thank you. Our partnerships team will contact you within 2 business days.</p>
            </form>

            <div class="space-y-4">
                <div class="rounded-md border border-[var(--color-border)] bg-[var,--color-surface)] p-5" style="box-shadow: var(--shadow-sm)">
                    <h3 class="text-sm font-bold text-[var(--color-text)]">Direct Contact</h3>
                    <p class="mt-2 text-sm text-[var(--color-muted)]">b2b@scooterdynamics.store</p>
                    <p class="text-sm text-[var(--color-muted)]">+1 800 555 1234 ext. 2</p>
                </div>

                <div class="rounded-md border border-[var(--color-border)] bg-[var,--color-surface)] p-5" style="box-shadow: var(--shadow-sm)">
                    <h3 class="text-sm font-bold text-[var(--color-text)]">What to include</h3>
                    <ul class="mt-2 space-y-1 text-sm text-[var,--color-muted)]">
                        <li>• Business type and sales channel</li>
                        <li>• Estimated monthly order volume</li>
                        <li>• Target product categories</li>
                        <li>• Country/region of operation</li>
                    </ul>
                </div>

                <div class="rounded-md border border-[var(--color-border)] bg-[var,--color-surface)] p-5" style="box-shadow: var(--shadow-sm)">
                    <h3 class="text-sm font-bold text-[var,--color-text)]">Related Resources</h3>
                    <div class="mt-2 space-y-1 text-sm">
                        <a href="/dealer-onboarding" class="block text-[var(--color-accent)] hover:underline">Dealer Onboarding</a>
                        <a href="/wholesale-partnerships" class="block text-[var(--color-accent)] hover:underline">Wholesale Partnerships</a>
                        <a href="/priority-support" class="block text-[var(--color-accent)] hover:underline">Priority Support</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
