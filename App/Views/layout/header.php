<?php
$headerGarageBrands = [];
$headerGarageVehicleOptions = [];
$headerCustomerGarageVehicles = [];
$headerSupportUrl = '/support';

try {
    $db = \App\Core\Database::getInstance();

    $brandRows = $db->table('brands')
        ->where('is_active', 1)
        ->orderBy('name', 'ASC')
        ->get();

    $brandById = [];
    foreach ($brandRows as $brandRow) {
        $brandId = (int) ($brandRow['id'] ?? 0);
        $brandName = (string) ($brandRow['name'] ?? 'Unknown');
        $brandById[$brandId] = $brandName;
        $headerGarageBrands[] = [
            'id' => $brandId,
            'name' => $brandName,
        ];
    }

    $vehicleRows = $db->table('vehicles')
        ->where('is_active', 1)
        ->orderBy('model', 'ASC')
        ->get();

    foreach ($vehicleRows as $vehicleRow) {
        $brandName = $brandById[(int) ($vehicleRow['brand_id'] ?? 0)] ?? 'Unknown';
        $headerGarageVehicleOptions[] = [
            'id' => (int) ($vehicleRow['id'] ?? 0),
            'brand_id' => (int) ($vehicleRow['brand_id'] ?? 0),
            'brand' => $brandName,
            'model' => (string) ($vehicleRow['model'] ?? ''),
            'label' => trim($brandName . ' ' . (string) ($vehicleRow['model'] ?? '')),
        ];
    }

    if (!empty($isLoggedIn) && !empty($currentCustomer['id'])) {
        $garageRows = $db->table('customer_vehicles')
            ->where('customer_id', (int) $currentCustomer['id'])
            ->orderBy('is_default', 'DESC')
            ->orderBy('id', 'DESC')
            ->get();

        foreach ($garageRows as $garageRow) {
            foreach ($headerGarageVehicleOptions as $option) {
                if ((int) $option['id'] !== (int) ($garageRow['vehicle_id'] ?? 0)) {
                    continue;
                }

                $headerCustomerGarageVehicles[] = [
                    'id' => (int) ($garageRow['id'] ?? 0),
                    'vehicle_id' => (int) ($garageRow['vehicle_id'] ?? 0),
                    'vehicle_type' => (string) ($garageRow['vehicle_type'] ?? 'scooter'),
                    'is_default' => (int) ($garageRow['is_default'] ?? 0) === 1,
                    'label' => (string) ($option['label'] ?? 'Vehicle'),
                ];
                break;
            }
        }
    }
} catch (\Throwable) {
    $headerGarageBrands = [];
    $headerGarageVehicleOptions = [];
    $headerCustomerGarageVehicles = [];
}
?>

<!-- Topbar -->
<div class="hidden xs:block border-b border-white/15 bg-zinc-900 text-[0.8125rem] text-white">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] flex items-center justify-between min-h-[2.5rem] py-0.5 gap-4 flex-wrap">
        <div class="hidden md:flex items-center gap-4">
            <span class="text-white/80">Free shipping on orders over $50</span>
        </div>
        <div class="flex items-center gap-3 ml-auto">
            <a href="/contact" class="hidden lg:inline text-white/80 hover:text-white transition-colors">Support: +1 800 555 1234</a>
            <a href="<?= htmlspecialchars($headerSupportUrl) ?>" class="text-white/80 hover:text-white transition-colors"><?= htmlspecialchars(__('customer_service')) ?></a>
            <?php if (!empty($isLoggedIn) && !empty($currentCustomer)): ?>
                <a href="/account" class="text-white/80 hover:text-white transition-colors">My Portal</a>
                <form method="POST" action="/logout" class="inline-flex">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
                    <button type="submit" class="text-white/80 hover:text-white transition-colors"><?= htmlspecialchars(__('logout')) ?></button>
                </form>
            <?php else: ?>
                <a href="/login" class="text-white/80 hover:text-white transition-colors"><?= htmlspecialchars(__('login')) ?> / <?= htmlspecialchars(__('account')) ?></a>
            <?php endif; ?>
            <button
                type="button"
                class="inline-flex h-8 items-center gap-1.5 rounded-md border border-white/25 bg-white/10 px-2 text-white/85 transition-colors hover:border-white/50 hover:text-white"
                @click="$store.theme.toggle()"
                :aria-label="$store.theme.dark ? 'Switch to light mode' : 'Switch to dark mode'"
            >
                <template x-if="$store.theme.dark">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                </template>
                <template x-if="!$store.theme.dark">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </template>
            </button>
        </div>
    </div>
