<section class="bg-[var(--color-bg)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-12 md:py-16">
        <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight text-[var(--color-text)]">Installation Guides</h1>
        <p class="mt-3 text-[var(--color-muted)] max-w-2xl">Step-by-step setup instructions for our most popular products. Get up and running quickly.</p>

        <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <?php
            $guides = [
                ['Wireless Headphones Setup', 'audio', 'Pairing via Bluetooth, app setup, noise cancellation modes, and multipoint connection.', ['Charge fully before first use', 'Hold power button 5s to enter pairing mode', 'Select device from your phone\'s Bluetooth settings', 'Download companion app for EQ and ANC settings']],
                ['Smartwatch Initial Setup', 'wearables', 'Phone pairing, watch face customization, health tracking, and notification setup.', ['Install the companion app on your phone', 'Turn on the watch and select your language', 'Scan the QR code shown on the watch', 'Follow the app prompts to complete setup']],
                ['Mechanical Keyboard Configuration', 'gaming', 'Key remapping, RGB lighting profiles, macro setup, and switch swapping guide.', ['Connect via USB-C cable or wireless dongle', 'Install configuration software from product page', 'Use FN + key combos for lighting presets', 'Hot-swap switches by pulling straight up with included tool']],
                ['USB-C Hub Connection', 'accessories', 'Connecting peripherals, display setup, power delivery, and troubleshooting.', ['Connect hub to your laptop\'s USB-C/Thunderbolt port', 'Plug in peripherals — devices are auto-detected', 'For displays: use HDMI port, set resolution in display settings', 'Ensure 100W PD pass-through for laptop charging']],
                ['Smart Home Devices', 'home', 'WiFi pairing, voice assistant integration, scheduling, and automation setup.', ['Plug in device and wait for indicator light', 'Open companion app and tap "Add Device"', 'Connect to your 2.4GHz WiFi network', 'Link with Alexa or Google Home for voice control']],
                ['Gaming Mouse Tuning', 'gaming', 'DPI configuration, button mapping, polling rate, and surface calibration.', ['Install driver software from product support page', 'Set DPI stages (400/800/1600/3200 recommended)', 'Assign macros or shortcuts to side buttons', 'Calibrate sensor to your mousepad surface']],
            ];
            foreach ($guides as $guide): ?>
                <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-5" x-data="{ expanded: false }">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="inline-flex h-5 items-center rounded bg-[var(--color-accent)]/10 px-2 text-[0.7rem] font-bold uppercase tracking-wide text-[var(--color-accent)]"><?= $guide[1] ?></span>
                    </div>
                    <h2 class="text-base font-semibold text-[var(--color-text)]"><?= $guide[0] ?></h2>
                    <p class="mt-1 text-sm text-[var(--color-muted)]"><?= $guide[2] ?></p>
                    <div x-show="expanded" x-cloak x-collapse class="mt-3">
                        <ol class="space-y-2 text-sm text-[var(--color-muted)] list-decimal list-inside">
                            <?php foreach ($guide[3] as $step): ?>
                                <li><?= $step ?></li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                    <button @click="expanded = !expanded" class="mt-3 text-sm font-semibold text-[var(--color-accent)] hover:underline" x-text="expanded ? 'Hide steps' : 'View steps'"></button>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-10 rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] p-6">
            <h2 class="text-lg font-bold text-[var(--color-text)] mb-2">Need hands-on help?</h2>
            <p class="text-sm text-[var(--color-muted)]">Our support team can walk you through any setup over chat or phone. <a href="/contact" class="text-[var(--color-accent)] hover:underline">Get in touch</a>.</p>
        </div>
    </div>
</section>
