<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Centralized SEO tag generation for international multi-domain setup.
 *
 * Generates: hreflang tags, canonical URLs, meta description, Open Graph tags,
 * JSON-LD structured data, and HTML lang attribute.
 *
 * Usage in views: echo Seo::head() inside <head> to render all SEO meta tags.
 * Controllers set page-specific data via Seo::title(), Seo::description(), etc.
 */
final class Seo
{
    private static string $title = '';
    private static string $description = '';
    private static string $ogImage = '';
    private static string $ogType = 'website';
    private static string $canonicalUrl = '';
    private static array $jsonLd = [];

    /**
     * Set page title (without site name suffix).
     */
    public static function title(string $title): void
    {
        self::$title = $title;
    }

    /**
     * Set meta description.
     */
    public static function description(string $description): void
    {
        self::$description = $description;
    }

    /**
     * Set Open Graph image URL.
     */
    public static function image(string $url): void
    {
        self::$ogImage = $url;
    }

    /**
     * Set Open Graph type (default: website).
     */
    public static function type(string $type): void
    {
        self::$ogType = $type;
    }

    /**
     * Override canonical URL (by default, auto-generated from current request).
     */
    public static function canonical(string $url): void
    {
        self::$canonicalUrl = $url;
    }

    /**
     * Add custom JSON-LD structured data block.
     */
    public static function addJsonLd(array $data): void
    {
        self::$jsonLd[] = $data;
    }

    /**
     * Get the BCP 47 language tag for the current store view (e.g., "nl-BE").
     */
    public static function htmlLang(): string
    {
        $storeView = StoreResolver::storeView();
        if ($storeView && !empty($storeView['hreflang'])) {
            return $storeView['hreflang'];
        }

        // Fallback from locale (nl_NL → nl-NL)
        $locale = StoreResolver::locale();
        return str_replace('_', '-', $locale);
    }

    /**
     * Render all SEO meta tags for the <head> section.
     */
    public static function head(): string
    {
        $tags = [];

        // Canonical URL
        $tags[] = self::renderCanonical();

        // Meta description
        if (self::$description !== '') {
            $desc = htmlspecialchars(self::$description, ENT_QUOTES, 'UTF-8');
            $tags[] = '<meta name="description" content="' . $desc . '">';
        }

        // hreflang tags for all alternate store views
        $tags[] = self::renderHreflangTags();

        // Open Graph tags
        $tags[] = self::renderOpenGraph();

        // JSON-LD structured data
        $tags[] = self::renderJsonLd();

        return implode("\n    ", array_filter($tags));
    }

