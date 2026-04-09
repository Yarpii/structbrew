<section class="bg-[var(--color-bg)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-12 md:py-16">
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-[var(--color-text)]">Returns Decision Tree</h1>
        <p class="mt-3 text-[var(--color-muted)] max-w-2xl">Not sure if your item qualifies for a return? Follow the steps below to find out.</p>

        <div class="mt-10" x-data="{ step: 1 }">
            <!-- Step 1 -->
            <div x-show="step === 1" class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-6">
                <p class="text-sm font-bold text-[var(--color-accent)] uppercase tracking-wide mb-2">Step 1</p>
                <h2 class="text-lg font-bold text-[var(--color-text)] mb-4">Was the item delivered within the last 30 days?</h2>
                <div class="flex gap-3">
                    <button @click="step = 2" class="inline-flex h-10 items-center rounded-[var(--radius-button)] bg-[var(--color-accent)] px-5 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">Yes</button>
                    <button @click="step = 10" class="inline-flex h-10 items-center rounded-[var(--radius-button)] border border-[var(--color-border)] bg-[var(--color-surface)] px-5 text-sm font-semibold text-[var(--color-text)] transition hover:border-[var(--color-accent)]">No</button>
                </div>
            </div>

            <!-- Step 2 -->
            <div x-show="step === 2" x-cloak class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-6">
                <p class="text-sm font-bold text-[var(--color-accent)] uppercase tracking-wide mb-2">Step 2</p>
                <h2 class="text-lg font-bold text-[var(--color-text)] mb-4">Is the item defective or damaged?</h2>
                <div class="flex gap-3">
                    <button @click="step = 20" class="inline-flex h-10 items-center rounded-[var(--radius-button)] bg-[var(--color-accent)] px-5 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">Yes, it's defective</button>
                    <button @click="step = 3" class="inline-flex h-10 items-center rounded-[var(--radius-button)] border border-[var(--color-border)] bg-[var(--color-surface)] px-5 text-sm font-semibold text-[var(--color-text)] transition hover:border-[var(--color-accent)]">No, it's fine</button>
                </div>
            </div>

            <!-- Step 3 -->
            <div x-show="step === 3" x-cloak class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-6">
                <p class="text-sm font-bold text-[var(--color-accent)] uppercase tracking-wide mb-2">Step 3</p>
                <h2 class="text-lg font-bold text-[var(--color-text)] mb-4">Is the item unused and in its original packaging?</h2>
                <div class="flex gap-3">
                    <button @click="step = 30" class="inline-flex h-10 items-center rounded-[var(--radius-button)] bg-[var(--color-accent)] px-5 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">Yes</button>
                    <button @click="step = 31" class="inline-flex h-10 items-center rounded-[var(--radius-button)] border border-[var(--color-border)] bg-[var(--color-surface)] px-5 text-sm font-semibold text-[var(--color-text)] transition hover:border-[var(--color-accent)]">No</button>
                </div>
            </div>

            <!-- Result: Outside 30 days -->
            <div x-show="step === 10" x-cloak class="rounded-[var(--radius-card)] border border-amber-200 dark:border-amber-900/40 bg-amber-50 dark:bg-amber-950/15 p-6">
                <h2 class="text-lg font-bold text-[var(--color-text)] mb-2">Outside return window</h2>
                <p class="text-sm text-[var(--color-muted)]">Our standard return period is 30 days from delivery. However, if the item is defective, it may still be covered under our <a href="/warranty-claim" class="text-[var(--color-accent)] hover:underline">12-month warranty</a>. <a href="/contact" class="text-[var(--color-accent)] hover:underline">Contact us</a> and we'll see what we can do.</p>
                <button @click="step = 1" class="mt-4 text-sm font-semibold text-[var(--color-accent)] hover:underline">Start over</button>
            </div>

            <!-- Result: Defective -->
            <div x-show="step === 20" x-cloak class="rounded-[var(--radius-card)] border border-emerald-200 dark:border-emerald-900/40 bg-emerald-50 dark:bg-emerald-950/15 p-6">
                <h2 class="text-lg font-bold text-emerald-700 dark:text-emerald-400 mb-2">Eligible for free return</h2>
                <p class="text-sm text-[var(--color-muted)]">Defective items qualify for a free return with a prepaid shipping label. We'll send a replacement or issue a full refund — your choice. <a href="/contact" class="text-[var(--color-accent)] hover:underline">Contact us</a> with your order number and photos of the defect.</p>
                <button @click="step = 1" class="mt-4 text-sm font-semibold text-[var(--color-accent)] hover:underline">Start over</button>
            </div>

            <!-- Result: Eligible -->
            <div x-show="step === 30" x-cloak class="rounded-[var(--radius-card)] border border-emerald-200 dark:border-emerald-900/40 bg-emerald-50 dark:bg-emerald-950/15 p-6">
                <h2 class="text-lg font-bold text-emerald-700 dark:text-emerald-400 mb-2">Eligible for return</h2>
                <p class="text-sm text-[var(--color-muted)]">Your item qualifies for a return. Return shipping is at your expense for change-of-mind returns. <a href="/contact" class="text-[var(--color-accent)] hover:underline">Contact us</a> to start the process. Refund will be issued within 3-5 business days of receiving the item.</p>
                <button @click="step = 1" class="mt-4 text-sm font-semibold text-[var(--color-accent)] hover:underline">Start over</button>
            </div>

            <!-- Result: Used / no packaging -->
            <div x-show="step === 31" x-cloak class="rounded-[var(--radius-card)] border border-amber-200 dark:border-amber-900/40 bg-amber-50 dark:bg-amber-950/15 p-6">
                <h2 class="text-lg font-bold text-[var(--color-text)] mb-2">Partial return may be possible</h2>
                <p class="text-sm text-[var(--color-muted)]">Used items or items without original packaging may be eligible for a partial refund or store credit, depending on condition. <a href="/contact" class="text-[var(--color-accent)] hover:underline">Contact us</a> with photos and we'll assess on a case-by-case basis.</p>
                <button @click="step = 1" class="mt-4 text-sm font-semibold text-[var(--color-accent)] hover:underline">Start over</button>
            </div>
        </div>
    </div>
</section>