</div>

<!-- Main Header -->
<header
    x-data='{
        mobileOpen: false,
        searchOpen: false,
        garageOpen: false,
        guestType: "scooter",
        guestBrandId: "",
        guestVehicleId: "",
        showAddForm: false,
        garageVehicleOptions: <?= htmlspecialchars((string) (json_encode($headerGarageVehicleOptions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]'), ENT_QUOTES, 'UTF-8') ?>,
        get guestBrands() {
            const m = {};
            this.garageVehicleOptions.forEach(v => { if (!m[v.brand_id]) m[v.brand_id] = { id: v.brand_id, name: v.brand }; });
            return Object.values(m).sort((a, b) => a.name.localeCompare(b.name));
        },
        get filteredModels() {
            return this.garageVehicleOptions.filter(v => !this.guestBrandId || String(v.brand_id) === String(this.guestBrandId));
        },
        addGuestVehicle() {
            if (!this.guestVehicleId) return;
            const opt = this.garageVehicleOptions.find(v => String(v.id) === String(this.guestVehicleId));
            if (!opt) return;
            $store.garageGuest.add({ vehicle_id: opt.id, brand: opt.brand, model: opt.model, label: opt.label, vehicle_type: this.guestType, brand_id: opt.brand_id });
            this.showAddForm = false; this.guestBrandId = ""; this.guestVehicleId = "";
        }
    }'
    class="sticky top-0 z-50 border-b bg-[var(--color-surface)] border-[var(--color-border)]"
    style="box-shadow: var(--shadow-sm)"
>
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] flex items-center justify-between gap-3 h-16">
        <!-- Logo -->
        <a href="/" class="flex items-center gap-2.5 shrink-0" aria-label="Scooter Dynamics">
            <img src="/assets/images/logo-dark.svg" alt="Scooter Dynamics" class="h-12 w-auto dark:hidden">
            <img src="/assets/images/logo-light.svg" alt="Scooter Dynamics" class="h-12 w-auto hidden dark:block">
        </a>

        <!-- Search (desktop) -->
        <div class="hidden sm:flex flex-1 max-w-xl mx-4">
            <form action="/shop" method="get" class="relative w-full">
                <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-muted)]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input
                    type="search" name="q"
                    placeholder="<?= htmlspecialchars(__('search')) ?>..."
                    class="h-10 w-full rounded-[var(--radius-input)] border border-[var(--color-border)] bg-[var(--color-bg)] pl-10 pr-4 text-sm focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10"
                >
            </form>
        </div>

        <!-- Navigation (desktop) -->
        <nav class="hidden lg:flex items-center gap-5 text-sm font-medium shrink-0">
            <a href="/" class="text-[var(--color-text)] hover:text-[var(--color-accent)] transition-colors"><?= htmlspecialchars(__('home')) ?></a>
            <a href="/shop" class="text-[var(--color-text)] hover:text-[var,--color-accent)] transition-colors"><?= htmlspecialchars(__('shop')) ?></a>
            <a href="/categories" class="text-[var(--color-text)] hover:text-[var,--color-accent)] transition-colors"><?= htmlspecialchars(__('categories')) ?></a>
            <a href="/about" class="text-[var,--color-text] hover:text-[var,--color-accent] transition-colors"><?= htmlspecialchars(__('about')) ?></a>
            <a href="/contact" class="text-[var,--color-text)] hover:text-[var,--color-accent] transition-colors"><?= htmlspecialchars(__('contact')) ?></a>
            <a href="<?= htmlspecialchars($headerSupportUrl) ?>" class="text-[var,--color-text] hover:text-[var,--color-accent] transition-colors"><?= htmlspecialchars(__('help')) ?></a>
            <?php if (!empty($isLoggedIn) && !empty($currentCustomer)): ?>
                <a href="/account" class="text-[var(--color-accent)] hover:text-[var,--color-accent-hover] transition-colors">Portal</a>
            <?php endif; ?>
        </nav>

        <!-- Right actions -->
        <div class="flex items-center gap-2 shrink-0">

            <!-- ── GARAGE BUTTON + PANEL ─────────────────────────────────── -->
            <div class="relative">
                <button
                    type="button"
                    @click="garageOpen = !garageOpen"
                    class="inline-flex h-10 items-center gap-2 rounded-[var(--radius-button)] border border-[var(--color-border)] bg-[var(--color-surface)] px-3 text-sm font-semibold text-[var(--color-text)] transition hover:border-[var(--color-accent)] hover:text-[var(--color-accent)]"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    <?php if (!empty($isLoggedIn) && !empty($currentCustomer)): ?>
                        <span class="hidden sm:inline">My Garage<?php if (!empty($headerCustomerGarageVehicles)): ?><span class="ml-1.5 inline-flex h-4 min-w-4 items-center justify-center rounded-sm bg-[var(--color-accent)] px-1 text-[10px] font-bold text-white"><?= count($headerCustomerGarageVehicles) ?></span><?php endif; ?></span>
                    <?php else: ?>
                        <span class="hidden sm:inline" x-text="$store.garageGuest.vehicles.length ? 'Garage (' + $store.garageGuest.vehicles.length + ')' : 'My Garage'"></span>
                    <?php endif; ?>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="hidden sm:block opacity-50" :class="garageOpen ? 'rotate-180' : ''"><polyline points="6 9 12 15 18 9"/></svg>
                </button>

                <!-- ── GARAGE PANEL ──────────────────────────────────────── -->
                <div
                    x-show="garageOpen"
                    x-cloak
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1"
                    @click.outside="garageOpen = false"
                    class="absolute right-0 mt-2 w-80 border border-[var(--color-border)] bg-[var(--color-surface)] z-50"
                    style="box-shadow: 0 8px 32px rgba(0,0,0,0.18); top: 100%;"
                >
                    <!-- Panel header -->
                    <div class="flex items-center justify-between border-b border-[var(--color-border)] px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="h-3 w-1 bg-[var(--color-accent)]"></div>
                            <span class="text-xs font-mono font-bold tracking-widest uppercase text-[var(--color-text)]">My Garage</span>
                        </div>
                        <?php if (!empty($isLoggedIn) && !empty($currentCustomer)): ?>
                            <a href="/account/garage" @click="garageOpen = false" class="text-[10px] font-semibold text-[var(--color-accent)] hover:underline uppercase tracking-wide">Manage →</a>
                        <?php else: ?>
                            <a href="/login" @click="garageOpen = false" class="text-[10px] font-semibold text-[var(--color-accent)] hover:underline uppercase tracking-wide">Sign in to sync →</a>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($isLoggedIn) && !empty($currentCustomer)): ?>
                        <!-- LOGGED-IN VEHICLES -->
                        <?php if (!empty($headerCustomerGarageVehicles)): ?>
                            <div class="divide-y divide-[var(--color-border)] max-h-56 overflow-y-auto">
                                <?php foreach ($headerCustomerGarageVehicles as $gv): ?>
                                    <div class="flex items-center gap-3 px-4 py-3 hover:bg-[var(--color-bg)] transition-colors group">
                                        <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center bg-[var(--color-accent)]/10 text-[var(--color-accent)]">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-[var(--color-text)] truncate"><?= htmlspecialchars((string) ($gv['label'] ?? 'Vehicle')) ?></p>
                                            <p class="text-[11px] text-[var(--color-muted)] capitalize"><?= htmlspecialchars(ucfirst((string) ($gv['vehicle_type'] ?? 'scooter'))) ?><?= !empty($gv['is_default']) ? ' &middot; <span class="text-[var(--color-accent)] font-semibold">Default</span>' : '' ?></p>
                                        </div>
                                        <a href="/shop?vehicle_id=<?= (int) ($gv['vehicle_id'] ?? 0) ?>" @click="garageOpen = false" class="flex-shrink-0 inline-flex h-7 items-center bg-[var(--color-accent)] px-2.5 text-[11px] font-bold text-white opacity-0 group-hover:opacity-100 transition-opacity hover:bg-[var(--color-accent-hover)]">Parts</a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="border-t border-[var(--color-border)] px-4 py-3 bg-[var(--color-bg)]">
                                <form action="/shop" method="get" class="flex gap-2">
                                    <select name="vehicle_id" class="flex-1 h-8 border border-[var(--color-border)] bg-[var(--color-surface)] px-2 text-xs text-[var(--color-text)] focus:border-[var(--color-accent)] focus:outline-none">
                                        <?php foreach ($headerCustomerGarageVehicles as $gv): ?>
                                            <option value="<?= (int) ($gv['vehicle_id'] ?? 0) ?>" <?= !empty($gv['is_default']) ? 'selected' : '' ?>><?= htmlspecialchars((string) ($gv['label'] ?? 'Vehicle')) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="inline-flex h-8 items-center bg-[var(--color-accent)] px-3 text-[11px] font-bold text-white hover:bg-[var(--color-accent-hover)] transition-colors">Find Parts</button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="px-4 py-8 text-center">
                                <svg class="mx-auto mb-3 opacity-20" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                                <p class="text-sm font-semibold text-[var(--color-text)]">No vehicles saved yet</p>
                                <p class="mt-1 text-xs text-[var(--color-muted)]">Add your scooter to find compatible parts instantly.</p>
                                <a href="/account/garage" @click="garageOpen = false" class="mt-3 inline-flex h-8 items-center bg-[var(--color-accent)] px-4 text-xs font-bold text-white hover:bg-[var(--color-accent-hover)] transition-colors">Add a Vehicle</a>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <!-- GUEST VEHICLES -->
                        <template x-if="$store.garageGuest.vehicles.length === 0 && !showAddForm">
                            <div class="px-4 py-8 text-center">
                                <svg class="mx-auto mb-3 opacity-20" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                                <p class="text-sm font-semibold text-[var(--color-text)]">No vehicles saved</p>
                                <p class="mt-1 text-xs text-[var(--color-muted)]">Save your ride to find compatible parts fast.</p>
                                <button type="button" @click="showAddForm = true" class="mt-3 inline-flex h-8 items-center bg-[var(--color-accent)] px-4 text-xs font-bold text-white hover:bg-[var(--color-accent-hover)] transition-colors">Add a Vehicle</button>
                            </div>
                        </template>

                        <template x-if="$store.garageGuest.vehicles.length > 0">
                            <div>
                                <div class="divide-y divide-[var(--color-border)] max-h-52 overflow-y-auto">
                                    <template x-for="v in $store.garageGuest.vehicles" :key="v.uid">
                                        <div class="flex items-center gap-3 px-4 py-3 hover:bg-[var(--color-bg)] transition-colors group">
                                            <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center bg-[var(--color-accent)]/10 text-[var(--color-accent)]">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-[var(--color-text)] truncate" x-text="v.label"></p>
                                                <p class="text-[11px] text-[var(--color-muted)] capitalize" x-text="v.vehicle_type"></p>
                                            </div>
                                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                                                <a :href="'/shop?vehicle_id=' + v.vehicle_id" @click="garageOpen = false" class="inline-flex h-7 items-center bg-[var(--color-accent)] px-2 text-[11px] font-bold text-white">Parts</a>
                                                <button type="button" @click="$store.garageGuest.remove(v.uid)" class="inline-flex h-7 w-7 items-center justify-center border border-[var(--color-border)] text-[var(--color-muted)] hover:border-rose-500 hover:text-rose-500 transition-colors">
                                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <div class="border-t border-[var(--color-border)] px-4 py-2.5 bg-[var(--color-bg)] flex items-center justify-between">
                                    <template x-if="$store.garageGuest.vehicles.length < 5">
                                        <button type="button" @click="showAddForm = true" class="text-xs font-semibold text-[var(--color-accent)] hover:underline">+ Add vehicle</button>
                                    </template>
                                    <template x-if="$store.garageGuest.vehicles.length >= 5">
                                        <span class="text-xs text-[var(--color-muted)]">5 / 5 slots used</span>
                                    </template>
                                    <span class="text-[10px] text-[var(--color-muted)]" x-text="$store.garageGuest.vehicles.length + ' / 5'"></span>
                                </div>
                            </div>
                        </template>

                        <!-- Add vehicle form (guest) -->
                        <template x-if="showAddForm && $store.garageGuest.vehicles.length < 5">
                            <div class="border-t border-[var(--color-border)] px-4 py-3 space-y-2 bg-[var(--color-bg)]">
                                <p class="text-[11px] font-bold uppercase tracking-widest text-[var(--color-muted)]">Add Vehicle</p>
                                <select x-model="guestType" class="h-9 w-full border border-[var(--color-border)] bg-[var(--color-surface)] px-2.5 text-sm text-[var(--color-text)] focus:border-[var(--color-accent)] focus:outline-none">
                                    <option value="scooter">Scooter</option>
                                    <option value="moped">Moped</option>
                                    <option value="motorcycle">Motorcycle</option>
                                </select>
                                <select x-model="guestBrandId" class="h-9 w-full border border-[var(--color-border)] bg-[var(--color-surface)] px-2.5 text-sm text-[var(--color-text)] focus:border-[var(--color-accent)] focus:outline-none">
                                    <option value="">Select manufacturer</option>
                                    <template x-for="b in guestBrands" :key="b.id">
                                        <option :value="b.id" x-text="b.name"></option>
                                    </template>
                                </select>
                                <select x-model="guestVehicleId" class="h-9 w-full border border-[var(--color-border)] bg-[var(--color-surface)] px-2.5 text-sm text-[var(--color-text)] focus:border-[var(--color-accent)] focus:outline-none">
                                    <option value="">Select model</option>
                                    <template x-for="v in filteredModels" :key="v.id">
                                        <option :value="v.id" x-text="v.label"></option>
                                    </template>
                                </select>
                                <div class="flex gap-2 pt-1">
                                    <button type="button" @click="addGuestVehicle()" :disabled="!guestVehicleId" class="flex-1 inline-flex h-9 items-center justify-center bg-[var(--color-accent)] text-xs font-bold text-white disabled:opacity-40 hover:bg-[var(--color-accent-hover)] transition-colors">Save Vehicle</button>
                                    <button type="button" @click="showAddForm = false; guestBrandId = ''; guestVehicleId = ''" class="inline-flex h-9 items-center justify-center border border-[var(--color-border)] px-3 text-xs text-[var(--color-muted)] hover:border-[var(--color-text)] transition-colors">Cancel</button>
                                </div>
                            </div>
                        </template>

                        <!-- Guest upsell footer -->
                        <div class="border-t border-[var(--color-border)] bg-[var(--color-accent)]/5 px-4 py-2.5 flex items-center justify-between">
                            <p class="text-[11px] text-[var(--color-muted)]">Sign in to save up to 10 vehicles</p>
                            <a href="/login" @click="garageOpen = false" class="text-[11px] font-bold text-[var(--color-accent)] hover:underline">Sign in →</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- ── END GARAGE ─────────────────────────────────────────────── -->

            <?php if (!empty($isLoggedIn) && !empty($currentCustomer)): ?>
                <a href="/account" class="hidden md:inline-flex h-10 items-center rounded-[var(--radius-button)] border border-[var(--color-border)] bg-[var(--color-surface)] px-3.5 text-sm font-semibold text-[var(--color-text)] transition hover:border-[var(--color-accent)] hover:text-[var(--color-accent)]">
                    <?= htmlspecialchars((string) ($currentCustomer['first_name'] ?? 'Account')) ?>
                </a>
            <?php else: ?>
                <a href="/login" class="hidden md:inline-flex h-10 items-center rounded-[var(--radius-button)] border border-[var(--color-border)] bg-[var(--color-surface)] px-3.5 text-sm font-semibold text-[var(--color-text)] transition hover:border-[var(--color-accent)] hover:text-[var(--color-accent)]">
                    Account
                </a>
            <?php endif; ?>

            <!-- Mobile search toggle -->
            <button
                @click="searchOpen = !searchOpen"
                class="sm:hidden inline-flex h-10 w-10 items-center justify-center rounded-[var(--radius-button)] border border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-muted)] hover:text-[var(--color-accent)] transition-colors"
            >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </button>

            <!-- Cart -->
            <a
                href="/cart"
                class="relative inline-flex h-10 items-center gap-2 rounded-[var(--radius-button)] bg-[var(--color-accent)] px-3.5 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]"
            >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                <span class="hidden xs:inline"><?= htmlspecialchars(__('cart')) ?></span>
                <template x-if="$store.cart.count > 0">
                    <span class="absolute -top-1.5 -right-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-white text-[var(--color-accent)] text-[11px] font-bold shadow" x-text="$store.cart.count"></span>
                </template>
            </a>

            <!-- Mobile menu toggle -->
            <button
                @click="mobileOpen = !mobileOpen"
                class="lg:hidden inline-flex h-10 w-10 items-center justify-center rounded-[var(--radius-button)] border border-[var(--color-border)] bg-[var(--color-surface)] text-[var(--color-text)] transition hover:border-[var(--color-accent)]"
            >
                <svg x-show="!mobileOpen" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                <svg x-show="mobileOpen" x-cloak width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
    </div>

    <!-- Mobile search bar -->
    <div x-show="searchOpen" x-cloak x-transition class="sm:hidden border-t border-[var(--color-border)] p-3">
        <form action="/shop" method="get" class="relative">
            <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-muted)]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="search" name="q" placeholder="<?= htmlspecialchars(__('search')) ?>..." class="h-10 w-full rounded-[var(--radius-input)] border border-[var(--color-border)] bg-[var(--color-bg)] pl-10 pr-4 text-sm focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10">
        </form>
    </div>

    <!-- Mobile nav -->
    <nav x-show="mobileOpen" x-cloak x-transition.opacity.duration.150ms class="lg:hidden border-t border-[var(--color-border)] bg-[var(--color-surface)]">
        <div class="mx-auto w-[92%] py-3 space-y-1">
            <a href="/" class="block px-3 py-2.5 text-sm font-medium text-[var(--color-text)] hover:bg-[var(--color-bg)] transition-colors"><?= htmlspecialchars(__('home')) ?></a>
            <a href="/shop" class="block px-3 py-2.5 text-sm font-medium text-[var(--color-text)] hover:bg-[var(--color-bg)] transition-colors"><?= htmlspecialchars(__('shop')) ?></a>
            <a href="/categories" class="block px-3 py-2.5 text-sm font-medium text-[var(--color-text)] hover:bg-[var(--color-bg)] transition-colors"><?= htmlspecialchars(__('categories')) ?></a>
            <a href="/about" class="block px-3 py-2.5 text-sm font-medium text-[var(--color-text)] hover:bg-[var(--color-bg)] transition-colors"><?= htmlspecialchars(__('about')) ?></a>
            <a href="/contact" class="block px-3 py-2.5 text-sm font-medium text-[var(--color-text)] hover:bg-[var(--color-bg)] transition-colors"><?= htmlspecialchars(__('contact')) ?></a>
            <a href="<?= htmlspecialchars($headerSupportUrl) ?>" class="block px-3 py-2.5 text-sm font-medium text-[var(--color-text)] hover:bg-[var(--color-bg)] transition-colors"><?= htmlspecialchars(__('customer_service')) ?></a>

            <?php if (!empty($isLoggedIn) && !empty($currentCustomer)): ?>
                <a href="/account/garage" class="block px-3 py-2.5 text-sm font-medium text-[var(--color-text)] hover:bg-[var(--color-bg)] transition-colors">My Garage</a>
                <a href="/account/tickets" class="block px-3 py-2.5 text-sm font-medium text-[var(--color-text)] hover:bg-[var(--color-bg)] transition-colors">My Tickets</a>
                <a href="/account/tickets/create" class="block px-3 py-2.5 text-sm font-medium text-[var(--color-text)] hover:bg-[var(--color-bg)] transition-colors">Open Ticket</a>
                <a href="/account" class="block px-3 py-2.5 text-sm font-medium text-[var(--color-accent)] hover:bg-[var(--color-bg)] transition-colors">My Portal</a>
                <form method="POST" action="/logout">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
                    <button type="submit" class="block w-full px-3 py-2.5 text-left text-sm font-medium text-[var(--color-text)] hover:bg-[var(--color-bg)] transition-colors">Logout</button>
                </form>
            <?php else: ?>
                <a href="/login" class="block px-3 py-2.5 text-sm font-medium text-[var(--color-text)] hover:bg-[var(--color-bg)] transition-colors"><?= htmlspecialchars(__('login')) ?></a>
                <a href="/register" class="block px-3 py-2.5 text-sm font-medium text-[var(--color-accent)] hover:bg-[var(--color-bg)] transition-colors"><?= htmlspecialchars(__('create_account')) ?></a>

                <!-- Mobile garage picker (guest) -->
                <div class="mt-2 border border-[var(--color-border)] bg-[var(--color-bg)] p-3">
                    <p class="text-xs font-bold uppercase tracking-widest text-[var(--color-muted)] mb-2">My Garage</p>

                    <template x-if="$store.garageGuest.vehicles.length > 0">
                        <div class="mb-2 space-y-1">
                            <template x-for="v in $store.garageGuest.vehicles" :key="v.uid">
                                <div class="flex items-center justify-between gap-2 border border-[var(--color-border)] px-2.5 py-2">
                                    <div>
                                        <span class="text-xs font-semibold text-[var(--color-text)]" x-text="v.label"></span>
                                        <span class="ml-1.5 text-[10px] text-[var(--color-muted)] capitalize" x-text="v.vehicle_type"></span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <a :href="'/shop?vehicle_id=' + v.vehicle_id" @click="mobileOpen = false" class="inline-flex h-6 items-center bg-[var(--color-accent)] px-2 text-[10px] font-bold text-white">Parts</a>
                                        <button type="button" @click="$store.garageGuest.remove(v.uid)" class="inline-flex h-6 w-6 items-center justify-center border border-[var(--color-border)] text-[var(--color-muted)] hover:text-rose-500 transition-colors">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <template x-if="$store.garageGuest.vehicles.length < 5">
                        <div class="space-y-2">
                            <select x-model="guestType" class="h-9 w-full border border-[var(--color-border)] bg-[var(--color-surface)] px-2.5 text-sm text-[var(--color-text)] focus:border-[var(--color-accent)] focus:outline-none">
                                <option value="scooter">Scooter</option>
                                <option value="moped">Moped</option>
                                <option value="motorcycle">Motorcycle</option>
                            </select>
                            <select x-model="guestBrandId" class="h-9 w-full border border-[var(--color-border)] bg-[var(--color-surface)] px-2.5 text-sm text-[var(--color-text)] focus:border-[var(--color-accent)] focus:outline-none">
                                <option value="">Select manufacturer</option>
                                <template x-for="b in guestBrands" :key="b.id">
                                    <option :value="b.id" x-text="b.name"></option>
                                </template>
                            </select>
                            <select x-model="guestVehicleId" class="h-9 w-full border border-[var(--color-border)] bg-[var(--color-surface)] px-2.5 text-sm text-[var(--color-text)] focus:border-[var(--color-accent)] focus:outline-none">
                                <option value="">Select model</option>
                                <template x-for="v in filteredModels" :key="'mob-' + v.id">
                                    <option :value="v.id" x-text="v.label"></option>
                                </template>
                            </select>
                            <button
                                type="button"
                                @click="addGuestVehicle(); if (guestVehicleId) { mobileOpen = false; }"
                                :disabled="!guestVehicleId"
                                class="inline-flex h-9 w-full items-center justify-center bg-[var(--color-accent)] text-xs font-bold text-white disabled:opacity-40 hover:bg-[var(--color-accent-hover)] transition-colors"
                            >Save & Find Parts</button>
                        </div>
                    </template>
                </div>
            <?php endif; ?>
        </div>
    </nav>
</header>
