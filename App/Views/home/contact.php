<section class="mx-auto w-full max-w-6xl space-y-8 px-4 py-10 sm:px-6 lg:px-8" x-data="{ loading: false }">
    <div class="rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)]/90 p-6 sm:p-8" style="box-shadow: var(--shadow-sm)">
        <h1 class="text-3xl font-bold tracking-tight text-[var(--color-text)]">Get in Touch</h1>
        <p class="mt-2 text-[var(--color-muted)]">We'd love to hear from you — send us a message or reach us directly.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)]">
        <!-- Contact Form -->
        <div class="rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-6" style="box-shadow: var(--shadow-sm)">
            <h2 class="text-xl font-semibold text-[var(--color-text)]">Send us a message</h2>
            <form class="mt-4 space-y-4" @submit.prevent="loading = true; setTimeout(() => { loading = false; alert('Message sent! (demo)'); }, 1000)">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-[var(--color-text)]" for="contact-name">Name <span class="text-[var(--color-accent)]">*</span></label>
                        <input id="contact-name" type="text" name="name" required autocomplete="name"
                               class="w-full rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] px-3 py-2.5 text-sm text-[var(--color-text)] transition focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-[var(--color-text)]" for="contact-email">Email <span class="text-[var(--color-accent)]">*</span></label>
                        <input id="contact-email" type="email" name="email" required autocomplete="email" spellcheck="false"
                               class="w-full rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] px-3 py-2.5 text-sm text-[var(--color-text)] transition focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-[var(--color-text)]" for="contact-phone">Phone</label>
                        <input id="contact-phone" type="tel" name="phone" autocomplete="tel"
                               class="w-full rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] px-3 py-2.5 text-sm text-[var(--color-text)] transition focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-[var(--color-text)]" for="contact-subject">Subject <span class="text-[var(--color-accent)]">*</span></label>
                        <select id="contact-subject" name="subject" required
                                class="w-full rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] px-3 py-2.5 text-sm text-[var(--color-text)] transition focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
                            <option value="" disabled selected>Choose a subject</option>
                            <option value="order">Order question</option>
                            <option value="return">Return request</option>
                            <option value="technical">Technical question</option>
                            <option value="product">Product compatibility</option>
                            <option value="shipping">Delivery / Tracking</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-medium text-[var(--color-text)]" for="contact-message">Message <span class="text-[var(--color-accent)]">*</span></label>
                    <textarea id="contact-message" name="message" required rows="6"
                              class="w-full rounded-xl border border-[var(--color-border)] bg-[var(--color-bg)] px-3 py-2.5 text-sm text-[var(--color-text)] transition focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10 resize-vertical"></textarea>
                </div>

                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-[var(--color-accent)] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)] disabled:cursor-not-allowed disabled:opacity-70" :disabled="loading">
                    <span x-show="!loading">Send Message</span>
                    <span x-show="loading" x-cloak class="animate-pulse">Sending...</span>
                </button>
            </form>
        </div>

        <!-- Contact Info -->
        <div class="space-y-4">
            <div class="rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-5" style="box-shadow: var(--shadow-sm)">
                <h2 class="text-lg font-semibold text-[var(--color-text)]">Direct Contact</h2>
                <ul class="mt-3 space-y-2 text-sm">
                    <li><a class="text-[var(--color-accent)] hover:underline" href="tel:+18005551234">+1 800 555 1234</a></li>
                    <li><a class="text-[var(--color-accent)] hover:underline" href="mailto:support@scooterdynamics.store">support@scooterdynamics.store</a></li>
                    <li class="text-[var(--color-muted)]">Mon - Fri: 09:00 - 17:00</li>
                </ul>
            </div>

            <div class="rounded-2xl border border-[var(--color-border)] bg-[var(--color-surface)] p-5" style="box-shadow: var(--shadow-sm)">
                <h3 class="text-base font-semibold text-[var(--color-text)]">Quick Links</h3>
                <ul class="mt-3 space-y-2 text-sm">
                    <li><a class="text-[var(--color-accent)] hover:underline" href="/shop">Browse Products</a></li>
                    <li><a class="text-[var(--color-accent)] hover:underline" href="/cart">View Cart</a></li>
                    <li><a class="text-[var(--color-accent)] hover:underline" href="/login">My Account</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>
