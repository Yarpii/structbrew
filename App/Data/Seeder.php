<?php

declare(strict_types=1);

namespace App\Data;

use App\Core\Auth;
use App\Core\Database;
use RuntimeException;

class Seeder
{
    public static function run(): void
    {
        $db = Database::getInstance();

        // ─── Websites / Stores / Views / Domains (from static seed snapshot) ───
        echo "  Seeding market structure...\n";
        $marketSeed = self::seedMarketStructure($db);

        $nlViewId = $marketSeed['preferred_view_ids']['nl'] ?? $marketSeed['default_view_id'];
        $deViewId = $marketSeed['preferred_view_ids']['de'] ?? $marketSeed['default_view_id'];
        $frViewId = $marketSeed['preferred_view_ids']['fr'] ?? $marketSeed['default_view_id'];
        $enViewId = $marketSeed['preferred_view_ids']['en'] ?? $marketSeed['default_view_id'];

        $viewIds = array_values(array_unique(array_map('intval', [$nlViewId, $deViewId, $frViewId, $enViewId])));

        // ─── Currencies ──────────────────────────────────────
        echo "  Seeding currencies...\n";
        $currencies = [
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => "\u{20AC}", 'decimal_places' => 2],
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$', 'decimal_places' => 2],
            ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => "\u{00A3}", 'decimal_places' => 2],
            ['code' => 'CHF', 'name' => 'Swiss Franc', 'symbol' => 'CHF', 'decimal_places' => 2],
        ];
        foreach ($currencies as $c) {
            $db->table('currencies')->insert(array_merge($c, [
                'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ]));
        }

        // ─── Tax Zones & Rates ───────────────────────────────
        echo "  Seeding tax zones...\n";
        $taxData = [
            ['name' => 'Netherlands', 'country_code' => 'NL', 'rate' => 21.00],
            ['name' => 'Germany', 'country_code' => 'DE', 'rate' => 19.00],
            ['name' => 'France', 'country_code' => 'FR', 'rate' => 20.00],
            ['name' => 'Belgium', 'country_code' => 'BE', 'rate' => 21.00],
            ['name' => 'Austria', 'country_code' => 'AT', 'rate' => 20.00],
            ['name' => 'Italy', 'country_code' => 'IT', 'rate' => 22.00],
            ['name' => 'Spain', 'country_code' => 'ES', 'rate' => 21.00],
            ['name' => 'United Kingdom', 'country_code' => 'GB', 'rate' => 20.00],
        ];
        foreach ($taxData as $t) {
            $zoneId = $db->table('tax_zones')->insert([
                'name' => $t['name'], 'country_code' => $t['country_code'], 'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $db->table('tax_rates')->insert([
                'tax_zone_id' => $zoneId, 'name' => "BTW {$t['country_code']}", 'rate' => $t['rate'], 'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // ─── Brands ──────────────────────────────────────────
        echo "  Seeding brands...\n";
        $brandData = [
            ['name' => 'Piaggio', 'slug' => 'piaggio'],
            ['name' => 'Vespa', 'slug' => 'vespa'],
            ['name' => 'Aprilia', 'slug' => 'aprilia'],
            ['name' => 'Gilera', 'slug' => 'gilera'],
            ['name' => 'Yamaha', 'slug' => 'yamaha'],
            ['name' => 'Honda', 'slug' => 'honda'],
            ['name' => 'Peugeot', 'slug' => 'peugeot'],
            ['name' => 'Sym', 'slug' => 'sym'],
            ['name' => 'Kymco', 'slug' => 'kymco'],
            ['name' => 'Derbi', 'slug' => 'derbi'],
            ['name' => 'Malossi', 'slug' => 'malossi'],
            ['name' => 'Polini', 'slug' => 'polini'],
            ['name' => 'Stage6', 'slug' => 'stage6'],
            ['name' => 'Yasuni', 'slug' => 'yasuni'],
            ['name' => 'Naraku', 'slug' => 'naraku'],
        ];
        $brandIds = [];
        foreach ($brandData as $i => $b) {
            $brandIds[$b['slug']] = $db->table('brands')->insert(array_merge($b, [
                'is_active' => 1, 'sort_order' => $i, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ]));
        }

        // ─── Vehicles ────────────────────────────────────────
        echo "  Seeding vehicles...\n";
        $vehicleData = [
            ['brand' => 'piaggio', 'model' => 'Zip 2T', 'year_from' => 2000, 'year_to' => 2023, 'cc' => 50],
            ['brand' => 'piaggio', 'model' => 'Zip 4T', 'year_from' => 2006, 'year_to' => 2023, 'cc' => 50],
            ['brand' => 'piaggio', 'model' => 'NRG Power', 'year_from' => 2005, 'year_to' => 2018, 'cc' => 50],
            ['brand' => 'piaggio', 'model' => 'Typhoon', 'year_from' => 2010, 'year_to' => 2023, 'cc' => 50],
            ['brand' => 'vespa', 'model' => 'Primavera', 'year_from' => 2014, 'year_to' => 2024, 'cc' => 50],
            ['brand' => 'vespa', 'model' => 'Sprint', 'year_from' => 2014, 'year_to' => 2024, 'cc' => 50],
            ['brand' => 'vespa', 'model' => 'LX 50', 'year_from' => 2005, 'year_to' => 2014, 'cc' => 50],
            ['brand' => 'yamaha', 'model' => 'Aerox', 'year_from' => 2003, 'year_to' => 2023, 'cc' => 50],
            ['brand' => 'yamaha', 'model' => 'Neo\'s', 'year_from' => 2008, 'year_to' => 2023, 'cc' => 50],
            ['brand' => 'yamaha', 'model' => 'Jog R', 'year_from' => 2002, 'year_to' => 2016, 'cc' => 50],
            ['brand' => 'honda', 'model' => 'PCX 125', 'year_from' => 2010, 'year_to' => 2024, 'cc' => 125],
            ['brand' => 'honda', 'model' => 'SH 125', 'year_from' => 2013, 'year_to' => 2024, 'cc' => 125],
            ['brand' => 'peugeot', 'model' => 'Speedfight', 'year_from' => 2009, 'year_to' => 2022, 'cc' => 50],
            ['brand' => 'peugeot', 'model' => 'Kisbee', 'year_from' => 2010, 'year_to' => 2023, 'cc' => 50],
            ['brand' => 'sym', 'model' => 'Orbit', 'year_from' => 2007, 'year_to' => 2020, 'cc' => 50],
            ['brand' => 'kymco', 'model' => 'Agility', 'year_from' => 2006, 'year_to' => 2023, 'cc' => 50],
        ];
        $vehicleIds = [];
        foreach ($vehicleData as $v) {
            $slug = strtolower(str_replace([' ', "'"], ['-', ''], $v['brand'] . '-' . $v['model'] . '-' . $v['cc'] . 'cc'));
            $vehicleIds[] = $db->table('vehicles')->insert([
                'brand_id' => $brandIds[$v['brand']], 'model' => $v['model'],
                'year_from' => $v['year_from'], 'year_to' => $v['year_to'],
                'engine_cc' => $v['cc'], 'slug' => $slug, 'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // ─── Categories ──────────────────────────────────────
        echo "  Seeding categories...\n";
        $catIds = self::seedCategoryTaxonomy($db, $viewIds);

        // ─── Products ────────────────────────────────────────
        echo "  Seeding products...\n";
        $products = [
            ['sku' => 'CYL-MAL-7316', 'slug' => 'malossi-70cc-cilinder-piaggio', 'brand' => 'malossi', 'cat' => 'motor-aandrijving',
             'weight' => 1.8, 'stock' => 25, 'oem' => '3111374',
             'en' => ['name' => 'Malossi 70cc Cylinder Kit', 'desc' => 'High-performance 70cc cylinder kit for Piaggio 2-stroke scooters.'],
             'nl' => ['name' => 'Malossi 70cc Cilinderkit', 'desc' => 'High-performance 70cc cilinderkit voor Piaggio 2-takt scooters.'],
             'de' => ['name' => 'Malossi 70cc Zylinder Kit', 'desc' => 'Hochleistungs-70cc-Zylinderkit f??r Piaggio 2-Takt-Roller.'],
             'fr' => ['name' => 'Kit Cylindre Malossi 70cc', 'desc' => 'Kit cylindre haute performance 70cc voor scooters Piaggio 2 temps.'],
             'price' => 189.95, 'sale' => 169.95],
            ['sku' => 'EXH-YAS-2080', 'slug' => 'yasuni-r-uitlaat-piaggio', 'brand' => 'yasuni', 'cat' => 'uitlaten',
             'weight' => 3.2, 'stock' => 15, 'oem' => null,
             'en' => ['name' => 'Yasuni R Exhaust Piaggio', 'desc' => 'Yasuni R performance exhaust for Piaggio 2-stroke 50cc.'],
             'nl' => ['name' => 'Yasuni R Uitlaat Piaggio', 'desc' => 'Yasuni R performance uitlaat voor Piaggio 2-takt 50cc.'],
             'de' => ['name' => 'Yasuni R Auspuff Piaggio', 'desc' => 'Yasuni R Performance-Auspuff f??r Piaggio 2-Takt 50ccm.'],
             'fr' => ['name' => "Pot d'\u{00E9}chappement Yasuni R Piaggio", 'desc' => "\u{00C9}chappement performance Yasuni R voor Piaggio 2 temps 50cc."],
             'price' => 149.95, 'sale' => null],
            ['sku' => 'VAR-MAL-5113', 'slug' => 'malossi-multivar-variateur', 'brand' => 'malossi', 'cat' => 'motor-aandrijving',
             'weight' => 0.9, 'stock' => 30, 'oem' => '5111258',
             'en' => ['name' => 'Malossi Multivar Variator', 'desc' => 'Malossi Multivar 2000 variator for Piaggio/Vespa engines.'],
             'nl' => ['name' => 'Malossi Multivar Variateur', 'desc' => 'Malossi Multivar 2000 variateur voor Piaggio/Vespa motoren.'],
             'de' => ['name' => 'Malossi Multivar Variator', 'desc' => 'Malossi Multivar 2000 Variator f??r Piaggio/Vespa Motoren.'],
             'fr' => ['name' => 'Variateur Malossi Multivar', 'desc' => 'Variateur Malossi Multivar 2000 voor motors Piaggio/Vespa.'],
             'price' => 89.95, 'sale' => null],
            ['sku' => 'BRK-STG6-001', 'slug' => 'stage6-racing-remblokken', 'brand' => 'stage6', 'cat' => 'remsysteem',
             'weight' => 0.15, 'stock' => 100, 'oem' => null,
             'en' => ['name' => 'Stage6 Racing Brake Pads', 'desc' => 'High-performance sintered brake pads for most 50cc scooters.'],
             'nl' => ['name' => 'Stage6 Racing Remblokken', 'desc' => 'High-performance gesinterde remblokken voor de meeste 50cc scooters.'],
             'de' => ['name' => 'Stage6 Racing Bremsbel??ge', 'desc' => 'Hochleistungs-Sinter-Bremsbel??ge f??r die meisten 50ccm Roller.'],
             'fr' => ['name' => 'Plaquettes de frein Stage6 Racing', 'desc' => 'Plaquettes de frein fritt??es haute performance pour la plupart des scooters 50cc.'],
             'price' => 14.95, 'sale' => 11.95],
            ['sku' => 'CDI-NAR-4200', 'slug' => 'naraku-racing-cdi', 'brand' => 'naraku', 'cat' => 'elektra',
             'weight' => 0.12, 'stock' => 45, 'oem' => null,
             'en' => ['name' => 'Naraku Racing CDI', 'desc' => 'Unrestricted racing CDI unit. Removes speed limiter.'],
             'nl' => ['name' => 'Naraku Racing CDI', 'desc' => 'Onbegrensd racing CDI unit. Verwijdert snelheidsbegrenzer.'],
             'de' => ['name' => 'Naraku Racing CDI', 'desc' => 'Unbegrenzte Racing-CDI-Einheit. Entfernt Geschwindigkeitsbegrenzung.'],
             'fr' => ['name' => 'CDI Racing Naraku', 'desc' => "Unit\u{00E9} CDI racing sans restriction. Supprime le limiteur de vitesse."],
             'price' => 29.95, 'sale' => null],
            ['sku' => 'FLT-POL-2030', 'slug' => 'polini-luchtfilter-piaggio', 'brand' => 'polini', 'cat' => 'filters',
             'weight' => 0.25, 'stock' => 60, 'oem' => null,
             'en' => ['name' => 'Polini Air Filter', 'desc' => 'Direct-fit performance air filter for Piaggio 2-stroke.'],
             'nl' => ['name' => 'Polini Luchtfilter', 'desc' => 'Direct-fit performance luchtfilter voor Piaggio 2-takt.'],
             'de' => ['name' => 'Polini Luftfilter', 'desc' => 'Direkt passender Performance-Luftfilter f??r Piaggio 2-Takt.'],
             'fr' => ['name' => "Filtre \u{00E0} air Polini", 'desc' => "Filtre \u{00E0} air performance voor Piaggio 2 temps."],
             'price' => 24.95, 'sale' => 19.95],
            ['sku' => 'TIR-MIC-1050', 'slug' => 'michelin-city-grip-120-70', 'brand' => null, 'cat' => 'wielen-banden',
             'weight' => 2.1, 'stock' => 40, 'oem' => null,
             'en' => ['name' => 'Michelin City Grip 120/70-12', 'desc' => 'Premium all-season scooter tyre. Excellent wet grip.'],
             'nl' => ['name' => 'Michelin City Grip 120/70-12', 'desc' => 'Premium all-season scooterband. Uitstekende grip op nat wegdek.'],
             'de' => ['name' => 'Michelin City Grip 120/70-12', 'desc' => 'Premium Ganzjahres-Rollerreifen. Hervorragender Nassgriff.'],
             'fr' => ['name' => 'Michelin City Grip 120/70-12', 'desc' => "Pneu scooter premium toutes saisons. Excellente adh\u{00E9}rence sur sol mouill\u{00E9}."],
             'price' => 39.95, 'sale' => null],
            ['sku' => 'HEL-AGV-K5', 'slug' => 'agv-k5-s-helm-zwart', 'brand' => null, 'cat' => 'accessoires',
             'weight' => 1.5, 'stock' => 12, 'oem' => null,
             'en' => ['name' => 'AGV K5 S Helmet - Matte Black', 'desc' => 'Full-face helmet with integrated sun visor. ECE certified.'],
             'nl' => ['name' => 'AGV K5 S Helm - Mat Zwart', 'desc' => 'Integraalhelm met ge??lingeerd zonnescherm. ECE gekeurd.'],
             'de' => ['name' => 'AGV K5 S Helm - Matt Schwarz', 'desc' => 'Integralhelm mit integriertem Sonnenvisier. ECE-zertifiziert.'],
             'fr' => ['name' => 'Casque AGV K5 S - Noir Mat', 'desc' => "Casque int\u{00E9}gral avec visi\u{00E8}re solaire int\u{00E9}gr\u{00E9}e. Certifi\u{00E9} ECE."],
             'price' => 199.95, 'sale' => 179.95],
        ];

        $localeViewMap = ['nl' => $nlViewId, 'de' => $deViewId, 'fr' => $frViewId, 'en' => $enViewId];

        foreach ($products as $p) {
            $productId = $db->table('products')->insert([
                'sku' => $p['sku'], 'slug' => $p['slug'],
                'brand_id' => $p['brand'] ? ($brandIds[$p['brand']] ?? null) : null,
                'weight' => $p['weight'], 'is_active' => 1, 'is_featured' => rand(0, 1),
                'manage_stock' => 1, 'stock_qty' => $p['stock'], 'low_stock_threshold' => 5,
                'oem_number' => $p['oem'],
                'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // Translations per store view
            $seenTranslationViews = [];
            foreach ($localeViewMap as $lang => $svId) {
                if (in_array((int) $svId, $seenTranslationViews, true)) {
                    continue;
                }
                $seenTranslationViews[] = (int) $svId;
                $db->table('product_translations')->insert([
                    'product_id' => $productId, 'store_view_id' => $svId,
                    'name' => $p[$lang]['name'], 'short_description' => $p[$lang]['desc'],
                    'description' => $p[$lang]['desc'], 'meta_title' => $p[$lang]['name'],
                    'url_key' => $p['slug'],
                ]);
            }

            // Pricing per store view (same EUR for all in this seed)
            foreach ($viewIds as $svId) {
                $db->table('product_pricing')->insert([
                    'product_id' => $productId, 'store_view_id' => $svId,
                    'price' => $p['price'],
                    'sale_price' => $p['sale'],
                    'currency_code' => 'EUR',
                ]);
            }

            // Category
            $legacyCategoryMap = [
                'motor-aandrijving' => 'engine-components',
                'remsysteem' => 'braking-systems',
                'elektra' => 'electrical-ignition-systems',
                'carrosserie' => 'body-fairing',
                'wielen-banden' => 'wheels-tires-hubs',
                'tuning' => 'performance-tuning',
                'accessoires' => 'safety-riding-gear',
                'uitlaten' => 'exhaust-systems',
                'filters' => 'filters-service-items',
            ];
            $productCategorySlug = $legacyCategoryMap[$p['cat']] ?? $p['cat'];
            if (isset($catIds[$productCategorySlug])) {
                $db->table('product_categories')->insert([
                    'product_id' => $productId, 'category_id' => $catIds[$productCategorySlug], 'position' => 0,
                ]);
            }

            // Random vehicle compatibility
            $numVehicles = rand(2, 5);
            $randomVehicles = array_rand(array_flip($vehicleIds), min($numVehicles, count($vehicleIds)));
            if (!is_array($randomVehicles)) $randomVehicles = [$randomVehicles];
            foreach ($randomVehicles as $vId) {
                $db->table('product_vehicles')->insert([
                    'product_id' => $productId, 'vehicle_id' => $vId,
                ]);
            }
        }

        echo "  Seeding additional category products...\n";
        self::seedAdditionalCategoryProducts($db, $brandIds, $vehicleIds, $viewIds, $localeViewMap, $enViewId);
        echo "  Seeding product media & technical content...\n";
        self::seedProductRichContent($db, $enViewId);

        // ─── Shipping Methods ────────────────────────────────
        echo "  Seeding shipping methods...\n";
        $shippingMethods = [
            ['code' => 'standard', 'name' => 'Standard Shipping', 'sort_order' => 0],
            ['code' => 'express', 'name' => 'Express Shipping', 'sort_order' => 1],
            ['code' => 'pickup', 'name' => 'Store Pickup', 'sort_order' => 2],
        ];
        foreach ($shippingMethods as $sm) {
            $db->table('shipping_methods')->insert(array_merge($sm, [
                'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ]));
        }

        // ─── Admin Role ──────────────────────────────────────
        echo "  Seeding admin role...\n";
        $db->table('admin_roles')->insert([
            'name' => 'Super Admin', 'permissions' => json_encode(['*']),
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // NOTE: Admin user must be created via `php brew admin:create` (interactive).
        // Hardcoded default credentials are a security risk for production.
        echo "  Done! Create an admin user with: php brew admin:create\n";
    }

    private static function seedMarketStructure(Database $db): array
    {
        $markets = self::seedMarketsNormalized();
        if (empty($markets)) {
            throw new RuntimeException('No market definitions found.');
        }

        $websiteIds = [];
        $domainAssigned = [];
        $preferredViewIds = ['nl' => null, 'de' => null, 'fr' => null, 'en' => null];
        $defaultViewId = null;
        $enUsViewId = null;  // will be promoted to is_default after all views are inserted
        $now = date('Y-m-d H:i:s');
        $websiteSort = 0;
        $storeSort = 0;
        $viewSort = 0;

        foreach ($markets as $code => $market) {
            $domain = strtolower((string) ($market['domain'] ?? ''));
            $locale = (string) ($market['locale'] ?? 'en_US');
            $currency = (string) ($market['currency'] ?? 'USD');
            $languageCode = strtolower(str_contains((string) $code, '_') ? explode('_', (string) $code, 2)[0] : substr($locale, 0, 2));
            $pathPrefix = (string) ($market['path_prefix'] ?? '/');
            $country = strtoupper((string) ($market['country'] ?? ''));

            $websiteCode = $domain !== '' ? self::slugify(str_replace('.', '-', $domain)) : 'default';
            if (!isset($websiteIds[$websiteCode])) {
                $websiteIds[$websiteCode] = $db->table('websites')->insert([
                    'code' => $websiteCode,
                    'name' => $domain !== '' ? strtoupper($domain) : 'Default Website',
                    'is_default' => $defaultViewId === null ? 1 : 0,
                    'sort_order' => $websiteSort++,
                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $storeId = $db->table('stores')->insert([
                'website_id' => (int) $websiteIds[$websiteCode],
                'code' => $code,
                'name' => trim($languageCode . ' ' . $country) !== '' ? strtoupper($languageCode) . ' ' . $country : strtoupper($code),
                'is_default' => $defaultViewId === null ? 1 : 0,
                'sort_order' => $storeSort++,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $viewId = $db->table('store_views')->insert([
                'store_id' => $storeId,
                'code' => $code,
                'name' => strtoupper($languageCode) . ($country !== '' ? ' (' . $country . ')' : ''),
                'locale' => $locale,
                'currency_code' => $currency,
                'theme' => 'default',
                'is_default' => 0,
                'sort_order' => $viewSort++,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            if ($defaultViewId === null) {
                $defaultViewId = (int) $viewId;
            }

            // Track the first en_US view with a root path prefix — this will become the master
            if ($enUsViewId === null && $locale === 'en_US' && $pathPrefix === '/') {
                $enUsViewId = (int) $viewId;
            }

            if (isset($preferredViewIds[$languageCode]) && $preferredViewIds[$languageCode] === null && $pathPrefix === '/') {
                $preferredViewIds[$languageCode] = (int) $viewId;
            }

            if ($domain !== '') {
                $domainPathKey = $domain . '|' . $pathPrefix;
                if (!isset($domainAssigned[$domainPathKey])) {
                    $db->table('store_domains')->insert([
                        'store_view_id' => (int) $viewId,
                        'domain' => $domain,
                        'path_prefix' => $pathPrefix,
                        'is_active' => 1,
                        'is_primary' => ($pathPrefix === '/') ? 1 : 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                    $domainAssigned[$domainPathKey] = true;
                }
            }
        }

        // Set the master default: prefer the first en_US root view, fall back to the very first view
        $masterViewId = $enUsViewId ?? $defaultViewId;
        if ($masterViewId) {
            $db->table('store_views')->where('id', $masterViewId)->update(['is_default' => 1]);
        }

        foreach ($preferredViewIds as $lang => $id) {
            if ($id === null) {
                $preferredViewIds[$lang] = $defaultViewId;
            }
        }

        return [
            'default_view_id' => (int) ($masterViewId ?? $defaultViewId),
            'preferred_view_ids' => $preferredViewIds,
        ];
    }

    private static function seedMarketsNormalized(): array
    {
        $rawMarkets = require __DIR__ . '/market_definitions_seed.php';
        if (!is_array($rawMarkets)) {
            return [];
        }

        $normalized = [];
        foreach ($rawMarkets as $code => $market) {
            if (!is_array($market)) {
                continue;
            }

            $parts = parse_url((string) ($market['url'] ?? ''));
            $host = strtolower((string) ($parts['host'] ?? ''));
            $path = self::normalizeSeedPath((string) ($parts['path'] ?? '/'));
            $locale = (string) ($market['locale'] ?? 'en_US');
            $languageCode = strtolower(str_contains((string) $code, '_') ? explode('_', (string) $code, 2)[0] : substr($locale, 0, 2));

            $normalized[(string) $code] = array_merge($market, [
                'code' => (string) $code,
                'domain' => $host,
                'path_prefix' => $path,
                'language_code' => $languageCode,
            ]);
        }

        return $normalized;
    }

    private static function normalizeSeedPath(string $path): string
    {
        $path = trim($path);
        if ($path === '' || $path === '/') {
            return '/';
        }

        $path = '/' . trim($path, '/');
        return $path === '' ? '/' : $path;
    }

    private static function slugify(string $name): string
    {
        $slug = strtolower($name);
        $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug) ?? $slug;
        $slug = trim($slug, '-');
        return $slug !== '' ? $slug : 'category';
    }

    private static function seedCategoryTaxonomy(Database $db, array $storeViewIds): array
    {
        $taxonomyPath = __DIR__ . '/category_taxonomy.txt';
        $taxonomyText = @file_get_contents($taxonomyPath);
        if (!is_string($taxonomyText) || trim($taxonomyText) === '') {
            throw new RuntimeException('Category taxonomy file is missing or empty: ' . $taxonomyPath);
        }

        $slugCounts = [];
        $lastIdByDepth = [];
        $positionByParent = [];
        $categoryIdsBySlug = [];
        $now = date('Y-m-d H:i:s');

        $lines = preg_split('/\R/u', $taxonomyText) ?: [];
        foreach ($lines as $line) {
            $line = rtrim((string) $line);
            if ($line === '') {
                continue;
            }

            $depth = null;
            $name = null;

            if (preg_match('/^\d+\.\s+(.+)$/u', $line, $m)) {
                $depth = 0;
                $name = trim($m[1]);
            } elseif (preg_match('/^([\s│]*)[├└]──\s+(.+)$/u', str_replace("\t", '    ', $line), $m)) {
                $prefix = str_replace('│', ' ', $m[1]);
                $depth = intdiv(strlen($prefix), 4) + 1;
                $name = trim($m[2]);
            }

            if ($depth === null || $name === null || $name === '') {
                continue;
            }

            $parentId = $depth > 0 ? ($lastIdByDepth[$depth - 1] ?? null) : null;
            $parentKey = $parentId !== null ? (string) $parentId : 'root';
            $position = $positionByParent[$parentKey] ?? 0;
            $positionByParent[$parentKey] = $position + 1;

            $slugBase = self::slugify($name);
            $slugCount = ($slugCounts[$slugBase] ?? 0) + 1;
            $slugCounts[$slugBase] = $slugCount;
            $slug = $slugCount > 1 ? ($slugBase . '-' . $slugCount) : $slugBase;

            $categoryId = $db->table('categories')->insert([
                'parent_id' => $parentId,
                'slug' => $slug,
                'position' => $position,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($storeViewIds as $storeViewId) {
                $db->table('category_translations')->insert([
                    'category_id' => $categoryId,
                    'store_view_id' => (int) $storeViewId,
                    'name' => $name,
                    'meta_title' => $name,
                ]);
            }

            $lastIdByDepth[$depth] = $categoryId;
            foreach (array_keys($lastIdByDepth) as $d) {
                if ($d > $depth) {
                    unset($lastIdByDepth[$d]);
                }
            }

            $categoryIdsBySlug[$slug] = $categoryId;
        }

        return $categoryIdsBySlug;
    }

    private static function seedAdditionalCategoryProducts(
        Database $db,
        array $brandIds,
        array $vehicleIds,
        array $viewIds,
        array $localeViewMap,
        int $enViewId
    ): void {
        $categories = $db->table('categories')
            ->whereNotNull('parent_id')
            ->orderBy('id', 'ASC')
            ->get();

        if (empty($categories)) {
            return;
        }

        $categoryTranslations = $db->table('category_translations')
            ->where('store_view_id', $enViewId)
            ->get();
        $categoryNameById = [];
        foreach ($categoryTranslations as $row) {
            $categoryNameById[(int) $row['category_id']] = (string) $row['name'];
        }

        $brandCycle = array_values(array_filter([
            'malossi', 'polini', 'stage6', 'yasuni', 'naraku', 'piaggio', 'vespa', 'yamaha', 'honda', 'peugeot'
        ], static fn(string $slug): bool => isset($brandIds[$slug])));

        $maxProducts = min(80, count($categories));
        for ($i = 0; $i < $maxProducts; $i++) {
            $category = $categories[$i];
            $categoryId = (int) $category['id'];
            $categoryName = $categoryNameById[$categoryId] ?? (string) $category['slug'];

            $index = $i + 1;
            $sku = 'CAT-' . str_pad((string) $index, 4, '0', STR_PAD_LEFT);
            $slug = self::slugify($categoryName . '-part-' . $index);
            $price = round(14.95 + ($index * 1.85), 2);
            $salePrice = ($index % 4 === 0) ? round($price * 0.9, 2) : null;
            $brandSlug = !empty($brandCycle) ? $brandCycle[$i % count($brandCycle)] : null;

            $productId = $db->table('products')->insert([
                'sku' => $sku,
                'slug' => $slug,
                'brand_id' => $brandSlug ? ($brandIds[$brandSlug] ?? null) : null,
                'weight' => round(0.2 + (($index % 10) * 0.15), 2),
                'is_active' => 1,
                'is_featured' => $index % 7 === 0 ? 1 : 0,
                'manage_stock' => 1,
                'stock_qty' => 10 + ($index % 30),
                'low_stock_threshold' => 5,
                'oem_number' => 'OEM-' . str_pad((string) $index, 5, '0', STR_PAD_LEFT),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $seenTranslationViews = [];
            foreach ($localeViewMap as $lang => $svId) {
                if (in_array((int) $svId, $seenTranslationViews, true)) {
                    continue;
                }
                $seenTranslationViews[] = (int) $svId;
                $name = $categoryName . ' Item ' . $index;
                $description = 'Quality replacement and performance part for ' . $categoryName . '.';
                $db->table('product_translations')->insert([
                    'product_id' => $productId,
                    'store_view_id' => (int) $svId,
                    'name' => $name,
                    'short_description' => $description,
                    'description' => $description,
                    'meta_title' => $name,
                    'url_key' => $slug . '-' . $lang,
                ]);
            }

            foreach ($viewIds as $svId) {
                $db->table('product_pricing')->insert([
                    'product_id' => $productId,
                    'store_view_id' => (int) $svId,
                    'price' => $price,
                    'sale_price' => $salePrice,
                    'currency_code' => 'EUR',
                ]);
            }

            $db->table('product_categories')->insert([
                'product_id' => $productId,
                'category_id' => $categoryId,
                'position' => 0,
            ]);

            if (!empty($vehicleIds)) {
                $attachCount = 1 + ($index % 3);
                for ($v = 0; $v < $attachCount; $v++) {
                    $vehicleId = $vehicleIds[($i + $v) % count($vehicleIds)];
                    $db->table('product_vehicles')->insert([
                        'product_id' => $productId,
                        'vehicle_id' => (int) $vehicleId,
                    ]);
                }
            }
        }
    }

    private static function seedProductRichContent(Database $db, int $storeViewId): void
    {
        $products = $db->table('products')
            ->orderBy('id', 'ASC')
            ->get();

        foreach ($products as $idx => $product) {
            $productId = (int) $product['id'];
            $sku = (string) ($product['sku'] ?? ('SKU-' . $productId));
            $nameRow = $db->table('product_translations')
                ->where('product_id', $productId)
                ->where('store_view_id', $storeViewId)
                ->first();
            $name = (string) ($nameRow['name'] ?? $product['slug']);

            $imageCount = ($idx % 3 === 0) ? 3 : (($idx % 2 === 0) ? 2 : 1);
            for ($i = 1; $i <= $imageCount; $i++) {
                $db->table('product_images')->insert([
                    'product_id' => $productId,
                    'path' => 'https://dummyimage.com/960x720/e5ecff/1d4ed8&text=' . rawurlencode($sku . ' ' . ($i === 1 ? 'Main' : 'Detail ' . $i)),
                    'alt_text' => $name . ($i === 1 ? ' main view' : ' detail view ' . $i),
                    'position' => $i - 1,
                    'is_main' => $i === 1 ? 1 : 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $specRows = [
                'spec_weight' => ($product['weight'] !== null ? number_format((float) $product['weight'], 2) . ' kg' : 'N/A'),
                'spec_sku' => $sku,
            ];
            if (!empty($product['oem_number'])) {
                $specRows['spec_oem'] = (string) $product['oem_number'];
            }

            $featureList = [
                'OEM quality fit',
                'Tested for daily scooter use',
                'Performance-oriented design',
            ];

            $attributeRows = array_merge($specRows, [
                'feature_list' => implode('|', $featureList),
                'fitment_notes' => 'Check model year, engine code, and OEM reference before installation.',
                'installation_notes' => 'Professional installation is recommended for best performance and reliability.',
                'description_long' => 'This scooter part is selected for reliable fitment and everyday performance. Built for riders who need dependable quality, with a focus on compatibility and long service life.',
            ]);

            if ($idx % 3 === 0) {
                $attributeRows['doc_installation_guide'] = 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf';
                $attributeRows['doc_technical_datasheet'] = 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf';
            }

            if ($idx % 4 === 0) {
                $attributeRows['video_overview'] = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
                $attributeRows['video_installation_walkthrough'] = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
            }

            foreach ($attributeRows as $attrCode => $attrValue) {
                $db->table('product_attributes')->insert([
                    'product_id' => $productId,
                    'attribute_key' => $attrCode,
                    'attribute_value' => $attrValue,
                ]);
            }
        }
    }
}
