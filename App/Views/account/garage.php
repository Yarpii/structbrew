<?php
$activeAccountTab   = 'garage';
$garageLimit        = 10;
$garageVehicles     = $garageVehicles ?? [];
$savedCount         = count($garageVehicles);
$vehicleOptionsJson = json_encode($garageVehicleOptions ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
?>
<section class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-7 md:py-10">
<div class="space-y-5">

    <?php include __DIR__ . '/_nav.php'; ?>

    <?php if (!empty($flashError)): ?>
        <div class="border-l-4 border-rose-500 bg-rose-500/8 px-4 py-3 text-sm text-rose-700 dark:text-rose-300 flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <?= htmlspecialchars((string) $flashError) ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($flashSuccess)): ?>
        <div class="border-l-4 border-emerald-500 bg-emerald-500/8 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-300 flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
            <?= htmlspecialchars((string) $flashSuccess) ?>
        </div>
    <?php endif; ?>


    <!-- ── HEADER ──────────────────────────────────────────────────────────── -->
    <div class="relative border border-[var(--color-border)] bg-[var(--color-surface)] overflow-hidden">
        <div class="absolute left-0 inset-y-0 w-1 bg-[var(--color-accent)]"></div>
        <div class="pl-6 pr-5 py-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs font-mono font-semibold tracking-[0.18em] text-[var(--color-accent)] uppercase mb-1">My Garage</p>
                <h1 class="text-2xl font-bold text-[var(--color-text)] leading-tight">Saved Vehicles</h1>
                <p class="mt-1 text-sm text-[var(--color-muted)]">Track mileage, service intervals, notes and compatible parts for each of your rides.</p>
            </div>
            <div class="flex items-center gap-4 flex-shrink-0">
                <div class="text-right">
                    <div class="flex items-center gap-1 justify-end mb-1.5">
                        <?php for ($i = 1; $i <= $garageLimit; $i++): ?>
                            <div class="h-1.5 w-4 transition-colors <?= $i <= $savedCount ? 'bg-[var(--color-accent)]' : 'bg-[var(--color-border)]' ?>"></div>
                        <?php endfor; ?>
                    </div>
                    <p class="text-xs font-mono text-[var(--color-muted)]"><?= $savedCount ?> / <?= $garageLimit ?> slots used</p>
                </div>
            </div>
        </div>
    </div>


    <!-- ── ADD VEHICLE FORM ─────────────────────────────────────────────────── -->
    <script type="application/json" id="garage-vehicle-options"><?= $vehicleOptionsJson ?></script>

    <div class="border border-[var(--color-border)] bg-[var(--color-surface)]"
         x-data="{
             open: <?= $savedCount === 0 ? 'true' : 'false' ?>,
             vehicles: JSON.parse(document.getElementById('garage-vehicle-options').textContent || '[]'),
             brandId: '',
             vehicleId: '',
             get brands() {
                 const m = {};
                 this.vehicles.forEach(v => { if (!m[v.brand_id]) m[v.brand_id] = { id: v.brand_id, name: v.brand }; });
                 return Object.values(m).sort((a, b) => a.name.localeCompare(b.name));
             },
             get filteredModels() {
                 return this.vehicles.filter(v => !this.brandId || String(v.brand_id) === String(this.brandId));
             }
         }"
         @open-add.window="open = true; $nextTick(() => $el.scrollIntoView({ behavior: 'smooth' }))">

        <button @click="open = !open"
                class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-[var(--color-bg)] transition-colors">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 flex items-center justify-center bg-[var(--color-accent)]/10 border border-[var(--color-accent)]/20 flex-shrink-0">
                    <svg class="w-4 h-4 text-[var(--color-accent)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-[var(--color-text)]">Add a Vehicle</p>
                    <p class="text-xs text-[var(--color-muted)]">Type → Manufacturer → Model</p>
                </div>
            </div>
            <svg class="w-4 h-4 text-[var(--color-muted)] transition-transform duration-200 flex-shrink-0" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
        </button>

        <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-cloak class="border-t border-[var(--color-border)] px-5 py-5">
            <form method="POST" action="/account/garage" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">
                <div>
                    <label class="mb-1.5 block text-xs font-mono font-semibold uppercase tracking-wider text-[var(--color-muted)]">Type</label>
                    <select name="vehicle_type" class="h-10 w-full border border-[var(--color-border)] bg-[var(--color-bg)] px-3 text-sm text-[var(--color-text)] focus:border-[var(--color-accent)] transition-colors">
                        <option value="scooter">Scooter</option>
                        <option value="moped">Moped</option>
                        <option value="motorcycle">Motorcycle</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-mono font-semibold uppercase tracking-wider text-[var(--color-muted)]">Manufacturer</label>
                    <select x-model="brandId" class="h-10 w-full border border-[var(--color-border)] bg-[var(--color-bg)] px-3 text-sm text-[var(--color-text)] focus:border-[var(--color-accent)] transition-colors">
                        <option value="">Choose manufacturer</option>
                        <template x-for="b in brands" :key="b.id"><option :value="b.id" x-text="b.name"></option></template>
                    </select>
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-mono font-semibold uppercase tracking-wider text-[var(--color-muted)]">Model</label>
                    <select name="vehicle_id" x-model="vehicleId" required class="h-10 w-full border border-[var(--color-border)] bg-[var(--color-bg)] px-3 text-sm text-[var(--color-text)] focus:border-[var(--color-accent)] transition-colors">
                        <option value="">Choose model</option>
                        <template x-for="m in filteredModels" :key="m.id"><option :value="m.id" x-text="m.label"></option></template>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="h-10 w-full bg-[var(--color-accent)] hover:bg-[var(--color-accent-hover)] text-white text-sm font-semibold transition-colors">Add to Garage</button>
                </div>
            </form>
            <?php if ($savedCount >= $garageLimit): ?>
                <p class="mt-3 text-xs text-[var(--color-muted)] flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 flex-shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                    Garage full (<?= $garageLimit ?>/<?= $garageLimit ?> slots). Remove a vehicle to add a new one.
                </p>
            <?php endif; ?>
        </div>
    </div>


    <!-- ── VEHICLE CARDS ────────────────────────────────────────────────────── -->
    <?php if (!empty($garageVehicles)): ?>

        <div class="space-y-4">
        <?php foreach ($garageVehicles as $gv):
            $id          = (int) ($gv['id'] ?? 0);
            $vehicleId   = (int) ($gv['vehicle_id'] ?? 0);
            $typeLabel   = strtoupper((string) ($gv['vehicle_type'] ?? 'SCOOTER'));
            $isDefault   = !empty($gv['is_default']);
            $brand       = (string) ($gv['brand'] ?? '');
            $model       = (string) ($gv['model'] ?? $gv['label'] ?? '');
            $photoPath   = (string) ($gv['photo_path'] ?? '');
            $specYear    = $gv['spec_year'];
            $specColour  = (string) ($gv['spec_colour'] ?? '');
            $specCc      = $gv['spec_engine_cc'];
            $specMods    = (string) ($gv['spec_mods_summary'] ?? '');
            $mileage     = $gv['mileage_km'];
            $interval    = $gv['service_interval_km'];
            $lastService = $gv['last_service_km'];
            $notes       = (string) ($gv['notes'] ?? '');
            $progress    = $gv['service_progress'];
            $kmSince     = $gv['km_since_service'];
            $kmUntil     = $gv['km_until_service'];
            $partsCount  = (int) ($gv['parts_count'] ?? 0);
            $history     = $gv['purchase_history'] ?? [];
            $totalSpent  = $gv['total_spent'];
            $mods        = $gv['mods'] ?? [];
            $modsDone    = count(array_filter($mods, fn($m) => (int)($m['is_done'] ?? 0) === 1));
            $modsTotal   = count($mods);
            $serviceOverdue = $progress !== null && $progress >= 100;
            $serviceWarning = $progress !== null && $progress >= 80 && !$serviceOverdue;
            $hasSpec     = $specYear !== null || $specColour !== '' || $specCc !== null || $specMods !== '';
            $xId = 'gv' . $id;
        ?>
        <div x-data="{ tab: 'overview', editOpen: false }"
             class="border <?= $isDefault ? 'border-[var(--color-accent)]/40' : 'border-[var(--color-border)]' ?> bg-[var(--color-surface)]">

            <!-- Card top stripe -->
            <div class="h-[3px] w-full <?= $isDefault ? 'bg-[var(--color-accent)]' : 'bg-[var(--color-border)] group-hover:bg-[var(--color-accent)]' ?> flex-shrink-0"></div>

            <!-- Card header row -->
            <div class="flex items-center gap-4 px-5 py-4 border-b border-[var(--color-border)]">
                <!-- Photo / upload -->
                <div class="hidden sm:block flex-shrink-0">
                    <form method="POST" action="/account/garage/<?= $id ?>/photo" enctype="multipart/form-data" class="relative group w-14 h-14">
                        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">
                        <?php if ($photoPath !== ''): ?>
                            <img src="/<?= htmlspecialchars($photoPath) ?>" alt="<?= htmlspecialchars($model) ?>"
                                 class="w-14 h-14 object-cover border border-[var(--color-border)]">
                        <?php else: ?>
                            <div class="w-14 h-14 flex items-center justify-center bg-[var(--color-bg)] border border-[var(--color-border)]">
                                <svg viewBox="0 0 96 44" fill="none" class="w-9 h-auto text-[var(--color-muted)]">
                                    <circle cx="16" cy="33" r="9" stroke="currentColor" stroke-width="2"/>
                                    <circle cx="80" cy="33" r="9" stroke="currentColor" stroke-width="2"/>
                                    <path d="M25 31 L52 31 L52 35 L25 35 Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    <path d="M52 33 L55 17 Q62 11 70 11 L77 11 L80 22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M40 20 Q44 15 52 14 L58 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M25 31 Q30 19 40 17 L44 17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M77 11 L80 6 M75 6 L84 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>
                        <?php endif; ?>
                        <!-- Upload overlay -->
                        <label class="absolute inset-0 flex flex-col items-center justify-center bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                            <svg class="w-4 h-4 text-white mb-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                            <span class="text-[9px] font-bold text-white uppercase tracking-wide leading-tight">Photo</span>
                            <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" class="sr-only" onchange="this.form.submit()">
                        </label>
                    </form>
                </div>

                <!-- Title / meta -->
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-0.5">
                        <span class="text-[10px] font-mono font-bold tracking-widest px-1.5 py-0.5 border <?= $isDefault ? 'border-[var(--color-accent)]/30 text-[var(--color-accent)] bg-[var(--color-accent)]/8' : 'border-[var(--color-border)] text-[var(--color-muted)]' ?>">
                            <?= htmlspecialchars($typeLabel) ?>
                        </span>
                        <?php if ($isDefault): ?>
                            <span class="flex items-center gap-1 text-[10px] font-semibold text-[var(--color-accent)]">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.86L12 17.77l-6.18 3.23L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                Default
                            </span>
                        <?php endif; ?>
                        <?php if ($serviceOverdue): ?>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 bg-rose-500/10 border border-rose-500/30 text-rose-500">SERVICE DUE</span>
                        <?php elseif ($serviceWarning): ?>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 bg-amber-500/10 border border-amber-500/30 text-amber-500">SERVICE SOON</span>
                        <?php endif; ?>
                    </div>
                    <p class="text-xs font-mono text-[var(--color-muted)] uppercase tracking-wider"><?= htmlspecialchars($brand) ?></p>
                    <p class="text-lg font-bold text-[var(--color-text)] leading-tight truncate"><?= htmlspecialchars($model) ?></p>
                </div>

                <!-- Quick stats -->
                <div class="hidden md:flex items-center gap-5 flex-shrink-0 text-right">
                    <?php if ($totalSpent !== null && $totalSpent > 0): ?>
                        <div class="border border-[var(--color-accent)]/30 bg-[var(--color-accent)]/5 px-3 py-1.5">
                            <p class="text-[10px] font-mono uppercase tracking-wider text-[var(--color-accent)] mb-0.5">Total Spent</p>
                            <p class="text-base font-bold text-[var(--color-accent)]">£<?= number_format($totalSpent, 2) ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if ($mileage !== null): ?>
                        <div>
                            <p class="text-xs text-[var(--color-muted)] font-mono uppercase tracking-wider">Mileage</p>
                            <p class="text-base font-bold text-[var(--color-text)]"><?= number_format($mileage) ?> <span class="text-xs font-normal text-[var(--color-muted)]">km</span></p>
                        </div>
                    <?php endif; ?>
                    <?php if ($partsCount > 0): ?>
                        <div>
                            <p class="text-xs text-[var(--color-muted)] font-mono uppercase tracking-wider">Parts</p>
                            <p class="text-base font-bold text-[var(--color-accent)]"><?= number_format($partsCount) ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Action buttons -->
                <div class="flex items-center gap-1.5 flex-shrink-0">
                    <a href="/shop?vehicle_id=<?= $vehicleId ?>"
                       class="inline-flex h-9 items-center gap-1.5 bg-[var(--color-accent)] hover:bg-[var(--color-accent-hover)] px-3 text-xs font-bold text-white transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <span class="hidden sm:inline">Find Parts</span>
                    </a>
                    <?php if (!$isDefault): ?>
                        <form method="POST" action="/account/garage/<?= $id ?>/select">
                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">
                            <button type="submit" title="Set as default" class="h-9 w-9 flex items-center justify-center border border-[var(--color-border)] hover:border-[var(--color-accent)]/40 hover:bg-[var(--color-accent)]/8 text-[var(--color-muted)] hover:text-[var(--color-accent)] transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.86L12 17.77l-6.18 3.23L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            </button>
                        </form>
                    <?php endif; ?>
                    <form method="POST" action="/account/garage/<?= $id ?>/delete" onsubmit="return confirm('Remove <?= htmlspecialchars(addslashes($model)) ?> from your garage?')">
                        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">
                        <button type="submit" title="Remove" class="h-9 w-9 flex items-center justify-center border border-[var(--color-border)] hover:border-rose-400/40 hover:bg-rose-500/8 text-[var(--color-muted)] hover:text-rose-500 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tab nav -->
            <div class="flex border-b border-[var(--color-border)] bg-[var(--color-bg)] text-xs font-semibold overflow-x-auto">
                <button @click="tab = 'overview'" :class="tab === 'overview' ? 'border-b-2 border-[var(--color-accent)] text-[var(--color-accent)]' : 'text-[var(--color-muted)] hover:text-[var(--color-text)]'"
                        class="px-4 py-2.5 transition-colors -mb-px whitespace-nowrap">Overview</button>
                <button @click="tab = 'spec'" :class="tab === 'spec' ? 'border-b-2 border-[var(--color-accent)] text-[var(--color-accent)]' : 'text-[var(--color-muted)] hover:text-[var(--color-text)]'"
                        class="px-4 py-2.5 transition-colors -mb-px whitespace-nowrap flex items-center gap-1.5">
                    Spec
                    <?php if ($hasSpec): ?><span class="inline-block w-1.5 h-1.5 rounded-full bg-[var(--color-accent)]"></span><?php endif; ?>
                </button>
                <button @click="tab = 'mods'" :class="tab === 'mods' ? 'border-b-2 border-[var(--color-accent)] text-[var(--color-accent)]' : 'text-[var(--color-muted)] hover:text-[var(--color-text)]'"
                        class="px-4 py-2.5 transition-colors -mb-px whitespace-nowrap flex items-center gap-1.5">
                    Mods
                    <?php if ($modsTotal > 0): ?>
                        <span class="text-[10px] font-bold <?= $modsDone === $modsTotal ? 'text-emerald-500' : 'text-[var(--color-accent)]' ?>"><?= $modsDone ?>/<?= $modsTotal ?></span>
                    <?php endif; ?>
                </button>
                <button @click="tab = 'service'" :class="tab === 'service' ? 'border-b-2 border-[var(--color-accent)] text-[var(--color-accent)]' : 'text-[var(--color-muted)] hover:text-[var(--color-text)]'"
                        class="px-4 py-2.5 transition-colors -mb-px whitespace-nowrap flex items-center gap-1.5">
                    Service
                    <?php if ($serviceOverdue): ?>
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                    <?php elseif ($serviceWarning): ?>
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                    <?php endif; ?>
                </button>
                <button @click="tab = 'notes'" :class="tab === 'notes' ? 'border-b-2 border-[var(--color-accent)] text-[var(--color-accent)]' : 'text-[var(--color-muted)] hover:text-[var(--color-text)]'"
                        class="px-4 py-2.5 transition-colors -mb-px whitespace-nowrap flex items-center gap-1.5">
                    Notes
                    <?php if ($notes !== ''): ?><span class="inline-block w-1.5 h-1.5 rounded-full bg-[var(--color-accent)]"></span><?php endif; ?>
                </button>
                <button @click="tab = 'history'" :class="tab === 'history' ? 'border-b-2 border-[var(--color-accent)] text-[var(--color-accent)]' : 'text-[var(--color-muted)] hover:text-[var(--color-text)]'"
                        class="px-4 py-2.5 transition-colors -mb-px whitespace-nowrap flex items-center gap-1.5">
                    Parts History
                    <?php if (!empty($history)): ?><span class="text-[10px] font-bold text-[var(--color-accent)]"><?= count($history) ?></span><?php endif; ?>
                </button>
            </div>

            <!-- ── TAB: OVERVIEW ── -->
            <div x-show="tab === 'overview'" class="px-5 py-5">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <!-- Mileage -->
                    <div class="border border-[var(--color-border)] bg-[var(--color-bg)] px-4 py-3">
                        <p class="text-[10px] font-mono uppercase tracking-wider text-[var(--color-muted)] mb-1">Current Mileage</p>
                        <?php if ($mileage !== null): ?>
                            <p class="text-xl font-bold text-[var(--color-text)]"><?= number_format($mileage) ?> <span class="text-sm font-normal text-[var(--color-muted)]">km</span></p>
                        <?php else: ?>
                            <p class="text-sm text-[var(--color-muted)] italic">Not set</p>
                        <?php endif; ?>
                    </div>
                    <!-- Service interval -->
                    <div class="border border-[var(--color-border)] bg-[var(--color-bg)] px-4 py-3">
                        <p class="text-[10px] font-mono uppercase tracking-wider text-[var(--color-muted)] mb-1">Service Every</p>
                        <?php if ($interval !== null): ?>
                            <p class="text-xl font-bold text-[var(--color-text)]"><?= number_format($interval) ?> <span class="text-sm font-normal text-[var(--color-muted)]">km</span></p>
                        <?php else: ?>
                            <p class="text-sm text-[var(--color-muted)] italic">Not set</p>
                        <?php endif; ?>
                    </div>
                    <!-- Next service -->
                    <div class="border <?= $serviceOverdue ? 'border-rose-500/30 bg-rose-500/5' : ($serviceWarning ? 'border-amber-500/30 bg-amber-500/5' : 'border-[var(--color-border)] bg-[var(--color-bg)]') ?> px-4 py-3">
                        <p class="text-[10px] font-mono uppercase tracking-wider text-[var(--color-muted)] mb-1">Until Next Service</p>
                        <?php if ($kmUntil !== null): ?>
                            <p class="text-xl font-bold <?= $serviceOverdue ? 'text-rose-500' : ($serviceWarning ? 'text-amber-500' : 'text-[var(--color-text)]') ?>">
                                <?= $serviceOverdue ? 'Overdue' : (number_format($kmUntil) . ' <span class="text-sm font-normal text-[var(--color-muted)]">km</span>') ?>
                            </p>
                        <?php else: ?>
                            <p class="text-sm text-[var(--color-muted)] italic">—</p>
                        <?php endif; ?>
                    </div>
                    <!-- Compatible parts -->
                    <div class="border border-[var(--color-border)] bg-[var(--color-bg)] px-4 py-3">
                        <p class="text-[10px] font-mono uppercase tracking-wider text-[var(--color-muted)] mb-1">Compatible Parts</p>
                        <p class="text-xl font-bold <?= $partsCount > 0 ? 'text-[var(--color-accent)]' : 'text-[var(--color-muted)]' ?>"><?= number_format($partsCount) ?></p>
                    </div>
                </div>

                <?php if ($notes !== ''): ?>
                    <div class="mt-4 border-l-2 border-[var(--color-accent)]/40 bg-[var(--color-bg)] px-4 py-3">
                        <p class="text-xs font-mono uppercase tracking-wider text-[var(--color-muted)] mb-1">Note</p>
                        <p class="text-sm text-[var(--color-text)]"><?= nl2br(htmlspecialchars($notes)) ?></p>
                    </div>
                <?php endif; ?>

                <div class="mt-4 flex flex-wrap gap-2">
                    <button @click="tab = 'spec'; editOpen = true"
                            class="inline-flex h-8 items-center gap-1.5 border border-[var(--color-border)] px-3 text-xs font-semibold text-[var(--color-muted)] hover:border-[var(--color-text)] hover:text-[var(--color-text)] transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="1"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                        <?= $hasSpec ? 'Edit spec' : 'Add build spec' ?>
                    </button>
                    <button @click="tab = 'mods'"
                            class="inline-flex h-8 items-center gap-1.5 border border-[var(--color-border)] px-3 text-xs font-semibold text-[var(--color-muted)] hover:border-[var(--color-text)] hover:text-[var(--color-text)] transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                        Mod list <?php if ($modsTotal > 0): ?>(<?= $modsDone ?>/<?= $modsTotal ?>)<?php endif; ?>
                    </button>
                    <button @click="tab = 'service'; editOpen = true"
                            class="inline-flex h-8 items-center gap-1.5 border border-[var(--color-border)] px-3 text-xs font-semibold text-[var(--color-muted)] hover:border-[var(--color-text)] hover:text-[var(--color-text)] transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Update mileage &amp; service
                    </button>
                    <button @click="tab = 'notes'; editOpen = true"
                            class="inline-flex h-8 items-center gap-1.5 border border-[var(--color-border)] px-3 text-xs font-semibold text-[var(--color-muted)] hover:border-[var(--color-text)] hover:text-[var(--color-text)] transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        <?= $notes !== '' ? 'Edit notes' : 'Add notes' ?>
                    </button>
                </div>
            </div>

            <!-- ── TAB: SPEC ── -->
            <div x-show="tab === 'spec'" x-cloak class="px-5 py-5 space-y-5">

                <!-- Spec sheet display -->
                <?php if ($hasSpec): ?>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="border border-[var(--color-border)] bg-[var(--color-bg)] px-4 py-3">
                            <p class="text-[10px] font-mono uppercase tracking-wider text-[var(--color-muted)] mb-1">Year</p>
                            <p class="text-xl font-bold text-[var(--color-text)]"><?= $specYear !== null ? $specYear : '<span class="text-sm font-normal text-[var(--color-muted)] italic">Not set</span>' ?></p>
                        </div>
                        <div class="border border-[var(--color-border)] bg-[var(--color-bg)] px-4 py-3">
                            <p class="text-[10px] font-mono uppercase tracking-wider text-[var(--color-muted)] mb-1">Colour</p>
                            <div class="flex items-center gap-2 mt-1">
                                <?php if ($specColour !== ''): ?>
                                    <span class="w-4 h-4 border border-[var(--color-border)] flex-shrink-0" style="background: <?= htmlspecialchars($specColour) ?>"></span>
                                    <p class="text-sm font-bold text-[var(--color-text)] capitalize"><?= htmlspecialchars($specColour) ?></p>
                                <?php else: ?>
                                    <p class="text-sm text-[var(--color-muted)] italic">Not set</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="border border-[var(--color-border)] bg-[var(--color-bg)] px-4 py-3">
                            <p class="text-[10px] font-mono uppercase tracking-wider text-[var(--color-muted)] mb-1">Engine</p>
                            <?php if ($specCc !== null): ?>
                                <p class="text-xl font-bold text-[var(--color-text)]"><?= number_format($specCc) ?> <span class="text-sm font-normal text-[var(--color-muted)]">cc</span></p>
                            <?php else: ?>
                                <p class="text-sm text-[var(--color-muted)] italic">Not set</p>
                            <?php endif; ?>
                        </div>
                        <div class="border border-[var(--color-border)] bg-[var(--color-bg)] px-4 py-3">
                            <p class="text-[10px] font-mono uppercase tracking-wider text-[var(--color-muted)] mb-1">Mods Summary</p>
                            <?php if ($specMods !== ''): ?>
                                <p class="text-sm text-[var(--color-text)] line-clamp-2"><?= htmlspecialchars($specMods) ?></p>
                            <?php else: ?>
                                <p class="text-sm text-[var(--color-muted)] italic">None noted</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="border border-dashed border-[var(--color-border)] px-4 py-4 text-sm text-[var(--color-muted)]">
                        Fill in the details below to create your digital spec sheet — year, colour, engine size, and any notable modifications.
                    </div>
                <?php endif; ?>

                <!-- Edit form -->
                <div>
                    <button @click="editOpen = !editOpen"
                            class="flex items-center gap-2 text-xs font-semibold text-[var(--color-accent)] hover:underline mb-3">
                        <span x-text="editOpen ? '▲ Hide form' : '▼ <?= $hasSpec ? 'Edit spec sheet' : 'Add spec sheet' ?>'"></span>
                    </button>
                    <div x-show="editOpen" x-cloak>
                        <form method="POST" action="/account/garage/<?= $id ?>/spec" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">
                            <div>
                                <label class="mb-1.5 block text-xs font-mono font-semibold uppercase tracking-wider text-[var(--color-muted)]">Year</label>
                                <input type="number" name="spec_year" min="1900" max="<?= date('Y') + 1 ?>"
                                       value="<?= $specYear !== null ? $specYear : '' ?>"
                                       placeholder="e.g. 2021"
                                       class="h-10 w-full border border-[var(--color-border)] bg-[var(--color-bg)] px-3 text-sm text-[var(--color-text)] focus:border-[var(--color-accent)] transition-colors">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-mono font-semibold uppercase tracking-wider text-[var(--color-muted)]">Colour</label>
                                <input type="text" name="spec_colour" maxlength="100"
                                       value="<?= htmlspecialchars($specColour) ?>"
                                       placeholder="e.g. Matte Black"
                                       class="h-10 w-full border border-[var(--color-border)] bg-[var(--color-bg)] px-3 text-sm text-[var(--color-text)] focus:border-[var(--color-accent)] transition-colors">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-mono font-semibold uppercase tracking-wider text-[var(--color-muted)]">Engine Size (cc)</label>
                                <input type="number" name="spec_engine_cc" min="1" max="9999"
                                       value="<?= $specCc !== null ? $specCc : '' ?>"
                                       placeholder="e.g. 125"
                                       class="h-10 w-full border border-[var(--color-border)] bg-[var(--color-bg)] px-3 text-sm text-[var(--color-text)] focus:border-[var(--color-accent)] transition-colors">
                            </div>
                            <div class="sm:col-span-2 lg:col-span-4">
                                <label class="mb-1.5 block text-xs font-mono font-semibold uppercase tracking-wider text-[var(--color-muted)]">Notable Modifications</label>
                                <textarea name="spec_mods_summary" rows="2" maxlength="500"
                                          placeholder="e.g. De-restricted, Malossi exhaust, LED headlight conversion..."
                                          class="w-full border border-[var(--color-border)] bg-[var(--color-bg)] px-3 py-2.5 text-sm text-[var(--color-text)] focus:border-[var(--color-accent)] transition-colors resize-none"><?= htmlspecialchars($specMods) ?></textarea>
                            </div>
                            <div class="sm:col-span-2 lg:col-span-4 flex justify-end">
                                <button type="submit" class="h-10 px-6 bg-[var(--color-accent)] hover:bg-[var(--color-accent-hover)] text-white text-sm font-semibold transition-colors">Save Spec</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ── TAB: MODS ── -->
            <div x-show="tab === 'mods'" x-cloak class="px-5 py-5 space-y-4">

                <?php if (!empty($mods)): ?>
                    <div class="divide-y divide-[var(--color-border)] border border-[var(--color-border)]">
                        <?php foreach ($mods as $mod):
                            $modId   = (int) ($mod['id'] ?? 0);
                            $isDone  = (int) ($mod['is_done'] ?? 0) === 1;
                            $modSearch = (string) ($mod['product_search'] ?? '');
                        ?>
                            <div class="flex items-center gap-3 px-4 py-3 <?= $isDone ? 'bg-emerald-500/4' : 'hover:bg-[var(--color-bg)]' ?> transition-colors group">
                                <!-- Toggle checkbox -->
                                <form method="POST" action="/account/garage/<?= $id ?>/mods/<?= $modId ?>/toggle" class="flex-shrink-0">
                                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">
                                    <button type="submit" title="<?= $isDone ? 'Mark as planned' : 'Mark as done' ?>"
                                            class="w-5 h-5 border-2 flex items-center justify-center transition-colors flex-shrink-0 <?= $isDone ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-[var(--color-border)] hover:border-[var(--color-accent)]' ?>">
                                        <?php if ($isDone): ?>
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                        <?php endif; ?>
                                    </button>
                                </form>

                                <!-- Title + shop link -->
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-[var(--color-text)] <?= $isDone ? 'line-through text-[var(--color-muted)]' : '' ?> truncate">
                                        <?= htmlspecialchars((string) ($mod['title'] ?? '')) ?>
                                    </p>
                                    <?php if ($modSearch !== '' && !$isDone): ?>
                                        <a href="/shop?q=<?= urlencode($modSearch) ?>&vehicle_id=<?= $vehicleId ?>"
                                           class="text-xs text-[var(--color-accent)] hover:underline flex items-center gap-1 mt-0.5">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                            Find in shop
                                        </a>
                                    <?php elseif ($isDone): ?>
                                        <p class="text-[10px] text-emerald-500 font-semibold uppercase tracking-wider mt-0.5">Done ✓</p>
                                    <?php endif; ?>
                                </div>

                                <!-- Delete -->
                                <form method="POST" action="/account/garage/<?= $id ?>/mods/<?= $modId ?>/delete" class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">
                                    <button type="submit" title="Remove" class="w-7 h-7 flex items-center justify-center text-[var(--color-muted)] hover:text-rose-500 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($modsTotal > 0): ?>
                        <div class="flex items-center gap-3">
                            <div class="flex-1 h-1.5 bg-[var(--color-border)]">
                                <div class="h-1.5 bg-emerald-500 transition-all" style="width: <?= $modsTotal > 0 ? round(($modsDone / $modsTotal) * 100) : 0 ?>%"></div>
                            </div>
                            <span class="text-xs font-mono text-[var(--color-muted)] flex-shrink-0"><?= $modsDone ?>/<?= $modsTotal ?> complete</span>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="border border-dashed border-[var(--color-border)] px-5 py-6 text-center">
                        <p class="text-sm font-semibold text-[var(--color-text)]">No mods planned yet</p>
                        <p class="mt-1 text-xs text-[var(--color-muted)]">Build your wish list below — tick them off as you complete each mod.</p>
                    </div>
                <?php endif; ?>

                <!-- Add mod form -->
                <div class="border-t border-[var(--color-border)] pt-4">
                    <p class="text-xs font-mono font-semibold uppercase tracking-wider text-[var(--color-muted)] mb-3">Add a Mod</p>
                    <form method="POST" action="/account/garage/<?= $id ?>/mods" class="grid gap-3 sm:grid-cols-3">
                        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-xs font-mono font-semibold uppercase tracking-wider text-[var(--color-muted)]">Mod Title</label>
                            <input type="text" name="title" maxlength="255" required
                                   placeholder="e.g. Malossi MHR exhaust, uprated fork springs..."
                                   class="h-10 w-full border border-[var(--color-border)] bg-[var(--color-bg)] px-3 text-sm text-[var(--color-text)] focus:border-[var(--color-accent)] transition-colors">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-mono font-semibold uppercase tracking-wider text-[var(--color-muted)]">Shop Search Term <span class="font-normal normal-case">(optional)</span></label>
                            <input type="text" name="product_search" maxlength="255"
                                   placeholder="e.g. Malossi exhaust 125"
                                   class="h-10 w-full border border-[var(--color-border)] bg-[var(--color-bg)] px-3 text-sm text-[var(--color-text)] focus:border-[var(--color-accent)] transition-colors">
                        </div>
                        <div class="sm:col-span-3 flex justify-end">
                            <button type="submit" class="h-10 px-6 bg-[var(--color-accent)] hover:bg-[var(--color-accent-hover)] text-white text-sm font-semibold transition-colors">Add to List</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ── TAB: SERVICE ──
            <div x-show="tab === 'service'" x-cloak class="px-5 py-5 space-y-5">

                <!-- Service progress bar -->
                <?php if ($progress !== null): ?>
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs font-mono font-semibold uppercase tracking-wider text-[var(--color-muted)]">Service Progress</p>
                            <span class="text-xs font-bold <?= $serviceOverdue ? 'text-rose-500' : ($serviceWarning ? 'text-amber-500' : 'text-[var(--color-text)]') ?>"><?= $progress ?>%</span>
                        </div>
                        <div class="h-2 w-full bg-[var(--color-border)]">
                            <div class="h-2 transition-all <?= $serviceOverdue ? 'bg-rose-500' : ($serviceWarning ? 'bg-amber-500' : 'bg-[var(--color-accent)]') ?>" style="width: <?= min(100, $progress) ?>%"></div>
                        </div>
                        <div class="flex justify-between mt-1.5 text-[11px] text-[var(--color-muted)]">
                            <span>Last service: <?= $lastService !== null ? number_format($lastService) . ' km' : 'not set' ?></span>
                            <span><?= $serviceOverdue ? 'OVERDUE by ' . number_format(abs($kmUntil ?? 0)) . ' km' : number_format($kmUntil ?? 0) . ' km remaining' ?></span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="border border-dashed border-[var(--color-border)] px-4 py-4 text-sm text-[var(--color-muted)]">
                        Set your current mileage and service interval below to track when your next service is due.
                    </div>
                <?php endif; ?>

                <!-- Edit form (collapsible) -->
                <div>
                    <button @click="editOpen = !editOpen"
                            class="flex items-center gap-2 text-xs font-semibold text-[var(--color-accent)] hover:underline mb-3">
                        <span x-text="editOpen ? '▲ Hide form' : '▼ Update mileage &amp; service'"></span>
                    </button>
                    <div x-show="editOpen" x-cloak>
                        <form method="POST" action="/account/garage/<?= $id ?>/update" class="grid gap-4 sm:grid-cols-3">
                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">
                            <div>
                                <label class="mb-1.5 block text-xs font-mono font-semibold uppercase tracking-wider text-[var(--color-muted)]">Current Mileage (km)</label>
                                <input type="number" name="mileage_km" min="0" max="9999999"
                                       value="<?= $mileage !== null ? htmlspecialchars((string) $mileage) : '' ?>"
                                       placeholder="e.g. 12500"
                                       class="h-10 w-full border border-[var(--color-border)] bg-[var(--color-bg)] px-3 text-sm text-[var(--color-text)] focus:border-[var(--color-accent)] transition-colors">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-mono font-semibold uppercase tracking-wider text-[var(--color-muted)]">Service Interval (km)</label>
                                <input type="number" name="service_interval_km" min="0" max="9999999"
                                       value="<?= $interval !== null ? htmlspecialchars((string) $interval) : '' ?>"
                                       placeholder="e.g. 3000"
                                       class="h-10 w-full border border-[var(--color-border)] bg-[var(--color-bg)] px-3 text-sm text-[var(--color-text)] focus:border-[var(--color-accent)] transition-colors">
                            </div>
                            <div>
                                <label class="mb-1.5 block text-xs font-mono font-semibold uppercase tracking-wider text-[var(--color-muted)]">Last Serviced at (km)</label>
                                <input type="number" name="last_service_km" min="0" max="9999999"
                                       value="<?= $lastService !== null ? htmlspecialchars((string) $lastService) : '' ?>"
                                       placeholder="e.g. 10000"
                                       class="h-10 w-full border border-[var(--color-border)] bg-[var(--color-bg)] px-3 text-sm text-[var(--color-text)] focus:border-[var(--color-accent)] transition-colors">
                            </div>
                            <!-- hidden notes field to preserve existing value -->
                            <input type="hidden" name="notes" value="<?= htmlspecialchars($notes) ?>">
                            <div class="sm:col-span-3 flex justify-end">
                                <button type="submit" class="h-10 px-6 bg-[var(--color-accent)] hover:bg-[var(--color-accent-hover)] text-white text-sm font-semibold transition-colors">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ── TAB: NOTES ── -->
            <div x-show="tab === 'notes'" x-cloak class="px-5 py-5 space-y-4">
                <?php if ($notes !== ''): ?>
                    <div class="border-l-2 border-[var(--color-accent)]/40 bg-[var(--color-bg)] px-4 py-3">
                        <p class="text-sm text-[var(--color-text)] whitespace-pre-wrap"><?= htmlspecialchars($notes) ?></p>
                    </div>
                <?php else: ?>
                    <p class="text-sm text-[var(--color-muted)] italic">No notes yet. Use the form below to jot something down.</p>
                <?php endif; ?>

                <div>
                    <button @click="editOpen = !editOpen" class="flex items-center gap-2 text-xs font-semibold text-[var(--color-accent)] hover:underline mb-3">
                        <span x-text="editOpen ? '▲ Hide form' : '▼ <?= $notes !== '' ? 'Edit note' : 'Add a note' ?>'"></span>
                    </button>
                    <div x-show="editOpen" x-cloak>
                        <form method="POST" action="/account/garage/<?= $id ?>/update">
                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">
                            <!-- preserve mileage/service fields -->
                            <input type="hidden" name="mileage_km" value="<?= $mileage !== null ? (int) $mileage : '' ?>">
                            <input type="hidden" name="service_interval_km" value="<?= $interval !== null ? (int) $interval : '' ?>">
                            <input type="hidden" name="last_service_km" value="<?= $lastService !== null ? (int) $lastService : '' ?>">
                            <textarea name="notes" rows="4" maxlength="1000"
                                      placeholder="e.g. Needs new brake pads, exhaust modified, last tyres changed 2024..."
                                      class="w-full border border-[var(--color-border)] bg-[var(--color-bg)] px-3 py-2.5 text-sm text-[var(--color-text)] focus:border-[var(--color-accent)] transition-colors resize-none"><?= htmlspecialchars($notes) ?></textarea>
                            <div class="mt-2 flex justify-end">
                                <button type="submit" class="h-9 px-5 bg-[var(--color-accent)] hover:bg-[var(--color-accent-hover)] text-white text-sm font-semibold transition-colors">Save Note</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- ── TAB: PURCHASE HISTORY ── -->
            <div x-show="tab === 'history'" x-cloak class="px-5 py-5">
                <?php if (!empty($history)): ?>
                    <p class="text-xs text-[var(--color-muted)] mb-3">Orders where you bought parts compatible with this vehicle.</p>
                    <div class="divide-y divide-[var(--color-border)] border border-[var(--color-border)]">
                        <?php foreach ($history as $order): ?>
                            <div class="flex items-center justify-between gap-4 px-4 py-3 hover:bg-[var(--color-bg)] transition-colors">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="flex-shrink-0 w-2 h-2 rounded-full <?= match((string) ($order['status'] ?? '')) {
                                        'delivered' => 'bg-emerald-500',
                                        'shipped'   => 'bg-blue-500',
                                        'cancelled', 'refunded' => 'bg-rose-500',
                                        default     => 'bg-amber-500',
                                    } ?>"></div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-[var(--color-text)]"><?= htmlspecialchars((string) ($order['order_number'] ?? '')) ?></p>
                                        <p class="text-xs text-[var(--color-muted)]"><?= date('d M Y', strtotime((string) ($order['created_at'] ?? ''))) ?> &middot; <span class="capitalize"><?= htmlspecialchars((string) ($order['status'] ?? '')) ?></span></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 flex-shrink-0">
                                    <p class="text-sm font-bold text-[var(--color-text)]"><?= htmlspecialchars((string) ($order['currency_code'] ?? '')) ?> <?= number_format((float) ($order['grand_total'] ?? 0), 2) ?></p>
                                    <a href="/account/orders/<?= (int) ($order['id'] ?? 0) ?>"
                                       class="inline-flex h-7 items-center px-2.5 border border-[var(--color-border)] text-xs font-semibold text-[var(--color-muted)] hover:border-[var(--color-accent)] hover:text-[var(--color-accent)] transition-colors">View</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-3">
                        <a href="/account/orders?vehicle_id=<?= $vehicleId ?>" class="text-xs font-semibold text-[var(--color-accent)] hover:underline">View all orders →</a>
                    </div>
                <?php else: ?>
                    <div class="border border-dashed border-[var(--color-border)] px-5 py-8 text-center">
                        <p class="text-sm font-semibold text-[var(--color-text)]">No orders for this vehicle yet</p>
                        <p class="mt-1 text-xs text-[var(--color-muted)]">Parts you order that are compatible with this vehicle will appear here.</p>
                        <a href="/shop?vehicle_id=<?= $vehicleId ?>"
                           class="mt-3 inline-flex h-8 items-center bg-[var(--color-accent)] px-4 text-xs font-bold text-white hover:bg-[var(--color-accent-hover)] transition-colors">Shop compatible parts</a>
                    </div>
                <?php endif; ?>
            </div>

        </div><!-- /vehicle card -->
        <?php endforeach; ?>
        </div><!-- /space-y-4 -->

        <!-- Empty slot teaser -->
        <?php if ($savedCount < $garageLimit): ?>
            <div class="flex items-center justify-center gap-3 border border-dashed border-[var(--color-border)] hover:border-[var(--color-accent)]/40 transition-colors cursor-pointer py-5 group"
                 onclick="window.dispatchEvent(new CustomEvent('open-add'))">
                <div class="w-8 h-8 flex items-center justify-center border border-dashed border-[var(--color-border)] group-hover:border-[var(--color-accent)]/40 transition-colors">
                    <svg class="w-4 h-4 text-[var(--color-muted)] group-hover:text-[var(--color-accent)] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </div>
                <span class="text-sm font-semibold text-[var(--color-muted)] group-hover:text-[var(--color-text)] transition-colors">Add another vehicle — <?= $garageLimit - $savedCount ?> slot<?= ($garageLimit - $savedCount) !== 1 ? 's' : '' ?> remaining</span>
            </div>
        <?php endif; ?>

    <?php else: ?>

        <!-- ── EMPTY STATE ──────────────────────────────────────────────────── -->
        <div class="border border-dashed border-[var(--color-border)] bg-[var(--color-surface)] py-16 flex flex-col items-center justify-center text-center px-6">
            <div class="text-[var(--color-border)] mb-5">
                <svg viewBox="0 0 96 44" fill="none" class="w-24 h-auto mx-auto">
                    <circle cx="16" cy="33" r="9" stroke="currentColor" stroke-width="2"/>
                    <circle cx="80" cy="33" r="9" stroke="currentColor" stroke-width="2"/>
                    <path d="M25 31 L52 31 L52 35 L25 35 Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M52 33 L55 17 Q62 11 70 11 L77 11 L80 22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M40 20 Q44 15 52 14 L58 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    <path d="M25 31 Q30 19 40 17 L44 17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M77 11 L80 6 M75 6 L84 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-[var(--color-text)]">Your garage is empty</h2>
            <p class="mt-2 text-sm text-[var(--color-muted)] max-w-sm">Add your first vehicle above and we'll filter the entire shop to only show you compatible parts.</p>
            <p class="mt-4 text-xs text-[var(--color-muted)]">Up to <?= $garageLimit ?> vehicles · build spec card · mod wishlist · photo upload · total spent · mileage tracking · service reminders</p>
        </div>

    <?php endif; ?>

</div>
</section>
