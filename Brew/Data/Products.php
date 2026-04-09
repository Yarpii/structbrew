<?php
declare(strict_types=1);
namespace Brew\Data;

final class Products
{
    private static array $categories = [
        'electronics' => 'Electronics',
        'audio'       => 'Audio',
        'accessories' => 'Accessories',
        'wearables'   => 'Wearables',
        'home'        => 'Home & Office',
        'gaming'      => 'Gaming',
    ];

    private static array $products = [
        [
            'id' => 1, 'slug' => 'wireless-noise-cancelling-headphones',
            'name' => 'Wireless Noise-Cancelling Headphones',
            'price' => 249.99, 'sale_price' => 199.99, 'category' => 'audio',
            'description' => 'Premium over-ear headphones with active noise cancellation, 30-hour battery life, and crystal-clear audio. Features adaptive sound control and multipoint connection for seamless switching between devices.',
            'features' => ['Active Noise Cancellation', '30h Battery Life', 'Bluetooth 5.3', 'Multipoint Connection', 'Hi-Res Audio', 'Foldable Design'],
            'sku' => 'AUD-WH-001', 'in_stock' => true, 'rating' => 4.8, 'reviews' => 342, 'badge' => 'Sale',
        ],
        [
            'id' => 2, 'slug' => 'smart-fitness-watch-pro',
            'name' => 'Smart Fitness Watch Pro',
            'price' => 329.00, 'sale_price' => null, 'category' => 'wearables',
            'description' => 'Advanced fitness tracking with GPS, heart rate monitoring, blood oxygen sensor, and 5ATM water resistance. Track over 100 workout modes with detailed analytics on your wrist.',
            'features' => ['GPS Tracking', 'Heart Rate Monitor', 'Blood Oxygen Sensor', '5ATM Water Resistant', '14-Day Battery', 'AMOLED Display'],
            'sku' => 'WR-SW-002', 'in_stock' => true, 'rating' => 4.6, 'reviews' => 189, 'badge' => 'New',
        ],
        [
            'id' => 3, 'slug' => 'mechanical-keyboard-rgb',
            'name' => 'Mechanical Keyboard RGB',
            'price' => 149.99, 'sale_price' => null, 'category' => 'gaming',
            'description' => 'Hot-swappable mechanical keyboard with per-key RGB lighting, gasket-mount design, and premium PBT keycaps. Quiet yet tactile switches for both gaming and typing.',
            'features' => ['Hot-Swappable Switches', 'Per-Key RGB', 'Gasket Mount', 'PBT Keycaps', 'USB-C', 'N-Key Rollover'],
            'sku' => 'GM-KB-003', 'in_stock' => true, 'rating' => 4.7, 'reviews' => 256, 'badge' => null,
        ],
        [
            'id' => 4, 'slug' => 'portable-bluetooth-speaker',
            'name' => 'Portable Bluetooth Speaker',
            'price' => 79.99, 'sale_price' => 59.99, 'category' => 'audio',
            'description' => 'Compact waterproof speaker with 360-degree sound, deep bass, and 20-hour playtime. Perfect for outdoor adventures with IP67 dust and water resistance.',
            'features' => ['360-Degree Sound', 'IP67 Waterproof', '20h Playtime', 'USB-C Charging', 'Stereo Pairing', 'Built-in Mic'],
            'sku' => 'AUD-BS-004', 'in_stock' => true, 'rating' => 4.5, 'reviews' => 478, 'badge' => 'Sale',
        ],
        [
            'id' => 5, 'slug' => 'usb-c-hub-multiport',
            'name' => 'USB-C Hub 8-in-1 Multiport',
            'price' => 54.99, 'sale_price' => null, 'category' => 'accessories',
            'description' => 'All-in-one USB-C hub with HDMI 4K output, USB 3.0 ports, SD card reader, ethernet, and 100W power delivery pass-through. Aluminum body with compact design.',
            'features' => ['HDMI 4K@60Hz', '3x USB 3.0', 'SD/MicroSD Reader', 'Gigabit Ethernet', '100W PD Pass-Through', 'Aluminum Body'],
            'sku' => 'ACC-HB-005', 'in_stock' => true, 'rating' => 4.4, 'reviews' => 167, 'badge' => null,
        ],
        [
            'id' => 6, 'slug' => 'wireless-ergonomic-mouse',
            'name' => 'Wireless Ergonomic Mouse',
            'price' => 69.99, 'sale_price' => null, 'category' => 'accessories',
            'description' => 'Ergonomically designed vertical mouse that reduces wrist strain. Features adjustable DPI, silent clicks, and dual-mode connectivity via Bluetooth and USB receiver.',
            'features' => ['Ergonomic Vertical Design', 'Silent Clicks', 'Adjustable DPI', 'Bluetooth + USB', '90-Day Battery', '6 Buttons'],
            'sku' => 'ACC-MS-006', 'in_stock' => true, 'rating' => 4.3, 'reviews' => 98, 'badge' => null,
        ],
        [
            'id' => 7, 'slug' => '4k-webcam-autofocus',
            'name' => '4K Webcam with Autofocus',
            'price' => 129.99, 'sale_price' => 99.99, 'category' => 'electronics',
            'description' => 'Ultra HD 4K webcam with AI-powered autofocus, automatic light correction, and dual noise-cancelling microphones. Perfect for video calls, streaming, and content creation.',
            'features' => ['4K Ultra HD', 'AI Autofocus', 'Auto Light Correction', 'Dual Microphones', 'Privacy Cover', 'Universal Mount'],
            'sku' => 'EL-WC-007', 'in_stock' => true, 'rating' => 4.6, 'reviews' => 134, 'badge' => 'Sale',
        ],
        [
            'id' => 8, 'slug' => 'smart-led-desk-lamp',
            'name' => 'Smart LED Desk Lamp',
            'price' => 89.99, 'sale_price' => null, 'category' => 'home',
            'description' => 'Intelligent desk lamp with adjustable color temperature, brightness control, and wireless charging base. Features a sleek aluminum design with touch controls and app connectivity.',
            'features' => ['Adjustable Color Temp', 'Wireless Charging Base', 'Touch Controls', 'App Control', 'Memory Function', 'Eye-Care Technology'],
            'sku' => 'HM-DL-008', 'in_stock' => true, 'rating' => 4.5, 'reviews' => 76, 'badge' => null,
        ],
        [
            'id' => 9, 'slug' => 'wireless-earbuds-anc',
            'name' => 'Wireless Earbuds ANC',
            'price' => 159.99, 'sale_price' => 129.99, 'category' => 'audio',
            'description' => 'True wireless earbuds with hybrid active noise cancellation, transparency mode, and premium sound quality. IPX5 water resistant with 8-hour battery plus charging case.',
            'features' => ['Hybrid ANC', 'Transparency Mode', 'IPX5 Water Resistant', '8h + 24h Battery', 'Wireless Charging Case', 'Multipoint'],
            'sku' => 'AUD-EB-009', 'in_stock' => true, 'rating' => 4.7, 'reviews' => 521, 'badge' => 'Bestseller',
        ],
        [
            'id' => 10, 'slug' => 'gaming-mouse-wireless',
            'name' => 'Gaming Mouse Wireless',
            'price' => 119.99, 'sale_price' => null, 'category' => 'gaming',
            'description' => 'Ultra-lightweight wireless gaming mouse with 25K DPI optical sensor, 70-hour battery life, and customizable RGB lighting. Weighs only 63g for fast, precise gameplay.',
            'features' => ['25K DPI Sensor', '70h Battery', '63g Ultra-Light', 'RGB Lighting', '5 Programmable Buttons', '1ms Response Time'],
            'sku' => 'GM-MS-010', 'in_stock' => true, 'rating' => 4.8, 'reviews' => 312, 'badge' => null,
        ],
        [
            'id' => 11, 'slug' => 'laptop-backpack-anti-theft',
            'name' => 'Laptop Backpack Anti-Theft',
            'price' => 79.99, 'sale_price' => null, 'category' => 'accessories',
            'description' => 'Water-resistant laptop backpack with anti-theft design, hidden zippers, USB charging port, and padded compartment for up to 16" laptops. Multiple organizer pockets.',
            'features' => ['Anti-Theft Design', 'USB Charging Port', 'Water Resistant', 'Fits 16" Laptop', 'Hidden Zippers', 'Padded Straps'],
            'sku' => 'ACC-BP-011', 'in_stock' => true, 'rating' => 4.4, 'reviews' => 203, 'badge' => null,
        ],
        [
            'id' => 12, 'slug' => 'smart-power-strip',
            'name' => 'Smart Power Strip WiFi',
            'price' => 34.99, 'sale_price' => 27.99, 'category' => 'home',
            'description' => 'WiFi-enabled smart power strip with 4 individually controlled outlets and 3 USB ports. Voice control compatible with Alexa and Google Home. Surge protection included.',
            'features' => ['4 Smart Outlets', '3 USB Ports', 'Voice Control', 'Surge Protection', 'Timer/Schedule', 'Energy Monitoring'],
            'sku' => 'HM-PS-012', 'in_stock' => true, 'rating' => 4.3, 'reviews' => 89, 'badge' => 'Sale',
        ],
        [
            'id' => 13, 'slug' => 'curved-ultrawide-monitor',
            'name' => 'Curved Ultrawide Monitor 34"',
            'price' => 449.99, 'sale_price' => null, 'category' => 'electronics',
            'description' => '34-inch curved ultrawide WQHD monitor with 144Hz refresh rate, 1ms response time, and HDR400. Immersive 21:9 aspect ratio for gaming and productivity.',
            'features' => ['34" WQHD 3440x1440', '144Hz Refresh Rate', '1ms Response Time', 'HDR400', 'FreeSync Premium', 'USB-C 90W PD'],
            'sku' => 'EL-MN-013', 'in_stock' => true, 'rating' => 4.7, 'reviews' => 156, 'badge' => 'Popular',
        ],
        [
            'id' => 14, 'slug' => 'fitness-tracker-band',
            'name' => 'Fitness Tracker Band',
            'price' => 49.99, 'sale_price' => 39.99, 'category' => 'wearables',
            'description' => 'Slim fitness tracker with heart rate monitoring, sleep tracking, and step counter. Water resistant with a bright AMOLED display and 10-day battery life.',
            'features' => ['Heart Rate Monitor', 'Sleep Tracking', 'AMOLED Display', '10-Day Battery', '5ATM Waterproof', '100+ Watch Faces'],
            'sku' => 'WR-FT-014', 'in_stock' => true, 'rating' => 4.2, 'reviews' => 634, 'badge' => 'Sale',
        ],
        [
            'id' => 15, 'slug' => 'wireless-charging-pad',
            'name' => 'Wireless Charging Pad 3-in-1',
            'price' => 44.99, 'sale_price' => null, 'category' => 'accessories',
            'description' => 'Sleek 3-in-1 wireless charging station for phone, earbuds, and smartwatch. Supports fast charging up to 15W with LED indicator and anti-slip surface.',
            'features' => ['3-in-1 Charging', '15W Fast Charge', 'LED Indicator', 'Anti-Slip Surface', 'Case Compatible', 'Compact Design'],
            'sku' => 'ACC-WC-015', 'in_stock' => true, 'rating' => 4.5, 'reviews' => 187, 'badge' => null,
        ],
        [
            'id' => 16, 'slug' => 'gaming-headset-surround',
            'name' => 'Gaming Headset 7.1 Surround',
            'price' => 89.99, 'sale_price' => null, 'category' => 'gaming',
            'description' => 'Immersive 7.1 virtual surround sound gaming headset with detachable noise-cancelling microphone, memory foam ear cushions, and RGB lighting. Works with PC, PS5, and Switch.',
            'features' => ['7.1 Surround Sound', 'Detachable Mic', 'Memory Foam Cushions', 'RGB Lighting', 'Multi-Platform', 'Inline Controls'],
            'sku' => 'GM-HS-016', 'in_stock' => false, 'rating' => 4.6, 'reviews' => 245, 'badge' => 'Out of Stock',
        ],
    ];

