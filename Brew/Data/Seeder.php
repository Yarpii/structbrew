<?php

declare(strict_types=1);

namespace Brew\Data;

use Brew\Core\Auth;
use Brew\Core\Database;

class Seeder
{
    public static function run(): void
    {
        $db = Database::getInstance();

        // ─── Websites ────────────────────────────────────────
        echo "  Seeding websites...\n";
        $europeId = $db->table('websites')->insert([
            'code' => 'europe', 'name' => 'Europe', 'is_default' => 1, 'sort_order' => 0, 'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // ─── Stores ──────────────────────────────────────────
        echo "  Seeding stores...\n";
        $nlStoreId = $db->table('stores')->insert([
            'website_id' => $europeId, 'code' => 'nl', 'name' => 'Netherlands', 'is_default' => 1, 'sort_order' => 0, 'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $deStoreId = $db->table('stores')->insert([
            'website_id' => $europeId, 'code' => 'de', 'name' => 'Germany', 'is_default' => 0, 'sort_order' => 1, 'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $frStoreId = $db->table('stores')->insert([
            'website_id' => $europeId, 'code' => 'fr', 'name' => 'France', 'is_default' => 0, 'sort_order' => 2, 'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $enStoreId = $db->table('stores')->insert([
            'website_id' => $europeId, 'code' => 'en', 'name' => 'International (English)', 'is_default' => 0, 'sort_order' => 3, 'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // ─── Store Views ─────────────────────────────────────
        echo "  Seeding store views...\n";
        $nlViewId = $db->table('store_views')->insert([
            'store_id' => $nlStoreId, 'code' => 'nl_nl', 'name' => 'Dutch (Netherlands)', 'locale' => 'nl_NL',
            'currency_code' => 'EUR', 'theme' => 'default', 'is_default' => 1, 'sort_order' => 0, 'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $deViewId = $db->table('store_views')->insert([
            'store_id' => $deStoreId, 'code' => 'de_de', 'name' => 'German (Germany)', 'locale' => 'de_DE',
            'currency_code' => 'EUR', 'theme' => 'default', 'is_default' => 0, 'sort_order' => 1, 'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $frViewId = $db->table('store_views')->insert([
            'store_id' => $frStoreId, 'code' => 'fr_fr', 'name' => 'French (France)', 'locale' => 'fr_FR',
            'currency_code' => 'EUR', 'theme' => 'default', 'is_default' => 0, 'sort_order' => 2, 'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $enViewId = $db->table('store_views')->insert([
            'store_id' => $enStoreId, 'code' => 'en_us', 'name' => 'English (International)', 'locale' => 'en_US',
            'currency_code' => 'EUR', 'theme' => 'default', 'is_default' => 0, 'sort_order' => 3, 'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $viewIds = [$nlViewId, $deViewId, $frViewId, $enViewId];

        // ─── Domains ─────────────────────────────────────────
        echo "  Seeding domains...\n";
        $domains = [
            ['store_view_id' => $nlViewId, 'domain' => 'scooterdynamics.nl', 'is_primary' => 1],
            ['store_view_id' => $nlViewId, 'domain' => 'www.scooterdynamics.nl', 'is_primary' => 0],
            ['store_view_id' => $deViewId, 'domain' => 'scooterdynamics.de', 'is_primary' => 1],
            ['store_view_id' => $deViewId, 'domain' => 'www.scooterdynamics.de', 'is_primary' => 0],
            ['store_view_id' => $frViewId, 'domain' => 'scooterdynamics.fr', 'is_primary' => 1],
            ['store_view_id' => $enViewId, 'domain' => 'scooterdynamics.com', 'is_primary' => 1],
            ['store_view_id' => $enViewId, 'domain' => 'www.scooterdynamics.com', 'is_primary' => 0],
            ['store_view_id' => $enViewId, 'domain' => 'localhost', 'is_primary' => 0],
        ];
        foreach ($domains as $d) {
            $db->table('store_domains')->insert(array_merge($d, [
                'is_active' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ]));
        }

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
        $cats = [
            'motor-aandrijving' => ['en' => 'Engine & Drivetrain', 'nl' => 'Motor & Aandrijving', 'de' => 'Motor & Antrieb', 'fr' => 'Moteur & Transmission'],
            'remsysteem' => ['en' => 'Braking System', 'nl' => 'Remsysteem', 'de' => 'Bremssystem', 'fr' => "Syst\u{00E8}me de freinage"],
            'elektra' => ['en' => 'Electrical', 'nl' => 'Elektra', 'de' => 'Elektrik', 'fr' => "\u{00C9}lectrique"],
            'carrosserie' => ['en' => 'Body & Panels', 'nl' => 'Carrosserie', 'de' => 'Karosserie', 'fr' => 'Carrosserie'],
            'wielen-banden' => ['en' => 'Wheels & Tyres', 'nl' => 'Wielen & Banden', 'de' => "R\u{00E4}der & Reifen", 'fr' => 'Roues & Pneus'],
            'tuning' => ['en' => 'Tuning & Performance', 'nl' => 'Tuning & Performance', 'de' => 'Tuning & Leistung', 'fr' => 'Tuning & Performance'],
            'accessoires' => ['en' => 'Accessories', 'nl' => 'Accessoires', 'de' => "Zubeh\u{00F6}r", 'fr' => 'Accessoires'],
            'uitlaten' => ['en' => 'Exhausts', 'nl' => 'Uitlaten', 'de' => 'Auspuff', 'fr' => "\u{00C9}chappements"],
            'filters' => ['en' => 'Filters', 'nl' => 'Filters', 'de' => 'Filter', 'fr' => 'Filtres'],
        ];
        $catIds = [];
        $pos = 0;
        foreach ($cats as $slug => $names) {
            $catId = $db->table('categories')->insert([
                'slug' => $slug, 'position' => $pos++, 'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $catIds[$slug] = $catId;

            // Add translations for each store view
            $localeMap = ['nl' => $nlViewId, 'de' => $deViewId, 'fr' => $frViewId, 'en' => $enViewId];
            foreach ($localeMap as $lang => $svId) {
                $db->table('category_translations')->insert([
                    'category_id' => $catId, 'store_view_id' => $svId,
                    'name' => $names[$lang], 'meta_title' => $names[$lang],
                ]);
            }
        }

        // ─── Products ────────────────────────────────────────
        echo "  Seeding products...\n";
        $products = [
            ['sku' => 'CYL-MAL-7316', 'slug' => 'malossi-70cc-cilinder-piaggio', 'brand' => 'malossi', 'cat' => 'motor-aandrijving',
             'weight' => 1.8, 'stock' => 25, 'oem' => '3111374',
             'en' => ['name' => 'Malossi 70cc Cylinder Kit', 'desc' => 'High-performance 70cc cylinder kit for Piaggio 2-stroke scooters.'],
             'nl' => ['name' => 'Malossi 70cc Cilinderkit', 'desc' => 'High-performance 70cc cilinderkit voor Piaggio 2-takt scooters.'],
             'de' => ['name' => 'Malossi 70cc Zylinder Kit', 'desc' => 'Hochleistungs-70cc-Zylinderkit f??r Piaggio 2-Takt-Roller.'],
             'fr' => ['name' => 'Kit Cylindre Malossi 70cc', 'desc' => 'Kit cylindre haute performance 70cc pour scooters Piaggio 2 temps.'],
             'price' => 189.95, 'sale' => 169.95],
            ['sku' => 'EXH-YAS-2080', 'slug' => 'yasuni-r-uitlaat-piaggio', 'brand' => 'yasuni', 'cat' => 'uitlaten',
             'weight' => 3.2, 'stock' => 15, 'oem' => null,
             'en' => ['name' => 'Yasuni R Exhaust Piaggio', 'desc' => 'Yasuni R performance exhaust for Piaggio 2-stroke 50cc.'],
             'nl' => ['name' => 'Yasuni R Uitlaat Piaggio', 'desc' => 'Yasuni R performance uitlaat voor Piaggio 2-takt 50cc.'],
             'de' => ['name' => 'Yasuni R Auspuff Piaggio', 'desc' => 'Yasuni R Performance-Auspuff f??r Piaggio 2-Takt 50ccm.'],
             'fr' => ['name' => "Pot d'\u{00E9}chappement Yasuni R Piaggio", 'desc' => "\u{00C9}chappement performance Yasuni R pour Piaggio 2 temps 50cc."],
             'price' => 149.95, 'sale' => null],
            ['sku' => 'VAR-MAL-5113', 'slug' => 'malossi-multivar-variateur', 'brand' => 'malossi', 'cat' => 'motor-aandrijving',
             'weight' => 0.9, 'stock' => 30, 'oem' => '5111258',
             'en' => ['name' => 'Malossi Multivar Variator', 'desc' => 'Malossi Multivar 2000 variator for Piaggio/Vespa engines.'],
             'nl' => ['name' => 'Malossi Multivar Variateur', 'desc' => 'Malossi Multivar 2000 variateur voor Piaggio/Vespa motoren.'],
             'de' => ['name' => 'Malossi Multivar Variator', 'desc' => 'Malossi Multivar 2000 Variator f??r Piaggio/Vespa Motoren.'],
             'fr' => ['name' => 'Variateur Malossi Multivar', 'desc' => 'Variateur Malossi Multivar 2000 pour moteurs Piaggio/Vespa.'],
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
             'fr' => ['name' => "Filtre \u{00E0} air Polini", 'desc' => "Filtre \u{00E0} air performance pour Piaggio 2 temps."],
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
             'nl' => ['name' => 'AGV K5 S Helm - Mat Zwart', 'desc' => 'Integraalhelm met ge??ntegreerd zonnescherm. ECE gekeurd.'],
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
            foreach ($localeViewMap as $lang => $svId) {
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
            if (isset($catIds[$p['cat']])) {
                $db->table('product_categories')->insert([
                    'product_id' => $productId, 'category_id' => $catIds[$p['cat']], 'position' => 0,
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

        // ─── Admin Role & User ───────────────────────────────
        echo "  Seeding admin user...\n";
        $roleId = $db->table('admin_roles')->insert([
            'name' => 'Super Admin', 'permissions' => json_encode(['*']),
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $db->table('admin_users')->insert([
            'role_id' => $roleId, 'email' => 'admin@structbrew.com',
            'password_hash' => Auth::hashPassword('admin123'),
            'first_name' => 'Admin', 'last_name' => 'User',
            'is_active' => 1, 'is_superadmin' => 1,
            'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'),
        ]);

        echo "  Done! Admin login: admin@structbrew.com / admin123\n";
    }
}
