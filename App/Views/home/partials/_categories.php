<!-- Part Finder Strip -->
<section class="bg-[var(--color-surface)] border-b border-[var(--color-border)]" style="box-shadow:var(--shadow-sm)">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-3.5">
        <div
            x-data='{
                brandId: "",
                vehicleId: "",
                options: <?= htmlspecialchars((string)(json_encode($garageVehicleOptions ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: "[]"), ENT_QUOTES, "UTF-8") ?>,
                get brands() {
                    const m = {};
                    this.options.forEach(v => { if (!m[v.brand_id]) m[v.brand_id] = { id: v.brand_id, name: v.brand }; });
                    return Object.values(m).sort((a, b) => a.name.localeCompare(b.name));
                },
                get models() {
                    return this.options.filter(v => !this.brandId || String(v.brand_id) === String(this.brandId));
                }
            }'
        >
            <form action="/shop" method="get"
                  class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">

                <!-- Label -->
                <p class="hidden lg:flex items-center gap-2 shrink-0 text-sm font-semibold text-[var(--color-text)] pr-3 border-r border-[var(--color-border)]">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[var(--color-accent)]"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    Find parts for your bike
                </p>

                <!-- Brand -->
                <select
                    x-model="brandId"
                    @change="vehicleId = ''"
                    name="brand_id"
                    class="h-10 rounded-[var(--radius-input)] border border-[var(--color-border)] bg-[var(--color-bg)] px-3 text-sm text-[var(--color-text)] focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10 sm:w-36"
                >
                    <option value="">All Brands</option>
                    <template x-for="b in brands" :key="b.id">
                        <option :value="b.id" x-text="b.name"></option>
                    </template>
                </select>

                <!-- Model -->
                <select
                    x-model="vehicleId"
                    name="vehicle_id"
                    class="h-10 rounded-[var(--radius-input)] border border-[var(--color-border)] bg-[var(--color-bg)] px-3 text-sm text-[var(--color-text)] focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10 sm:w-44"
                >
                    <option value="">All Models</option>
                    <template x-for="m in models" :key="m.id">
                        <option :value="m.id" x-text="m.model"></option>
                    </template>
                </select>

                <!-- Keyword -->
                <div class="relative flex-1">
                    <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-muted)]" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input
                        type="search" name="q"
                        placeholder="Search parts, accessories..."
                        class="h-10 w-full rounded-[var(--radius-input)] border border-[var(--color-border)] bg-[var(--color-bg)] pl-9 pr-4 text-sm focus:border-[var(--color-accent)] focus:ring-2 focus:ring-[var(--color-accent)]/10"
                    >
                </div>

                <!-- Submit -->
                <button type="submit"
                        class="h-10 shrink-0 inline-flex items-center gap-1.5 rounded-[var(--radius-button)] bg-[var(--color-accent)] px-5 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">
                    Search
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>

                <!-- Browse all -->
                <a href="/shop"
                   class="hidden md:inline-flex h-10 shrink-0 items-center gap-1 border-l border-[var(--color-border)] pl-4 ml-1 text-sm text-[var(--color-muted)] transition hover:text-[var(--color-accent)]">
                    Browse all
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>

            </form>
        </div>
    </div>
</section>
