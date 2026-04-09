<!-- Brand Marquee Strip -->
<section class="bg-[var(--color-surface)] border-y border-[var(--color-border)] overflow-hidden">
    <div class="py-8">
        <p class="text-center text-xs font-medium text-[var(--color-muted)] uppercase tracking-widest mb-5">Trusted by teams and brands worldwide</p>
        <div class="relative">
            <div class="flex animate-marquee gap-12 whitespace-nowrap">
                <?php
                $brands = ['TechNova', 'SoundWave', 'PixelForge', 'CloudNine', 'VoltEdge', 'DataPulse', 'NeonByte', 'AeroSync', 'CoreFusion', 'BlinkIO', 'TechNova', 'SoundWave', 'PixelForge', 'CloudNine', 'VoltEdge', 'DataPulse', 'NeonByte', 'AeroSync', 'CoreFusion', 'BlinkIO'];
                foreach ($brands as $brand): ?>
                    <span class="inline-flex items-center gap-2 text-[var(--color-muted)]/60 text-lg font-bold tracking-tight select-none">
                        <span class="w-8 h-8 rounded-lg bg-[var(--color-border)] flex items-center justify-center text-xs font-extrabold text-[var(--color-muted)]"><?= $brand[0] ?></span>
                        <?= $brand ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <style>
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        .animate-marquee { animation: marquee 30s linear infinite; }
        .animate-marquee:hover { animation-play-state: paused; }
    </style>
</section>