    /**
     * Generate canonical URL for the current page.
     */
    private static function renderCanonical(): string
    {
        $url = self::$canonicalUrl;
        if ($url === '') {
            $url = self::currentUrl();
        }
        return '<link rel="canonical" href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Generate hreflang link tags for all language/country alternatives.
     *
     * For each page, we output an hreflang tag for every store view that shares
     * the same base domain group + an x-default pointing to the .com fallback.
     */
    private static function renderHreflangTags(): string
    {
        try {
            $db = Database::getInstance();
        } catch (\Throwable $e) {
            return '';
        }

        // Get all active store views with their domains
        $storeViews = $db->table('store_views')
            ->where('is_active', 1)
            ->get();

        if (empty($storeViews)) {
            return '';
        }

        // Build a map of store_view_id → primary domain info
        $domains = $db->table('store_domains')
            ->where('is_active', 1)
            ->get();

        $domainMap = [];
        foreach ($domains as $d) {
            $viewId = (int) $d['store_view_id'];
            // Prefer exact match; domains are already associated 1:1 via path_prefix
            $domainMap[$viewId] = $d;
        }

        $currentPath = self::currentPagePath();
        $tags = [];

        foreach ($storeViews as $sv) {
            $viewId = (int) $sv['id'];
            $hreflang = $sv['hreflang'] ?? str_replace('_', '-', $sv['locale'] ?? 'en-US');

            if (!isset($domainMap[$viewId])) {
                continue;
            }

            $d = $domainMap[$viewId];
            $domain = $d['domain'];
            $pathPrefix = $d['path_prefix'] ?? '/';

            // Build full URL: https://domain + path_prefix + current page path
            $href = 'https://' . $domain . rtrim($pathPrefix, '/') . $currentPath;

            $tags[] = '<link rel="alternate" hreflang="' . htmlspecialchars($hreflang, ENT_QUOTES, 'UTF-8')
                . '" href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '">';
        }

        // x-default: points to the .com international version
        $xDefaultUrl = 'https://scooterdynamics.com' . $currentPath;
        $tags[] = '<link rel="alternate" hreflang="x-default" href="'
            . htmlspecialchars($xDefaultUrl, ENT_QUOTES, 'UTF-8') . '">';

        return implode("\n    ", $tags);
    }

    /**
     * Render Open Graph meta tags.
     */
    private static function renderOpenGraph(): string
    {
        $tags = [];
        $siteName = 'Scooter Dynamics';
        $title = self::$title !== '' ? self::$title : $siteName;
        $url = self::$canonicalUrl !== '' ? self::$canonicalUrl : self::currentUrl();

        // Locale in OG format (nl_BE, fr_FR)
        $ogLocale = StoreResolver::locale();

        $tags[] = '<meta property="og:site_name" content="' . htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') . '">';
        $tags[] = '<meta property="og:type" content="' . htmlspecialchars(self::$ogType, ENT_QUOTES, 'UTF-8') . '">';
        $tags[] = '<meta property="og:title" content="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '">';
        $tags[] = '<meta property="og:url" content="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">';
        $tags[] = '<meta property="og:locale" content="' . htmlspecialchars($ogLocale, ENT_QUOTES, 'UTF-8') . '">';

        if (self::$description !== '') {
            $tags[] = '<meta property="og:description" content="' . htmlspecialchars(self::$description, ENT_QUOTES, 'UTF-8') . '">';
        }

        if (self::$ogImage !== '') {
            $tags[] = '<meta property="og:image" content="' . htmlspecialchars(self::$ogImage, ENT_QUOTES, 'UTF-8') . '">';
        }

        // Twitter Card
        $tags[] = '<meta name="twitter:card" content="summary_large_image">';
        $tags[] = '<meta name="twitter:title" content="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '">';
        if (self::$description !== '') {
            $tags[] = '<meta name="twitter:description" content="' . htmlspecialchars(self::$description, ENT_QUOTES, 'UTF-8') . '">';
        }
        if (self::$ogImage !== '') {
            $tags[] = '<meta name="twitter:image" content="' . htmlspecialchars(self::$ogImage, ENT_QUOTES, 'UTF-8') . '">';
        }

        return implode("\n    ", $tags);
    }

    /**
     * Render JSON-LD structured data blocks.
     * Always includes Organization schema; page-specific schemas added via addJsonLd().
     */
    private static function renderJsonLd(): string
    {
        $blocks = [];

        // Organization schema (always present)
        $blocks[] = self::organizationSchema();

        // Merge any page-specific JSON-LD
        foreach (self::$jsonLd as $data) {
            $blocks[] = $data;
        }

        $output = [];
        foreach ($blocks as $block) {
            $json = json_encode($block, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
            $output[] = '<script type="application/ld+json">' . $json . '</script>';
        }

        return implode("\n    ", $output);
    }

    /**
     * Organization schema for the current store.
     */
    private static function organizationSchema(): array
    {
        $currentHost = $_SERVER['HTTP_HOST'] ?? 'scooterdynamics.com';
        $currentHost = preg_replace('/:\d+$/', '', $currentHost);

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'Scooter Dynamics',
            'url' => 'https://' . $currentHost . '/',
            'logo' => 'https://' . $currentHost . '/assets/images/logo.png',
        ];
    }

    /**
     * Generate a Product JSON-LD schema from product data.
     * Call from product detail pages.
     */
    public static function productSchema(array $product, string $url): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $product['name'] ?? '',
            'url' => $url,
        ];

        if (!empty($product['description'])) {
            $schema['description'] = strip_tags($product['description']);
        }

        if (!empty($product['sku'])) {
            $schema['sku'] = $product['sku'];
        }

        if (!empty($product['image'])) {
            $schema['image'] = $product['image'];
        }

        // Offer (price)
        $price = $product['sale_price'] ?? $product['price'] ?? null;
        if ($price !== null) {
            $schema['offers'] = [
                '@type' => 'Offer',
                'price' => (float) $price,
                'priceCurrency' => StoreResolver::currency(),
                'availability' => 'https://schema.org/' .
                    (($product['stock_qty'] ?? 0) > 0 ? 'InStock' : 'OutOfStock'),
                'url' => $url,
            ];
        }

        return $schema;
    }

    /**
     * Generate a BreadcrumbList JSON-LD schema.
     */
    public static function breadcrumbSchema(array $items): array
    {
        $list = [];
        foreach ($items as $i => $item) {
            $list[] = [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $item['name'],
                'item' => $item['url'] ?? null,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $list,
        ];
    }

    /**
     * Get the current full URL (scheme + host + URI).
     */
    private static function currentUrl(): string
    {
        $scheme = self::isSecure() ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $host = preg_replace('/:\d+$/', '', $host);
        $uri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
        return $scheme . '://' . $host . $uri;
    }

    /**
     * Get just the page path portion (after any path_prefix).
     * e.g., on scooterdynamics.be/fr/shop/product-x → /shop/product-x
     */
    private static function currentPagePath(): string
    {
        $uri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
        $prefix = StoreResolver::pathPrefix();

        if ($prefix !== '/' && str_starts_with($uri, rtrim($prefix, '/'))) {
            $uri = substr($uri, strlen(rtrim($prefix, '/')));
            if ($uri === '' || $uri === false) {
                $uri = '/';
            }
        }

        return $uri;
    }

    private static function isSecure(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ($_SERVER['SERVER_PORT'] ?? 0) == 443
            || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
    }
}
