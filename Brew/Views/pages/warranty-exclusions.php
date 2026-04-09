<section class="bg-[var(--color-bg)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-12 md:py-16">
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-[var(--color-text)]">Warranty Exclusions</h1>
        <p class="mt-3 text-[var(--color-muted)] max-w-2xl">Our 12-month warranty covers manufacturing defects. The following situations are not covered.</p>

        <div class="mt-10 grid gap-4 sm:grid-cols-2">
            <?php
            $exclusions = [
                ['Physical Damage', 'Drops, impacts, cracks, dents, or bent connectors caused by accidents or mishandling.', 'shield-off'],
                ['Water & Liquid Damage', 'Damage from submersion, spills, or moisture beyond the product\'s rated IP protection level.', 'droplet'],
                ['Unauthorized Modifications', 'Firmware hacks, hardware mods, third-party repairs, or opening sealed components voids the warranty.', 'tool'],
                ['Normal Wear & Tear', 'Battery degradation over time, cosmetic scuffs, faded keycaps, or worn ear cushions from regular use.', 'clock'],
                ['Misuse or Neglect', 'Using the product outside its intended purpose, ignoring safety instructions, or exposure to extreme conditions.', 'alert'],
                ['Third-Party Accessories', 'Damage caused by non-compatible chargers, cables, or accessories not sold by Scooter Dynamics.', 'plug'],
                ['Software Issues', 'Bugs, compatibility problems, or performance issues caused by third-party software or OS updates.', 'code'],
                ['Cosmetic Imperfections', 'Minor cosmetic variations that do not affect functionality (e.g., slight color differences, minor surface texture).', 'eye'],
            ];
            foreach ($exclusions as $ex): ?>
                <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-5">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-[var(--color-accent)]/10 text-[var(--color-accent)]">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </span>
                        <h2 class="text-base font-semibold text-[var(--color-text)]"><?= $ex[0] ?></h2>
                    </div>
                    <p class="text-sm text-[var(--color-muted)]"><?= $ex[1] ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-10 rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-6">
            <h2 class="text-lg font-bold text-[var(--color-text)] mb-3">What IS covered</h2>
            <div class="grid gap-3 sm:grid-cols-2 text-sm text-[var(--color-muted)]">
                <p class="flex items-start gap-2"><span class="text-emerald-500">&#10003;</span> Dead pixels beyond manufacturer tolerance (typically 3+)</p>
                <p class="flex items-start gap-2"><span class="text-emerald-500">&#10003;</span> Buttons or switches that stop working under normal use</p>
                <p class="flex items-start gap-2"><span class="text-emerald-500">&#10003;</span> Charging failures not caused by third-party chargers</p>
                <p class="flex items-start gap-2"><span class="text-emerald-500">&#10003;</span> Bluetooth/WiFi connectivity failures (hardware-level)</p>
                <p class="flex items-start gap-2"><span class="text-emerald-500">&#10003;</span> Speaker or microphone defects</p>
                <p class="flex items-start gap-2"><span class="text-emerald-500">&#10003;</span> Structural failures under normal use conditions</p>
            </div>
        </div>

        <div class="mt-8 flex flex-wrap gap-3">
            <a href="/warranty-claim" class="inline-flex h-10 items-center gap-2 rounded-[var(--radius-button)] bg-[var(--color-accent)] px-5 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">
                Start a Warranty Claim
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
            <a href="/returns-warranty" class="inline-flex h-10 items-center rounded-[var(--radius-button)] border border-[var(--color-border)] bg-[var(--color-surface)] px-5 text-sm font-semibold text-[var(--color-text)] transition hover:border-[var(--color-accent)] hover:text-[var(--color-accent)]">Returns & Warranty Overview</a>
        </div>
    </div>
</section>
