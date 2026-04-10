<?php
$csrfToken    = $csrfToken ?? '';
$flashSuccess = $flashSuccess ?? null;
$flashError   = $flashError ?? null;
?>
<section class="mx-auto w-full max-w-6xl space-y-6 px-4 py-10 sm:px-6 lg:px-8"
         x-data="{ panel: 'none', loading: false, selectedDepartment: '' }">

    <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)]/90 p-6 sm:p-8" style="box-shadow: var(--shadow-sm)">
        <h1 class="text-3xl font-bold tracking-tight text-[var(--color-text)]">Contact Us</h1>
        <p class="mt-2 text-[var(--color-muted)]">Choose what you need help with and the right form will appear below.</p>
    </div>

    <!-- 3-card chooser -->
    <div class="grid gap-4 sm:grid-cols-3">

        <!-- General Support -->
        <button type="button" @click="panel = (panel === 'support' ? 'none' : 'support')"
                :class="panel === 'support' ? 'border-[var(--color-accent)] ring-1 ring-[var(--color-accent)]' : 'border-[var(--color-border)] hover:border-[var(--color-accent)]/60'"
                class="group flex flex-col items-start rounded-md border bg-[var(--color-surface)] p-5 text-left transition" style="box-shadow: var(--shadow-sm)">
            <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-md bg-[var(--color-accent)]/10 text-[var(--color-accent)]">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
            </div>
            <p class="text-sm font-semibold text-[var(--color-text)]">General Support</p>
            <p class="mt-1 text-xs text-[var(--color-muted)]">Order issues, returns, product questions and everything else.</p>
            <span class="mt-3 text-xs font-medium text-[var(--color-accent)]" x-text="panel === 'support' ? '&#9650; Close' : 'Open form \u2192'"></span>
        </button>

        <!-- Dealer Application -->
        <button type="button" @click="panel = (panel === 'dealer' ? 'none' : 'dealer')"
                :class="panel === 'dealer' ? 'border-[var(--color-accent)] ring-1 ring-[var(--color-accent)]' : 'border-[var(--color-border)] hover:border-[var(--color-accent)]/60'"
                class="group flex flex-col items-start rounded-md border bg-[var(--color-surface)] p-5 text-left transition" style="box-shadow: var(--shadow-sm)">
            <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-md bg-[var(--color-accent)]/10 text-[var(--color-accent)]">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            </div>
            <p class="text-sm font-semibold text-[var(--color-text)]">Become a Dealer</p>
            <p class="mt-1 text-xs text-[var(--color-muted)]">Apply for a dealer account with wholesale pricing and business terms.</p>
            <span class="mt-3 text-xs font-medium text-[var(--color-accent)]" x-text="panel === 'dealer' ? '&#9650; Close' : 'Open form \u2192'"></span>
        </button>

        <!-- Partner Application -->
        <button type="button" @click="panel = (panel === 'partner' ? 'none' : 'partner')"
                :class="panel === 'partner' ? 'border-[var(--color-accent)] ring-1 ring-[var(--color-accent)]' : 'border-[var(--color-border)] hover:border-[var(--color-accent)]/60'"
                class="group flex flex-col items-start rounded-md border bg-[var(--color-surface)] p-5 text-left transition" style="box-shadow: var(--shadow-sm)">
            <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-md bg-[var(--color-accent)]/10 text-[var(--color-accent)]">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            <p class="text-sm font-semibold text-[var(--color-text)]">Partner Program</p>
            <p class="mt-1 text-xs text-[var(--color-muted)]">Join as an affiliate partner and earn commission on every sale you refer.</p>
            <span class="mt-3 text-xs font-medium text-[var(--color-accent)]" x-text="panel === 'partner' ? '&#9650; Close' : 'Open form \u2192'"></span>
        </button>

    </div>

    <!-- General Support form -->
    <div x-show="panel === 'support'" x-collapse x-cloak>
        <div class="grid gap-6 lg:grid-cols-[minmax(0,1.5fr)_minmax(0,1fr)]">
            <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-6" style="box-shadow: var(--shadow-sm)">
                <h2 class="text-xl font-semibold text-[var(--color-text)]">Send us a message</h2>
                <form class="mt-4 space-y-4" @submit.prevent="loading = true; setTimeout(() => { loading = false; alert('Message sent! (demo)'); }, 1000)">
                    <div>
                        <label class="text-sm font-medium text-[var(--color-text)]" for="contact-department">Department <span class="text-[var(--color-accent)]">*</span></label>
                        <select id="contact-department" name="department_id" x-model="selectedDepartment" required
                                class="mt-1 w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3 py-2.5 text-sm text-[var(--color-text)] transition focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
                            <option value="" disabled>Choose department</option>
                            <?php foreach (($departments ?? []) as $department): ?>
                                <option value="<?= (int) $department['id'] ?>"><?= htmlspecialchars($department['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-[var(--color-text)]" for="contact-name">Name <span class="text-[var(--color-accent)]">*</span></label>
                            <input id="contact-name" type="text" name="name" required autocomplete="name"
                                   class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3 py-2.5 text-sm text-[var(--color-text)] transition focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-[var(--color-text)]" for="contact-email">Email <span class="text-[var(--color-accent)]">*</span></label>
                            <input id="contact-email" type="email" name="email" required autocomplete="email" spellcheck="false"
                                   class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3 py-2.5 text-sm text-[var(--color-text)] transition focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-[var(--color-text)]" for="contact-phone">Phone</label>
                            <input id="contact-phone" type="tel" name="phone" autocomplete="tel"
                                   class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3 py-2.5 text-sm text-[var(--color-text)] transition focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-[var(--color-text)]" for="contact-subject">Subject <span class="text-[var(--color-accent)]">*</span></label>
                            <input id="contact-subject" type="text" name="subject" required
                                   class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3 py-2.5 text-sm text-[var(--color-text)] transition focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10"
                                   placeholder="Briefly describe your request">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-[var(--color-text)]" for="contact-message">Message <span class="text-[var(--color-accent)]">*</span></label>
                        <textarea id="contact-message" name="message" required rows="6"
                                  class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3 py-2.5 text-sm text-[var(--color-text)] transition focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10 resize-vertical"></textarea>
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center rounded-md bg-[var(--color-accent)] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)] disabled:cursor-not-allowed disabled:opacity-70" :disabled="loading">
                        <span x-show="!loading">Send Message</span>
                        <span x-show="loading" x-cloak class="animate-pulse">Sending...</span>
                    </button>
                </form>
            </div>

            <div class="space-y-4">
                <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-5" style="box-shadow: var(--shadow-sm)">
                    <h2 class="text-lg font-semibold text-[var(--color-text)]">Direct Contact</h2>
                    <ul class="mt-3 space-y-2 text-sm">
                        <li><a class="text-[var(--color-accent)] hover:underline" href="tel:+18005551234">+1 800 555 1234</a></li>
                        <li><a class="text-[var(--color-accent)] hover:underline" href="mailto:support@scooterdynamics.store">support@scooterdynamics.store</a></li>
                        <li class="text-[var(--color-muted)]">Mon - Fri: 09:00 - 17:00</li>
                    </ul>
                    <div class="mt-4 border-t border-[var(--color-border)] pt-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-[var(--color-muted)]">B2B &amp; Wholesale</p>
                        <ul class="mt-2 space-y-1.5 text-sm">
                            <li><a class="text-[var(--color-accent)] hover:underline" href="mailto:b2b@scooterdynamics.store">b2b@scooterdynamics.store</a></li>
                            <li><a class="text-[var(--color-accent)] hover:underline" href="/b2b-contact">View B2B programs</a></li>
                        </ul>
                    </div>
                </div>
                <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-5" style="box-shadow: var(--shadow-sm)">
                    <h3 class="text-base font-semibold text-[var(--color-text)]">Quick Links</h3>
                    <ul class="mt-3 space-y-2 text-sm">
                        <li><a class="text-[var(--color-accent)] hover:underline" href="/support">Support Center</a></li>
                        <li><a class="text-[var(--color-accent)] hover:underline" href="/account/tickets">My Tickets</a></li>
                        <li><a class="text-[var(--color-accent)] hover:underline" href="/account/tickets/create">Open Ticket</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Dealer Application form -->
    <div x-show="panel === 'dealer'" x-collapse x-cloak>
        <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-6 md:p-8" style="box-shadow: var(--shadow-sm)">
            <h2 class="text-xl font-bold text-[var(--color-text)]">Apply to become a dealer</h2>
            <p class="mt-1 text-sm text-[var(--color-muted)]">Fill out the form below and our B2B team will review your application within 2&ndash;3 business days.</p>

            <form method="POST" action="/dealer/apply" class="mt-6 space-y-5">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-[var(--color-text)] mb-1.5">Company Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="company_name" required maxlength="191"
                               class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 py-2.5 text-sm text-[var(--color-text)] placeholder-[var(--color-muted)] focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[var(--color-text)] mb-1.5">Contact Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="contact_name" required maxlength="191"
                               class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 py-2.5 text-sm text-[var(--color-text)] placeholder-[var(--color-muted)] focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] focus:outline-none">
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-[var(--color-text)] mb-1.5">Email Address <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" required
                               class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 py-2.5 text-sm text-[var(--color-text)] placeholder-[var(--color-muted)] focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[var(--color-text)] mb-1.5">Phone</label>
                        <input type="tel" name="phone" maxlength="50"
                               class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 py-2.5 text-sm text-[var(--color-text)] placeholder-[var(--color-muted)] focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] focus:outline-none">
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-[var(--color-text)] mb-1.5">Country</label>
                        <input type="text" name="country" maxlength="100" placeholder="e.g. Netherlands"
                               class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 py-2.5 text-sm text-[var(--color-text)] placeholder-[var(--color-muted)] focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[var(--color-text)] mb-1.5">Business Type</label>
                        <select name="business_type"
                                class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 py-2.5 text-sm text-[var(--color-text)] focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] focus:outline-none">
                            <option value="retailer">Retailer</option>
                            <option value="webshop">Webshop</option>
                            <option value="workshop">Workshop / Service Center</option>
                            <option value="distributor">Distributor</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-[var(--color-text)] mb-1.5">VAT / Tax Number</label>
                        <input type="text" name="vat_number" maxlength="100" placeholder="e.g. NL123456789B01"
                               class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 py-2.5 text-sm text-[var(--color-text)] placeholder-[var(--color-muted)] focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[var(--color-text)] mb-1.5">Est. Annual Order Volume</label>
                        <select name="annual_volume"
                                class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 py-2.5 text-sm text-[var(--color-text)] focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] focus:outline-none">
                            <option value="">Select a range</option>
                            <option value="Under EUR 5,000">Under &euro;5,000</option>
                            <option value="EUR 5,000-25,000">&euro;5,000 &ndash; &euro;25,000</option>
                            <option value="EUR 25,000-100,000">&euro;25,000 &ndash; &euro;100,000</option>
                            <option value="EUR 100,000+">&euro;100,000+</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1.5">Website</label>
                    <input type="url" name="website" maxlength="255" placeholder="https://yourstore.com"
                           class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 py-2.5 text-sm text-[var(--color-text)] placeholder-[var(--color-muted)] focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1.5">Tell us about your business</label>
                    <textarea name="message" rows="4" maxlength="3000" placeholder="Describe your sales channels, customer base and why you'd like to become an authorized dealer."
                              class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 py-2.5 text-sm text-[var(--color-text)] placeholder-[var(--color-muted)] focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] focus:outline-none resize-none"></textarea>
                </div>
                <div class="flex items-center gap-4 pt-1">
                    <button type="submit" class="inline-flex h-10 items-center gap-2 rounded-md bg-[var(--color-accent)] px-6 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">
                        Submit Application
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                    <a href="/dealer-onboarding" class="text-sm text-[var(--color-muted)] hover:text-[var(--color-accent)] hover:underline">Learn more about the dealer program</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Partner Application form -->
    <div x-show="panel === 'partner'" x-collapse x-cloak>
        <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-6 md:p-8" style="box-shadow: var(--shadow-sm)">
            <h2 class="text-xl font-bold text-[var(--color-text)]">Apply to join the Partner Program</h2>
            <p class="mt-1 text-sm text-[var(--color-muted)]">Fill out the form below and our team will review your application within 2&ndash;3 business days.</p>

            <form method="POST" action="/partner-program/apply" class="mt-6 space-y-5">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-[var(--color-text)] mb-1.5">First name <span class="text-rose-500">*</span></label>
                        <input type="text" name="first_name" required maxlength="100"
                               class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 py-2.5 text-sm text-[var(--color-text)] placeholder-[var(--color-muted)] focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[var(--color-text)] mb-1.5">Last name <span class="text-rose-500">*</span></label>
                        <input type="text" name="last_name" required maxlength="100"
                               class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 py-2.5 text-sm text-[var(--color-text)] placeholder-[var(--color-muted)] focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1.5">Email address <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" required
                           class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 py-2.5 text-sm text-[var(--color-text)] placeholder-[var(--color-muted)] focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] focus:outline-none">
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-[var(--color-text)] mb-1.5">Company / Brand</label>
                        <input type="text" name="company" maxlength="191" placeholder="Optional"
                               class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 py-2.5 text-sm text-[var(--color-text)] placeholder-[var(--color-muted)] focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[var(--color-text)] mb-1.5">Country</label>
                        <input type="text" name="country" maxlength="100" placeholder="e.g. United Kingdom"
                               class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 py-2.5 text-sm text-[var(--color-text)] placeholder-[var(--color-muted)] focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1.5">Website / Social / Channel URL</label>
                    <input type="url" name="website" maxlength="255" placeholder="https://yourchannel.com"
                           class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 py-2.5 text-sm text-[var(--color-text)] placeholder-[var(--color-muted)] focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-[var(--color-text)] mb-1.5">Tell us about yourself</label>
                    <textarea name="message" rows="4" maxlength="3000" placeholder="How do you plan to promote our products? What's your audience size or channel reach?"
                              class="w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 py-2.5 text-sm text-[var(--color-text)] placeholder-[var(--color-muted)] focus:border-[var(--color-accent)] focus:ring-1 focus:ring-[var(--color-accent)] focus:outline-none resize-none"></textarea>
                </div>
                <div class="flex items-center gap-4 pt-1">
                    <button type="submit" class="inline-flex h-10 items-center gap-2 rounded-md bg-[var(--color-accent)] px-6 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">
                        Submit Application
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                    <a href="/partner-program" class="text-sm text-[var(--color-muted)] hover:text-[var(--color-accent)] hover:underline">Learn more about the partner program</a>
                </div>
            </form>
        </div>
    </div>

</section>
