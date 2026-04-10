<?php
/**
 * Landing page — My Garage section
 *
 * @var bool   $isLoggedIn
 * @var array  $garageVehicleOptions  [{id, brand_id, brand, model, label}]
 * @var array  $garageVehicles        [{id, vehicle_id, vehicle_type, is_default, label, brand, model}] (logged-in only)
 * @var string $csrfToken
 */
$isLoggedIn           = $isLoggedIn ?? false;
$garageLimit          = $isLoggedIn ? 10 : 5;
$garageVehicles       = $isLoggedIn ? ($garageVehicles ?? []) : [];
$garageVehicleOptions = $garageVehicleOptions ?? [];
$csrfToken            = $csrfToken ?? '';
$savedCount           = count($garageVehicles);

$vehicleOptionsJson = json_encode($garageVehicleOptions, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
?>

<!-- ===== MY GARAGE SECTION ===== -->
<section class="relative bg-[#07090f] border-y border-[#141c2a] text-white overflow-hidden">

    <!-- Subtle dot-grid background -->
    <div class="pointer-events-none absolute inset-0"
         style="background-image: radial-gradient(circle, rgba(255,255,255,0.04) 1px, transparent 1px); background-size: 28px 28px;"></div>

    <!-- Top accent bar -->
    <div class="absolute top-0 inset-x-0 h-[2px] bg-gradient-to-r from-transparent via-[var(--color-accent)] to-transparent"></div>

    <!-- Embed vehicle options as JSON for Alpine to read -->
    <script type="application/json" id="sb-garage-options"><?= $vehicleOptionsJson ?></script>

    <div class="relative mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-12"
         x-data="{
             isLoggedIn: <?= $isLoggedIn ? 'true' : 'false' ?>,
             limit: <?= $garageLimit ?>,
             showForm: false,
             brandId: '',
             vehicleId: '',
             vehicleType: 'scooter',
             vehicleOptions: [],
             init() {
                 this.vehicleOptions = JSON.parse(document.getElementById('sb-garage-options').textContent || '[]');
             },
             get brands() {
                 const m = {};
                 this.vehicleOptions.forEach(v => { if (!m[v.brand_id]) m[v.brand_id] = { id: v.brand_id, name: v.brand }; });
                 return Object.values(m).sort((a, b) => a.name.localeCompare(b.name));
             },
             get filteredModels() {
                 return this.vehicleOptions.filter(v => !this.brandId || String(v.brand_id) === String(this.brandId));
             },
             get usedSlots() {
                 return this.isLoggedIn ? <?= $savedCount ?> : $store.garageGuest.vehicles.length;
             },
             get canAdd() {
                 return this.usedSlots < this.limit;
             },
             openForm() {
                 if (this.canAdd) this.showForm = true;
             },
             addGuest() {
                 if (!this.vehicleId || !this.canAdd) return;
                 const opt = this.vehicleOptions.find(v => String(v.id) === String(this.vehicleId));
                 if (!opt) return;
                 $store.garageGuest.add({ vehicle_id: opt.id, brand: opt.brand, model: opt.model, label: opt.label, vehicle_type: this.vehicleType, brand_id: opt.brand_id });
                 this.showForm = false; this.brandId = ''; this.vehicleId = '';
             }
         }">

        <!-- ── HEADER ─────────────────────────────────────────────────────── -->
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-5 mb-8">
            <div>
                <p class="text-xs font-mono font-semibold tracking-[0.2em] text-[var(--color-accent)] uppercase mb-2">— My Garage</p>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight leading-tight">Find parts for your exact ride</h2>
                <p class="mt-1.5 text-sm text-slate-400 max-w-md">Save your scooter or moped once — we'll filter the entire shop to only show compatible parts.</p>
            </div>

            <!-- Slot counter + action -->
            <div class="flex items-center gap-4 flex-shrink-0">
                <div class="flex flex-col items-end gap-1.5">
                    <div class="flex items-center gap-1">
                        <?php if ($isLoggedIn): ?>
                            <?php for ($i = 1; $i <= $garageLimit; $i++): ?>
                                <div class="w-2.5 h-2.5 border <?= $i <= $savedCount ? 'bg-[var(--color-accent)] border-[var(--color-accent)]' : 'bg-transparent border-[#2a3d55]' ?>"></div>
                            <?php endfor; ?>
                        <?php else: ?>
                            <template x-for="i in limit" :key="i">
                                <div class="w-2.5 h-2.5 border transition-colors"
                                     :class="i <= $store.garageGuest.vehicles.length ? 'bg-[var(--color-accent)] border-[var(--color-accent)]' : 'bg-transparent border-[#2a3d55]'"></div>
                            </template>
                        <?php endif; ?>
                    </div>
                    <span class="text-xs font-mono text-slate-500">
                        <?php if ($isLoggedIn): ?>
                            <?= $savedCount ?>/<?= $garageLimit ?> slots
                        <?php else: ?>
                            <span x-text="$store.garageGuest.vehicles.length + '/<?= $garageLimit ?> slots'"></span>
                        <?php endif; ?>
                    </span>
                </div>
                <?php if ($isLoggedIn): ?>
                    <a href="/account/garage"
                       class="text-xs font-semibold text-slate-400 hover:text-white border border-[#1e2d40] hover:border-[#3a506e] px-3 py-2 transition-colors whitespace-nowrap">
                        Manage garage →
                    </a>
                <?php else: ?>
                    <a href="/login"
                       class="text-xs font-semibold text-slate-400 hover:text-white border border-[#1e2d40] hover:border-[#3a506e] px-3 py-2 transition-colors whitespace-nowrap">
                        Sign in to sync →
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- ── VEHICLE SLOTS GRID ─────────────────────────────────────────── -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">

            <?php if ($isLoggedIn): ?>
                <!-- ── LOGGED-IN: PHP-rendered filled slots ── -->
                <?php foreach ($garageVehicles as $gv): ?>
                    <?php $typeLabel = strtoupper((string) ($gv['vehicle_type'] ?? 'SCOOTER')); ?>
                    <div class="relative flex flex-col bg-[#0d1526] border border-[#1b2b3f] hover:border-[#2a4060] transition-colors group">
                        <!-- Top accent stripe -->
                        <div class="h-[3px] bg-[var(--color-accent)] w-full flex-shrink-0"></div>
                        <div class="p-4 flex flex-col flex-1">
                            <!-- Type badge -->
                            <span class="self-start text-[10px] font-mono font-bold tracking-widest text-[var(--color-accent)] mb-3"><?= htmlspecialchars($typeLabel) ?></span>
                            <!-- Scooter icon -->
                            <div class="flex-1 flex items-center justify-center py-1 text-slate-600 group-hover:text-slate-500 transition-colors">
                                <svg viewBox="0 0 96 44" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-20 h-auto">
                                    <circle cx="16" cy="33" r="9" stroke="currentColor" stroke-width="2"/>
                                    <circle cx="80" cy="33" r="9" stroke="currentColor" stroke-width="2"/>
                                    <path d="M25 31 L52 31 L52 35 L25 35 Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    <path d="M52 33 L55 17 Q62 11 70 11 L77 11 L80 22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M40 20 Q44 15 52 14 L58 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M25 31 Q30 19 40 17 L44 17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M77 11 L80 6 M75 6 L84 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <!-- Vehicle info -->
                            <div class="mt-2">
                                <p class="text-[11px] font-mono text-slate-500 uppercase tracking-wider"><?= htmlspecialchars((string) ($gv['brand'] ?? '')) ?></p>
                                <p class="text-base font-bold text-white leading-tight mt-0.5 truncate"><?= htmlspecialchars((string) ($gv['model'] ?? '')) ?></p>
                                <?php if (!empty($gv['is_default'])): ?>
                                    <span class="mt-1.5 inline-block text-[10px] font-mono text-[var(--color-accent)] border border-[var(--color-accent)]/30 px-1.5 py-0.5">DEFAULT</span>
                                <?php endif; ?>
                            </div>
                            <!-- Actions -->
                            <div class="mt-3 flex gap-2 items-center">
                                <a href="/shop?vehicle_id=<?= (int) ($gv['vehicle_id'] ?? 0) ?>"
                                   class="flex-1 text-center text-xs font-semibold bg-[var(--color-accent)] hover:bg-[var(--color-accent-hover)] text-white py-1.5 transition-colors">
                                    Find Parts
                                </a>
                                <form method="POST" action="/account/garage/<?= (int) $gv['id'] ?>/delete">
                                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                    <input type="hidden" name="_redirect" value="/">
                                    <button type="submit"
                                            class="w-8 h-8 flex items-center justify-center border border-[#1e2d40] hover:border-rose-500/40 hover:bg-rose-500/10 text-slate-500 hover:text-rose-400 transition-colors"
                                            title="Remove">
                                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- ── LOGGED-IN: empty slots ── -->
                <?php for ($i = $savedCount; $i < $garageLimit; $i++): ?>
                    <div class="flex flex-col items-center justify-center min-h-[180px] border border-dashed border-[#1b2b3f] hover:border-[var(--color-accent)]/40 cursor-pointer transition-colors group"
                         @click="openForm()">
                        <div class="w-8 h-8 border border-dashed border-[#2a3d55] group-hover:border-[var(--color-accent)]/50 flex items-center justify-center transition-colors mb-2">
                            <svg class="w-4 h-4 text-slate-600 group-hover:text-[var(--color-accent)]/70 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        </div>
                        <span class="text-xs text-slate-600 group-hover:text-slate-500 transition-colors font-mono">Empty slot</span>
                    </div>
                <?php endfor; ?>

            <?php else: ?>
                <!-- ── GUEST: Alpine-rendered filled slots ── -->
                <template x-for="(vehicle, idx) in $store.garageGuest.vehicles" :key="vehicle.uid">
                    <div class="relative flex flex-col bg-[#0d1526] border border-[#1b2b3f] hover:border-[#2a4060] transition-colors group">
                        <div class="h-[3px] bg-[var(--color-accent)] w-full flex-shrink-0"></div>
                        <div class="p-4 flex flex-col flex-1">
                            <span class="self-start text-[10px] font-mono font-bold tracking-widest text-[var(--color-accent)] mb-3 uppercase" x-text="vehicle.vehicle_type"></span>
                            <div class="flex-1 flex items-center justify-center py-1 text-slate-600 group-hover:text-slate-500 transition-colors">
                                <svg viewBox="0 0 96 44" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-20 h-auto">
                                    <circle cx="16" cy="33" r="9" stroke="currentColor" stroke-width="2"/>
                                    <circle cx="80" cy="33" r="9" stroke="currentColor" stroke-width="2"/>
                                    <path d="M25 31 L52 31 L52 35 L25 35 Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    <path d="M52 33 L55 17 Q62 11 70 11 L77 11 L80 22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M40 20 Q44 15 52 14 L58 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    <path d="M25 31 Q30 19 40 17 L44 17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M77 11 L80 6 M75 6 L84 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <div class="mt-2">
                                <p class="text-[11px] font-mono text-slate-500 uppercase tracking-wider" x-text="vehicle.brand"></p>
                                <p class="text-base font-bold text-white leading-tight mt-0.5 truncate" x-text="vehicle.model"></p>
                            </div>
                            <div class="mt-3 flex gap-2 items-center">
                                <a :href="'/shop?vehicle_id=' + vehicle.vehicle_id"
                                   class="flex-1 text-center text-xs font-semibold bg-[var(--color-accent)] hover:bg-[var(--color-accent-hover)] text-white py-1.5 transition-colors">
                                    Find Parts
                                </a>
                                <button @click="$store.garageGuest.remove(vehicle.uid)"
                                        class="w-8 h-8 flex items-center justify-center border border-[#1e2d40] hover:border-rose-500/40 hover:bg-rose-500/10 text-slate-500 hover:text-rose-400 transition-colors"
                                        title="Remove">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- ── GUEST: empty slots ── -->
                <template x-for="i in (limit - $store.garageGuest.vehicles.length)" :key="'e'+i">
                    <div class="flex flex-col items-center justify-center min-h-[180px] border border-dashed border-[#1b2b3f] hover:border-[var(--color-accent)]/40 cursor-pointer transition-colors group"
                         @click="openForm()">
                        <div class="w-8 h-8 border border-dashed border-[#2a3d55] group-hover:border-[var(--color-accent)]/50 flex items-center justify-center transition-colors mb-2">
                            <svg class="w-4 h-4 text-slate-600 group-hover:text-[var(--color-accent)]/70 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        </div>
                        <span class="text-xs text-slate-600 group-hover:text-slate-500 transition-colors font-mono">Empty slot</span>
                    </div>
                </template>
            <?php endif; ?>

        </div><!-- /grid -->


        <!-- ── ADD VEHICLE FORM ───────────────────────────────────────────── -->
        <div x-show="showForm"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             x-cloak
             class="mt-3 border border-[#1e2d40] bg-[#0b1320]">

            <div class="flex items-center justify-between px-4 py-3 border-b border-[#1a2740]">
                <span class="text-sm font-semibold text-white">Add a Vehicle</span>
                <button @click="showForm = false; brandId = ''; vehicleId = ''"
                        class="text-slate-500 hover:text-white transition-colors">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <div class="p-4">
                <!-- Form for LOGGED-IN (POST to /account/garage) -->
                <?php if ($isLoggedIn): ?>
                    <form method="POST" action="/account/garage"
                          class="flex flex-col sm:flex-row gap-3 items-end">
                        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        <input type="hidden" name="_redirect" value="/">
                        <div class="flex-shrink-0">
                            <label class="block text-[11px] font-mono text-slate-500 uppercase tracking-wider mb-1.5">Type</label>
                            <select name="vehicle_type" x-model="vehicleType"
                                    class="h-9 bg-[#0d1526] border border-[#1e2d40] text-white text-sm px-3 focus:border-[var(--color-accent)] transition-colors w-full sm:w-auto">
                                <option value="scooter">Scooter</option>
                                <option value="moped">Moped</option>
                                <option value="motorcycle">Motorcycle</option>
                            </select>
                        </div>
                        <div class="flex-1 min-w-0">
                            <label class="block text-[11px] font-mono text-slate-500 uppercase tracking-wider mb-1.5">Manufacturer</label>
                            <select x-model="brandId"
                                    class="h-9 w-full bg-[#0d1526] border border-[#1e2d40] text-white text-sm px-3 focus:border-[var(--color-accent)] transition-colors">
                                <option value="">Choose manufacturer</option>
                                <template x-for="b in brands" :key="b.id">
                                    <option :value="b.id" x-text="b.name"></option>
                                </template>
                            </select>
                        </div>
                        <div class="flex-1 min-w-0">
                            <label class="block text-[11px] font-mono text-slate-500 uppercase tracking-wider mb-1.5">Model</label>
                            <select name="vehicle_id" x-model="vehicleId" required
                                    class="h-9 w-full bg-[#0d1526] border border-[#1e2d40] text-white text-sm px-3 focus:border-[var(--color-accent)] transition-colors">
                                <option value="">Choose model</option>
                                <template x-for="m in filteredModels" :key="m.id">
                                    <option :value="m.id" x-text="m.label"></option>
                                </template>
                            </select>
                        </div>
                        <div class="flex-shrink-0">
                            <button type="submit"
                                    class="h-9 px-5 bg-[var(--color-accent)] hover:bg-[var(--color-accent-hover)] text-white text-sm font-semibold transition-colors whitespace-nowrap">
                                + Add to Garage
                            </button>
                        </div>
                    </form>

                <!-- Form for GUESTS (Alpine / localStorage) -->
                <?php else: ?>
                    <div class="flex flex-col sm:flex-row gap-3 items-end">
                        <div class="flex-shrink-0">
                            <label class="block text-[11px] font-mono text-slate-500 uppercase tracking-wider mb-1.5">Type</label>
                            <select x-model="vehicleType"
                                    class="h-9 bg-[#0d1526] border border-[#1e2d40] text-white text-sm px-3 focus:border-[var(--color-accent)] transition-colors w-full sm:w-auto">
                                <option value="scooter">Scooter</option>
                                <option value="moped">Moped</option>
                                <option value="motorcycle">Motorcycle</option>
                            </select>
                        </div>
                        <div class="flex-1 min-w-0">
                            <label class="block text-[11px] font-mono text-slate-500 uppercase tracking-wider mb-1.5">Manufacturer</label>
                            <select x-model="brandId"
                                    class="h-9 w-full bg-[#0d1526] border border-[#1e2d40] text-white text-sm px-3 focus:border-[var(--color-accent)] transition-colors">
                                <option value="">Choose manufacturer</option>
                                <template x-for="b in brands" :key="b.id">
                                    <option :value="b.id" x-text="b.name"></option>
                                </template>
                            </select>
                        </div>
                        <div class="flex-1 min-w-0">
                            <label class="block text-[11px] font-mono text-slate-500 uppercase tracking-wider mb-1.5">Model</label>
                            <select x-model="vehicleId"
                                    class="h-9 w-full bg-[#0d1526] border border-[#1e2d40] text-white text-sm px-3 focus:border-[var(--color-accent)] transition-colors">
                                <option value="">Choose model</option>
                                <template x-for="m in filteredModels" :key="m.id">
                                    <option :value="m.id" x-text="m.label"></option>
                                </template>
                            </select>
                        </div>
                        <div class="flex-shrink-0">
                            <button @click="addGuest()"
                                    :disabled="!vehicleId"
                                    class="h-9 px-5 bg-[var(--color-accent)] hover:bg-[var(--color-accent-hover)] disabled:opacity-40 disabled:cursor-not-allowed text-white text-sm font-semibold transition-colors whitespace-nowrap">
                                + Add to Garage
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div><!-- /add form -->


        <!-- ── "ADD VEHICLE" BUTTON (shown when form is closed and slots remain) ── -->
        <div class="mt-3" x-show="!showForm && canAdd" x-cloak>
            <button @click="showForm = true"
                    class="flex items-center gap-2 text-xs font-mono text-slate-500 hover:text-[var(--color-accent)] border border-dashed border-[#1b2b3f] hover:border-[var(--color-accent)]/40 px-4 py-2.5 transition-colors w-full sm:w-auto">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add a vehicle to your garage
            </button>
        </div>


        <!-- ── GUEST UPSELL BAR ───────────────────────────────────────────── -->
        <?php if (!$isLoggedIn): ?>
            <div class="mt-6 pt-5 border-t border-[#141c2a] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-8 h-8 border border-[#1e2d40] flex items-center justify-center mt-0.5">
                        <svg class="w-4 h-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white">Vehicles saved locally on this device</p>
                        <p class="text-xs text-slate-500 mt-0.5">Create a free account to sync across all your devices and unlock <strong class="text-slate-400">10 vehicle slots</strong>.</p>
                    </div>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <a href="/register"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-[var(--color-accent)] hover:bg-[var(--color-accent-hover)] px-4 py-2 transition-colors">
                        Create free account →
                    </a>
                    <a href="/login"
                       class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-400 hover:text-white border border-[#1e2d40] hover:border-[#3a506e] px-4 py-2 transition-colors">
                        Sign in
                    </a>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>