    public static function all(): array
    {
        return self::$products;
    }

    public static function find(int $id): ?array
    {
        foreach (self::$products as $product) {
            if ($product['id'] === $id) return $product;
        }
        return null;
    }

    public static function findBySlug(string $slug): ?array
    {
        foreach (self::$products as $product) {
            if ($product['slug'] === $slug) return $product;
        }
        return null;
    }

    public static function byCategory(string $category): array
    {
        return array_values(array_filter(self::$products, fn($p) => $p['category'] === $category));
    }

    public static function featured(int $limit = 8): array
    {
        return array_slice(self::$products, 0, $limit);
    }

    public static function onSale(): array
    {
        return array_values(array_filter(self::$products, fn($p) => $p['sale_price'] !== null));
    }

    public static function categories(): array
    {
        return self::$categories;
    }

    public static function search(string $query): array
    {
        $q = strtolower(trim($query));
        if ($q === '') return self::$products;
        return array_values(array_filter(self::$products, function ($p) use ($q) {
            return str_contains(strtolower($p['name']), $q)
                || str_contains(strtolower($p['description']), $q)
                || str_contains(strtolower($p['category']), $q);
        }));
    }

    public static function related(int $productId, int $limit = 4): array
    {
        $product = self::find($productId);
        if (!$product) return [];
        $related = array_filter(self::$products, fn($p) => $p['category'] === $product['category'] && $p['id'] !== $productId);
        return array_slice(array_values($related), 0, $limit);
    }

    public static function newArrivals(int $limit = 4): array
    {
        $products = self::$products;
        usort($products, fn($a, $b) => $b['id'] - $a['id']);
        return array_slice($products, 0, $limit);
    }

    public static function trending(int $limit = 4): array
    {
        $products = self::$products;
        usort($products, fn($a, $b) => $b['reviews'] - $a['reviews']);
        return array_slice($products, 0, $limit);
    }
}
