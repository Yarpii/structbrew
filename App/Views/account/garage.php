<?php $activeAccountTab = 'garage'; ?>
<section class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-7 md:py-10">
    <div class="space-y-4">
        <?php include __DIR__ . '/_nav.php'; ?>

        <?php if (!empty($flashError)): ?>
            <div class="rounded-md border border-rose-500/20 bg-rose-500/10 px-4 py-3 text-sm text-rose-700 dark:text-rose-200"><?= htmlspecialchars((string) $flashError) ?></div>
        <?php endif; ?>
        <?php if (!empty($flashSuccess)): ?>
            <div class="rounded-md border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-700 dark:text-emerald-200"><?= htmlspecialchars((string) $flashSuccess) ?></div>
        <?php endif; ?>

        <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-5 md:p-6">
            <h1 class="text-2xl font-bold text-[var(--color-text)]">Add vehicle</h1>
            <p class="mt-1 text-sm text-[var(--color-muted)]">Save your scooters/mopeds once, then find matching parts faster.</p>

            <form method="POST" action="/account/garage" class="mt-4 grid gap-4 md:grid-cols-4" x-data="{ vehicles: <?= htmlspecialchars((string) json_encode($garageVehicleOptions ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8') ?>, brandId: '', vehicleId: '' }">
                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-[var(--color-text)]">Type</label>
                    <select name="vehicle_type" class="h-11 w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 text-[var(--color-text)]">
                        <option value="scooter">Scooter</option>
                        <option value="moped">Moped</option>
                        <option value="motorcycle">Motorcycle</option>
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-[var(--color-text)]">Manufacturer</label>
                    <select x-model="brandId" class="h-11 w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 text-[var(--color-text)]">
                        <option value="">Choose manufacturer</option>
                        <?php foreach (($garageBrands ?? []) as $brand): ?>
                            <option value="<?= (int) $brand['id'] ?>"><?= htmlspecialchars((string) $brand['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-[var(--color-text)]">Model</label>
                    <select name="vehicle_id" x-model="vehicleId" required class="h-11 w-full rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] px-3.5 text-[var(--color-text)]">
                        <option value="">Choose model</option>
                        <template x-for="vehicle in vehicles.filter(v => !brandId || String(v.brand_id) === String(brandId))" :key="vehicle.id">
                            <option :value="vehicle.id" x-text="vehicle.label"></option>
                        </template>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="inline-flex h-11 w-full items-center justify-center rounded-md bg-[var(--color-accent)] px-4 text-sm font-semibold text-white">Add vehicle</button>
                </div>
            </form>

            <p class="mt-3 text-xs text-[var(--color-muted)]">By having a customer account you can permanently save several vehicles and change them at any time.</p>
        </div>

        <div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-5 md:p-6">
            <h2 class="text-xl font-semibold text-[var(--color-text)]">Saved vehicles</h2>

            <?php if (!empty($garageVehicles)): ?>
                <div class="mt-4 space-y-3">
                    <?php foreach ($garageVehicles as $garageVehicle): ?>
                        <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] p-4">
                            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-[var(--color-text)]"><?= htmlspecialchars((string) ucfirst((string) ($garageVehicle['vehicle_type'] ?? 'scooter'))) ?></p>
                                    <p class="text-sm text-[var(--color-text)]"><?= htmlspecialchars((string) ($garageVehicle['label'] ?? 'Vehicle')) ?></p>
                                    <?php if (!empty($garageVehicle['is_default'])): ?>
                                        <span class="mt-1 inline-flex items-center rounded-md bg-[var(--color-accent)]/10 px-2 py-0.5 text-xs font-semibold text-[var(--color-accent)]">Default</span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <?php if (empty($garageVehicle['is_default'])): ?>
                                        <form method="POST" action="/account/garage/<?= (int) $garageVehicle['id'] ?>/select">
                                            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">
                                            <button type="submit" class="inline-flex h-9 items-center justify-center rounded-md border border-[var(--color-border)] px-3 text-xs font-semibold text-[var(--color-text)]">Set default</button>
                                        </form>
                                    <?php endif; ?>
                                    <a href="/shop?vehicle_id=<?= (int) ($garageVehicle['vehicle_id'] ?? 0) ?>" class="inline-flex h-9 items-center justify-center rounded-md border border-[var(--color-border)] px-3 text-xs font-semibold text-[var(--color-text)]">Find parts</a>
                                    <form method="POST" action="/account/garage/<?= (int) $garageVehicle['id'] ?>/delete" onsubmit="return confirm('Remove this vehicle from garage?');">
                                        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars((string) $csrfToken) ?>">
                                        <button type="submit" class="inline-flex h-9 items-center justify-center rounded-md border border-rose-500/20 bg-rose-500/10 px-3 text-xs font-semibold text-rose-600">Remove</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="mt-4 text-sm text-[var(--color-muted)]">No vehicles saved yet.</p>
            <?php endif; ?>
        </div>
    </div>
</section>
