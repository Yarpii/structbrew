<section class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[40rem] py-7 md:py-10" x-data="{ loading: false }">
    <?php if (!empty($turnstileEnabled) && !empty($turnstileSiteKey)): ?>
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <?php endif; ?>

    <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-5 md:p-6" style="box-shadow: var(--shadow-sm)">
        <h1 class="text-2xl font-bold text-[var(--color-text)]">Two-factor verification</h1>
        <p class="mt-2 text-sm text-[var(--color-muted)]">
            Enter the 6-digit code from your authenticator app for
            <span class="font-semibold text-[var(--color-text)]"><?= htmlspecialchars((string) ($maskedEmail ?? 'your account')) ?></span>.
        </p>

        <form method="POST" action="/login/2fa" class="mt-5 space-y-4" @submit="loading = true">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">

            <div>
                <label class="mb-1.5 block text-sm font-semibold text-[var(--color-text)]" for="otp-code">Verification code</label>
                <input id="otp-code" name="code" type="text" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" minlength="6" required
                       placeholder="123456"
                       class="h-11 w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 text-[var(--color-text)] tracking-[0.25em]">
            </div>

            <?php if (!empty($turnstileEnabled) && !empty($turnstileSiteKey)): ?>
                <div class="pt-1">
                    <div class="cf-turnstile" data-sitekey="<?= htmlspecialchars((string) $turnstileSiteKey) ?>"></div>
                </div>
            <?php endif; ?>

            <button type="submit" class="inline-flex h-11 w-full items-center justify-center rounded-md bg-[var(--color-accent)] px-4 text-sm font-semibold text-white" :disabled="loading">
                <span x-show="!loading">Verify and continue</span>
                <span x-show="loading" x-cloak>Verifying...</span>
            </button>
        </form>
    </div>
</section>
