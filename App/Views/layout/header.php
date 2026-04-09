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
            <a href="<?= htmlspecialchars($headerSupportUrl) ?>" class="text-white/80 hover:text-white transition-colors">Support Center</a>
            <?php if (!empty($isLoggedIn) && !empty($currentCustomer)): ?>
                <a href="/account" class="text-white/80 hover:text-white transition-colors">My Portal</a>
                <form method="POST" action="/logout" class="inline-flex">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
                    <button type="submit" class="text-white/80 hover:text-white transition-colors">Logout</button>
                </form>
            <?php else: ?>
                <a href="/login" class="text-white/80 hover:text-white transition-colors">Login / Account</a>
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
        guestGarageOpen: false,
        guestType: "scooter",
        guestBrandId: "",
        guestVehicleId: "",
        garageVehicleOptions: <?= htmlspecialchars((string) (json_encode($headerGarageVehicleOptions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]'), ENT_QUOTES, 'UTF-8') ?>
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
                    placeholder="Search products..."
                    class="h-10 w-full rounded-[var(--radius-input)] border border-[var(--color-border)] bg-[var(--color-bg)] pl-10 pr-4 text-sm focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10"
                >
            </form>
        </div>

        <!-- Navigation (desktop) -->
        <nav class="hidden lg:flex items-center gap-5 text-sm font-medium shrink-0">
            <a href="/" class="text-[var(--color-text)] hover:text-[var(--color-accent)] transition-colors">Home</a>
            <a href="/shop" class="text-[var(--color-text)] hover:text-[var,--color-accent)] transition-colors">Shop</a>
            <a href="/categories" class="text-[var(--color-text)] hover:text-[var,--color-accent)] transition-colors">Categories</a>
            <a href="/about" class="text-[var,--color-text] hover:text-[var,--color-accent] transition-colors">About</a>
            <a href="/contact" class="text-[var,--color-text)] hover:text-[var,--color-accent] transition-colors">Contact</a>
            <a href="<?= htmlspecialchars($headerSupportUrl) ?>" class="text-[var,--color-text] hover:text-[var,--color-accent] transition-colors">Support</a>
            <?php if (!empty($isLoggedIn) && !empty($currentCustomer)): ?>
                <a href="/account" class="text-[var(--color-accent)] hover:text-[var,--color-accent-hover] transition-colors">Portal</a>
            <?php endif; ?>
        </nav>

        <!-- Right actions -->
        <div class="flex items-center gap-2 shrink-0">
            <?php if (!empty($isLoggedIn) && !empty($currentCustomer)): ?>
                <?php if (!empty($headerCustomerGarageVehicles)): ?>
                    <form action="/shop" method="get" class="hidden xl:flex items-center gap-2 rounded-md border border-[var,--color-border] bg-[var,--color-bg)] px-2 py-1">
                        <select name="vehicle_id" class="h-8 rounded-md border border-[var,--color-border] bg-white px-2 text-xs text-[var,--color-text]">
                            <?php foreach ($headerCustomerGarageVehicles as $garageVehicle): ?>
                                <option value="<?= (int) ($garageVehicle['vehicle_id'] ?? 0) ?>" <?= !empty($garageVehicle['is_default']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars((string) ucfirst((string) ($garageVehicle['vehicle_type'] ?? 'scooter')) . ': ' . (string) ($garageVehicle['label'] ?? 'Vehicle')) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="inline-flex h-8 items-center rounded-md bg-[var(--color-accent)] px-2.5 text-xs font-semibold text-white">Find parts</button>
                    </form>
                <?php else: ?>
                    <a href="/account/garage" class="hidden xl:inline-flex h-10 items-center rounded-md border border-[var,--color-border] bg-[var,--color-bg)] px-3 text-sm font-semibold text-[var,--color-text]">Add vehicle</a>
                <?php endif; ?>

                <a href="/account" class="hidden md:inline-flex h-10 items-center rounded-[var,--radius-button] border border-[var,--color-border] bg-[var,--color-bg)] px-3.5 text-sm font-semibold text-[var,--color-text] transition hover:border-[var(--color-accent)] hover:text-[var,--color-accent)]">
                    <?= htmlspecialchars((string) ($currentCustomer['first_name'] ?? 'Account')) ?>
                </a>
            <?php else: ?>
                <div class="hidden xl:block relative">
                    <button type="button" @click="guestGarageOpen = !guestGarageOpen" class="inline-flex h-10 items-center rounded-md border border-[var,--color-border] bg-[var,--color-bg)] px-3 text-sm font-semibold text-[var,--color-text)]">
                        <span x-text="$store.garageGuest.selectedVehicle ? ('Vehicle: ' + $store.garageGuest.selectedVehicle.label) : 'Add vehicle'"></span>
                    </button>

                    <div x-show="guestGarageOpen" x-cloak @click.outside="guestGarageOpen = false" class="absolute right-0 mt-2 w-[21rem] rounded-md border border-[var,--color-border)] bg-[var,--color-surface)] p-3" style="box-shadow: var(--shadow-md)">
                        <p class="text-sm font-semibold text-[var,--color-text)]">Add vehicle</p>
                        <div class="mt-2 space-y-2">
                            <select x-model="guestType" class="h-9 w-full rounded-md border border-[var,--color-border)] bg-[var,--color-bg)] px-2.5 text-sm text-[var,--color-text)]">
                                <option value="scooter">Scooter</option>
                                <option value="moped">Moped</option>
                                <option value="motorcycle">Motorcycle</option>
                            </select>
                            <select x-model="guestBrandId" class="h-9 w-full rounded-md border border-[var,--color-border)] bg-[var,--color-bg)] px-2.5 text-sm text-[var,--color-text)]">
                                <option value="">Manufacturer</option>
                                <?php foreach ($headerGarageBrands as $brand): ?>
                                    <option value="<?= (int) $brand['id'] ?>"><?= htmlspecialchars((string) $brand['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select x-model="guestVehicleId" class="h-9 w-full rounded-md border border-[var,--color-border)] bg-[var,--color-bg)] px-2.5 text-sm text-[var,--color-text)]">
                                <option value="">Model</option>
                                <template x-for="vehicle in garageVehicleOptions.filter(v => !guestBrandId || String(v.brand_id) === String(guestBrandId))" :key="vehicle.id">
                                    <option :value="vehicle.id" x-text="vehicle.label"></option>
                                </template>
                            </select>
                            <button
                                type="button"
                                @click="(() => {
                                    const selected = garageVehicleOptions.find(v => String(v.id) === String(guestVehicleId));
                                    if (!selected) return;
                                    $store.garageGuest.add({
                                        vehicle_type: guestType,
                                        vehicle_id: selected.id,
                                        brand_id: selected.brand_id,
                                        label: selected.label,
                                    });
                                    guestGarageOpen = false;
                                })()"
                                class="inline-flex h-9 w-full items-center justify-center rounded-md bg-[var(--color-accent)] px-3 text-xs font-semibold text-white"
                            >
                                Save vehicle
                            </button>
                        </div>

                        <template x-if="$store.garageGuest.vehicles.length > 0">
                            <div class="mt-3 border-t border-[var,--color-border] pt-2.5">
                                <p class="text-xs font-semibold uppercase tracking-wide text-[var,--color-muted]">Saved</p>
                                <div class="mt-2 space-y-1.5 max-h-32 overflow-auto">
                                    <template x-for="vehicle in $store.garageGuest.vehicles" :key="vehicle.id">
                                        <div class="flex items-center justify-between gap-2 rounded-md border border-[var,--color-border] bg-[var,--color-bg)] px-2 py-1.5">
                                            <button type="button" @click="$store.garageGuest.select(vehicle.id)" class="text-xs text-left text-[var,--color-text)]" x-text="vehicle.label"></button>
                                            <button type="button" @click="$store.garageGuest.remove(vehicle.id)" class="text-[11px] text-rose-600">Remove</button>
                                        </div>
                                    </template>
                                </div>
                                <a :href="$store.garageGuest.selectedVehicle ? ('/shop?vehicle_id=' + $store.garageGuest.selectedVehicle.vehicle_id) : '/shop'" class="mt-2 inline-flex h-8 w-full items-center justify-center rounded-md border border-[var,--color-border)] text-xs font-semibold text-[var,--color-text)]">Find parts</a>
                            </div>
                        </template>
                    </div>
                </div>

                <a href="/login" class="hidden md:inline-flex h-10 items-center rounded-[var,--radius-button] border border-[var,--color-border] bg-[var,--color-bg)] px-3.5 text-sm font-semibold text-[var,--color-text] transition hover:border-[var(--color-accent)] hover:text-[var,--color-accent)]">
                    Account
                </a>
            <?php endif; ?>

            <!-- Mobile search toggle -->
            <button
                @click="searchOpen = !searchOpen"
                class="sm:hidden inline-flex h-10 w-10 items-center justify-center rounded-[var(--radius-button)] border border-[var,--color-border] bg-[var,--color-bg)] text-[var(--color-muted)] hover:text-[var,--color-accent)] transition-colors"
            >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </button>

            <!-- Cart -->
            <a
                href="/cart"
                class="relative inline-flex h-10 items-center gap-2 rounded-[var(--radius-button)] bg-[var,--color-accent] px-3.5 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]"
            >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                <span class="hidden xs:inline">Cart</span>
                <template x-if="$store.cart.count > 0">
                    <span class="absolute -top-1.5 -right-1.5 flex h-5 w-5 items-center justify-center rounded-full bg-white text-[var(--color-accent)] text-[11px] font-bold shadow" x-text="$store.cart.count"></span>
                </template>
            </a>

            <!-- Mobile menu toggle -->
            <button
                @click="mobileOpen = !mobileOpen"
                class="lg:hidden inline-flex h-10 w-10 items-center justify-center rounded-[var,--radius-button] border border-[var,--color-border] bg-[var,--color-bg)] text-[var,--color-text] transition hover:border-[var(--color-accent)]"
            >
                <svg x-show="!mobileOpen" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                <svg x-show="mobileOpen" x-cloak width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
    </div>

    <!-- Mobile search bar -->
    <div x-show="searchOpen" x-cloak x-transition class="sm:hidden border-t border-[var,--color-border] p-3">
        <form action="/shop" method="get" class="relative">
            <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-muted)]" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="search" name="q" placeholder="Search products..." class="h-10 w-full rounded-[var(--radius-input)] border border-[var,--color-border] bg-[var,--color-bg)] pl-10 pr-4 text-sm focus:border-[var,--color-accent] focus:ring-2 focus:ring-[var,--color-accent)]/10">
        </form>
    </div>

    <!-- Mobile nav -->
    <nav x-show="mobileOpen" x-cloak x-transition.opacity.duration.150ms class="lg:hidden border-t border-[var,--color-border] bg-[var,--color-surface)]">
        <div class="mx-auto w-[92%] py-3 space-y-1">
            <a href="/" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-[var,--color-text)] hover:bg-[var,--color-bg)] transition-colors">Home</a>
            <a href="/shop" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-[var,--color-text)] hover:bg-[var,--color-bg)] transition-colors">Shop</a>
            <a href="/categories" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-[var,--color-text)] hover:bg-[var,--color-bg)] transition-colors">Categories</a>
            <a href="/about" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-[var,--color-text)] hover:bg-[var,--color-bg)] transition-colors">About</a>
            <a href="/contact" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-[var,--color-text)] hover:bg-[var,--color-bg)] transition-colors">Contact</a>
            <a href="<?= htmlspecialchars($headerSupportUrl) ?>" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-[var,--color-text)] hover:bg-[var,--color-bg)] transition-colors">Support Center</a>
            <?php if (!empty($isLoggedIn) && !empty($currentCustomer)): ?>
                <a href="/account/tickets" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-[var,--color-text)] hover:bg-[var,--color-bg)] transition-colors">My Tickets</a>
                <a href="/account/tickets/create" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-[var,--color-text)] hover:bg-[var,--color-bg)] transition-colors">Open Ticket</a>
                <a href="/account" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-[var,--color-accent)] hover:bg-[var,--color-bg)] transition-colors">My Portal</a>
                <a href="/account/garage" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-[var,--color-text)] hover:bg-[var,--color-bg)] transition-colors">My Garage</a>
                <form method="POST" action="/logout">
                    <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) ($csrfToken ?? '')) ?>">
                    <button type="submit" class="block w-full rounded-lg px-3 py-2.5 text-left text-sm font-medium text-[var,--color-text)] hover:bg-[var,--color-bg)] transition-colors">Logout</button>
                </form>
            <?php else: ?>
                <a href="/login" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-[var,--color-text)] hover:bg-[var,--color-bg)] transition-colors">Login</a>
                <a href="/register" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-[var,--color-accent)] hover:bg-[var,--color-bg)] transition-colors">Create Account</a>
                <a href="/contact" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-[var,--color-text)] hover:bg-[var,--color-bg)] transition-colors">Contact Support</a>

                <div class="mt-2 rounded-md border border-[var(--color-border)] bg-[var,--color-bg)] p-3">
                    <p class="text-xs font-semibold uppercase tracking-wide text-[var,--color-muted]">Quick vehicle picker</p>
                    <div class="mt-2 space-y-2">
                        <select x-model="guestType" class="h-9 w-full rounded-md border border-[var,--color-border)] bg-white px-2.5 text-sm text-[var,--color-text)]">
                            <option value="scooter">Scooter</option>
                            <option value="moped">Moped</option>
                            <option value="motorcycle">Motorcycle</option>
                        </select>
                        <select x-model="guestBrandId" class="h-9 w-full rounded-md border border-[var,--color-border)] bg-white px-2.5 text-sm text-[var,--color-text)]">
                            <option value="">Manufacturer</option>
                            <?php foreach ($headerGarageBrands as $brand): ?>
                                <option value="<?= (int) $brand['id'] ?>"><?= htmlspecialchars((string) $brand['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select x-model="guestVehicleId" class="h-9 w-full rounded-md border border-[var,--color-border)] bg-white px-2.5 text-sm text-[var,--color-text)]">
                            <option value="">Model</option>
                            <template x-for="vehicle in garageVehicleOptions.filter(v => !guestBrandId || String(v.brand_id) === String(guestBrandId))" :key="'mobile-' + vehicle.id">
                                <option :value="vehicle.id" x-text="vehicle.label"></option>
                            </template>
                        </select>
                        <button
                            type="button"
                            @click="(() => {
                                const selected = garageVehicleOptions.find(v => String(v.id) === String(guestVehicleId));
                                if (!selected) return;
                                $store.garageGuest.add({ vehicle_type: guestType, vehicle_id: selected.id, brand_id: selected.brand_id, label: selected.label });
                                mobileOpen = false;
                                window.location.href = '/shop?vehicle_id=' + selected.id;
                            })()"
                            class="inline-flex h-9 w-full items-center justify-center rounded-md bg-[var(--color-accent)] px-3 text-xs font-semibold text-white"
                        >
                            Save & find parts
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </nav>
</header>
