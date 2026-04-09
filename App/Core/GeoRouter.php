<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Geo-routing service: redirects visitors to their country-specific domain
 * based on Cloudflare CF-IPCountry header or Accept-Language fallback.
 *
 * Each country maps to its primary domain and default language path.
 * Multi-language domains route to the correct path based on browser language.
 *
 * Users can override by setting a 'geo_store_override' cookie (set when they
 * manually navigate to a different domain).
 */
final class GeoRouter
{
    /** Cookie name to mark that the user manually chose a store */
    private const OVERRIDE_COOKIE = 'geo_store_override';

    /** Cookie lifetime: 30 days */
    private const OVERRIDE_TTL = 2592000;

    /**
     * Country code (ISO 3166-1 alpha-2) → primary domain URL.
     * This is the default landing URL for visitors from each country.
     * Derived from market_definitions_seed.php.
     */
    private const COUNTRY_DOMAINS = [
        // Europe
        'AT' => 'https://scooterdynamics.at/',
        'BE' => 'https://scooterdynamics.be/',
        'DE' => 'https://scooterdynamics.de/',
        'NL' => 'https://scooterdynamics.nl/',
        'FR' => 'https://scooterdynamics.fr/',
        'CH' => 'https://scooterdynamics.ch/',
        'LI' => 'https://scooterdynamics.li/',
        'IT' => 'https://scooterdynamics.it/',
        'ES' => 'https://scooterdynamics.es/',
        'PT' => 'https://scooterdynamics.pt/',
        'GR' => 'https://scooterdynamics.gr/',
        'ME' => 'https://scooterdynamics.me/',
        'SE' => 'https://scooterdynamics.se/',
        'DK' => 'https://scooterdynamics.dk/',
        'FI' => 'https://scooterdynamics.fi/',
        'LT' => 'https://scooterdynamics.lt/',
        'PL' => 'https://scooterdynamics.pl/',
        'CZ' => 'https://scooterdynamics.cz/',
        'SK' => 'https://scooterdynamics.sk/',
        'HU' => 'https://scooterdynamics.hu/',
        'SI' => 'https://scooterdynamics.si/',
        'RO' => 'https://scooterdynamics.ro/',
        'LV' => 'https://scooterdynamics.lv/',
        'EE' => 'https://scooterdynamics.ee/',
        'TR' => 'https://scooterdynamics.tr/',
        'GB' => 'https://scooterdynamics.co.uk/',
        'IE' => 'https://scooterdynamics.eu/ie/',
        'NO' => 'https://scooterdynamics.eu/no/',
        'IS' => 'https://scooterdynamics.eu/is/',
        'RS' => 'https://scooterdynamics.eu/rs/',
        'BA' => 'https://scooterdynamics.eu/ba/',
        'MK' => 'https://scooterdynamics.eu/mk/',
        'AL' => 'https://scooterdynamics.eu/al/',
        'UA' => 'https://scooterdynamics.eu/ua/',
        'MD' => 'https://scooterdynamics.eu/md/',
        'BG' => 'https://scooterdynamics.eu/bg/',
        'HR' => 'https://scooterdynamics.eu/hr/',
        'CY' => 'https://scooterdynamics.eu/cy/',
        'MT' => 'https://scooterdynamics.eu/mt/',

        // Americas
        'US' => 'https://scooterdynamics.us/',
        'CA' => 'https://scooterdynamics.com/ca/',
        'MX' => 'https://scooterdynamics.mx/',
        'BR' => 'https://scooterdynamics.com/br/',
        'AR' => 'https://scooterdynamics.com/ar/',
        'CO' => 'https://scooterdynamics.com/co/',
        'CL' => 'https://scooterdynamics.cl/',
        'PE' => 'https://scooterdynamics.com/pe/',
        'EC' => 'https://scooterdynamics.com/ec/',
        'VE' => 'https://scooterdynamics.com/ve/',
        'BO' => 'https://scooterdynamics.com/bo/',
        'PY' => 'https://scooterdynamics.com/py/',
        'UY' => 'https://scooterdynamics.com/uy/',
        'JM' => 'https://scooterdynamics.com/jm/',
        'CU' => 'https://scooterdynamics.com/cu/',
        'DO' => 'https://scooterdynamics.com/do/',
        'TT' => 'https://scooterdynamics.com/tt/',

        // Asia
        'JP' => 'https://scooterdynamics.asia/jp/',
        'KR' => 'https://scooterdynamics.asia/kr/',
        'CN' => 'https://scooterdynamics.asia/cn/',
        'HK' => 'https://scooterdynamics.asia/hk/',
        'TW' => 'https://scooterdynamics.tw/',
        'TH' => 'https://scooterdynamics.asia/th/',
        'VN' => 'https://scooterdynamics.asia/vn/',
        'SG' => 'https://scooterdynamics.asia/sg/',
        'PH' => 'https://scooterdynamics.asia/ph/',
        'ID' => 'https://scooterdynamics.asia/id/',
        'MY' => 'https://scooterdynamics.my/',
        'IN' => 'https://scooterdynamics.in/',
        'PK' => 'https://scooterdynamics.asia/pk/',
        'BD' => 'https://scooterdynamics.asia/bd/',
        'LK' => 'https://scooterdynamics.asia/lk/',
        'NP' => 'https://scooterdynamics.asia/np/',
        'BT' => 'https://scooterdynamics.asia/bt/',
        'MV' => 'https://scooterdynamics.asia/mv/',
        'AF' => 'https://scooterdynamics.asia/af/',
        'MN' => 'https://scooterdynamics.asia/mn/',
        'KH' => 'https://scooterdynamics.asia/kh/',
        'KZ' => 'https://scooterdynamics.asia/kz/',
        'UZ' => 'https://scooterdynamics.asia/uz/',
        'TM' => 'https://scooterdynamics.asia/tm/',
        'KG' => 'https://scooterdynamics.asia/kg/',
        'TJ' => 'https://scooterdynamics.asia/tj/',
        'AM' => 'https://scooterdynamics.asia/am/',
        'GE' => 'https://scooterdynamics.asia/ge/',
        'AZ' => 'https://scooterdynamics.asia/az/',

        // Middle East
        'AE' => 'https://scooterdynamics.com/ae/',
        'SA' => 'https://scooterdynamics.com/sa/',
        'IL' => 'https://scooterdynamics.com/il/',
        'JO' => 'https://scooterdynamics.com/jo/',
        'LB' => 'https://scooterdynamics.com/lb/',

        // Africa
        'ZA' => 'https://scooterdynamics.com/za/',
        'NG' => 'https://scooterdynamics.com/ng/',
        'EG' => 'https://scooterdynamics.com/eg/',
        'KE' => 'https://scooterdynamics.com/ke/',
        'MA' => 'https://scooterdynamics.com/ma/',
        'ET' => 'https://scooterdynamics.com/et/',
        'GH' => 'https://scooterdynamics.com/gh/',

        // Oceania
        'AU' => 'https://scooterdynamics.com/au/',
        'NZ' => 'https://scooterdynamics.nz/',
    ];

    /**
     * For multi-language domains: maps domain host → language code → path prefix.
     * When a visitor lands on a multi-language domain, their browser language
     * determines which path_prefix they get redirected to.
     *
     * Only domains with more than one language are listed here.
     */
    private const DOMAIN_LANGUAGES = [
        'scooterdynamics.be'     => ['nl' => '/', 'fr' => '/fr/'],
        'scooterdynamics.ch'     => ['de' => '/', 'fr' => '/fr/', 'it' => '/it/'],
        'scooterdynamics.es'     => ['es' => '/', 'ca' => '/ca/', 'gl' => '/gl/'],
        'scooterdynamics.se'     => ['sv' => '/', 'fi' => '/fi/'],
        'scooterdynamics.cz'     => ['cs' => '/', 'it' => '/it/', 'fr' => '/fr/'],
        'scooterdynamics.ro'     => ['ro' => '/', 'hu' => '/hu/'],
        'scooterdynamics.co.uk'  => ['en' => '/', 'cy' => '/cy/', 'gd' => '/gd/'],
        'scooterdynamics.us'     => ['en' => '/', 'es' => '/es/'],
        'scooterdynamics.in'     => ['en' => '/', 'hi' => '/hi/', 'ta' => '/ta/', 'bn' => '/bn/'],
        'scooterdynamics.my'     => ['ms' => '/', 'en' => '/en/'],
        'scooterdynamics.tw'     => ['zh' => '/', 'en' => '/en/'],
        'scooterdynamics.nz'     => ['en' => '/', 'mi' => '/mi/'],
    ];

    /**
     * Shared domains that serve multiple countries via path prefix.
     * These should NOT be geo-redirected away from (the path already identifies the country).
     */
    private const SHARED_DOMAINS = [
        'scooterdynamics.com',
        'scooterdynamics.eu',
        'scooterdynamics.asia',
    ];

    /**
     * Run geo-routing check. Returns a redirect URL if the visitor should be
     * redirected, or null if they're already on the correct domain.
     *
     * Skips: admin routes, API routes, asset routes, POST requests, AJAX, bots.
     */
    public static function check(): ?string
    {
        // Don't redirect if user has override cookie
        if (self::hasOverrideCookie()) {
            return null;
        }

        // Don't redirect non-GET requests (forms, API calls)
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method !== 'GET') {
            return null;
        }

        // Don't redirect AJAX requests
        if (self::isAjax()) {
            return null;
        }

        // Don't redirect search engine bots — they must crawl each domain independently
        if (self::isBot()) {
            return null;
        }

        // Don't redirect admin, API, setup, or asset requests
        $uri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
        if (self::isExcludedPath((string) $uri)) {
            return null;
        }

        // Detect visitor country
        $country = self::detectCountry();
        if ($country === null) {
            return null;
        }

        // Get the target URL for this country
        $targetUrl = self::COUNTRY_DOMAINS[$country] ?? null;
        if ($targetUrl === null) {
            return null;
        }

        $targetParts = parse_url($targetUrl);
        $targetHost = strtolower($targetParts['host'] ?? '');
        $targetPath = $targetParts['path'] ?? '/';

        $currentHost = strtolower($_SERVER['HTTP_HOST'] ?? '');
        $currentHost = preg_replace('/:\d+$/', '', $currentHost); // strip port

        // If already on a shared domain (.com, .eu, .asia), check if the path matches
        if (in_array($currentHost, self::SHARED_DOMAINS, true)) {
            $currentUri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
            // If the target is also on this shared domain, check path prefix
            if ($targetHost === $currentHost && str_starts_with($currentUri, rtrim($targetPath, '/'))) {
                return null; // Already on the correct path
            }
        }

        // If already on the correct domain
        if ($targetHost === $currentHost) {
            // Check language preference for multi-language domains
            $langRedirect = self::checkLanguageRedirect($currentHost);
            if ($langRedirect !== null) {
                return $langRedirect;
            }
            return null; // Already on correct domain, correct language
        }

        // Build redirect URL: target domain + language-aware path
        $redirectUrl = rtrim($targetUrl, '/');

        // For multi-language target domains, check browser language
        if (isset(self::DOMAIN_LANGUAGES[$targetHost])) {
            $browserLang = self::detectBrowserLanguage();
            $languages = self::DOMAIN_LANGUAGES[$targetHost];

            if ($browserLang !== null && isset($languages[$browserLang])) {
                $langPath = $languages[$browserLang];
                $redirectUrl = 'https://' . $targetHost . $langPath;
            }
        }

        return $redirectUrl;
    }

    /**
     * Set the override cookie so the user stays on their chosen domain.
     * Call this when the visitor manually navigates to a specific store.
     */
    public static function setOverrideCookie(): void
    {
        $secure = self::isSecure();
        setcookie(self::OVERRIDE_COOKIE, '1', [
            'expires'  => time() + self::OVERRIDE_TTL,
            'path'     => '/',
            'secure'   => $secure,
            'httponly'  => true,
            'samesite' => 'Lax',
        ]);
    }

    /**
     * Clear the override cookie (e.g., when user clicks "change country").
     */
    public static function clearOverrideCookie(): void
    {
        $secure = self::isSecure();
        setcookie(self::OVERRIDE_COOKIE, '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'secure'   => $secure,
            'httponly'  => true,
            'samesite' => 'Lax',
        ]);
    }

    /**
     * Detect visitor's country using Cloudflare header or fallback methods.
     * Returns ISO 3166-1 alpha-2 code (uppercase) or null.
     */
    private static function detectCountry(): ?string
    {
        // Primary: Cloudflare CF-IPCountry header (most reliable, no extra lookups)
        $cfCountry = $_SERVER['HTTP_CF_IPCOUNTRY'] ?? null;
        if ($cfCountry !== null && $cfCountry !== 'XX' && $cfCountry !== 'T1') {
            $code = strtoupper(trim($cfCountry));
            if (preg_match('/^[A-Z]{2}$/', $code)) {
                return $code;
            }
        }

        // Fallback: X-Geo-Country (set by some reverse proxies/CDNs)
        $xGeo = $_SERVER['HTTP_X_GEO_COUNTRY'] ?? null;
        if ($xGeo !== null) {
            $code = strtoupper(trim($xGeo));
            if (preg_match('/^[A-Z]{2}$/', $code)) {
                return $code;
            }
        }

        // No reliable country detection available
        return null;
    }

    /**
     * Parse Accept-Language header to determine preferred language.
     * Returns 2-letter language code or null.
     */
    private static function detectBrowserLanguage(): ?string
    {
        $header = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        if ($header === '') {
            return null;
        }

        // Parse Accept-Language: e.g., "fr-BE,fr;q=0.9,nl;q=0.8,en;q=0.7"
        $languages = [];
        foreach (explode(',', $header) as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }

            $segments = explode(';', $part);
            $lang = strtolower(trim($segments[0]));
            $quality = 1.0;

            if (isset($segments[1])) {
                $qPart = trim($segments[1]);
                if (str_starts_with($qPart, 'q=')) {
                    $quality = (float) substr($qPart, 2);
                }
            }

            // Extract just the language code (fr-BE → fr)
            $langCode = explode('-', $lang)[0];
            if (preg_match('/^[a-z]{2,3}$/', $langCode)) {
                $languages[$langCode] = max($languages[$langCode] ?? 0, $quality);
            }
        }

        if (empty($languages)) {
            return null;
        }

        // Sort by quality descending and return the top language
        arsort($languages);
        return array_key_first($languages);
    }

    /**
     * For visitors already on the correct domain but a multi-language one,
     * check if their browser language suggests a different path.
     * Only redirects if visitor is on the root path (not deep-linked).
     */
    private static function checkLanguageRedirect(string $host): ?string
    {
        if (!isset(self::DOMAIN_LANGUAGES[$host])) {
            return null;
        }

        $uri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
        // Only redirect from root path — don't interrupt deep-linked visitors
        if ($uri !== '/' && $uri !== '') {
            return null;
        }

        $browserLang = self::detectBrowserLanguage();
        if ($browserLang === null) {
            return null;
        }

        $languages = self::DOMAIN_LANGUAGES[$host];
        if (isset($languages[$browserLang]) && $languages[$browserLang] !== '/') {
            return 'https://' . $host . $languages[$browserLang];
        }

        return null;
    }

    private static function hasOverrideCookie(): bool
    {
        return isset($_COOKIE[self::OVERRIDE_COOKIE]) && $_COOKIE[self::OVERRIDE_COOKIE] === '1';
    }

    private static function isAjax(): bool
    {
        return (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest')
            || (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json'));
    }

    private static function isBot(): bool
    {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        if ($ua === '') {
            return false;
        }

        // Major search engine crawlers that must index each domain independently
        $bots = [
            'Googlebot', 'Bingbot', 'Slurp', 'DuckDuckBot', 'Baiduspider',
            'YandexBot', 'Sogou', 'facebookexternalhit', 'Twitterbot',
            'LinkedInBot', 'WhatsApp', 'TelegramBot', 'Applebot',
            'AhrefsBot', 'SemrushBot', 'MJ12bot', 'DotBot', 'PetalBot',
            'Bytespider', 'GPTBot', 'ClaudeBot', 'anthropic-ai',
            'ia_archiver', 'archive.org_bot',
        ];

        $uaLower = strtolower($ua);
        foreach ($bots as $bot) {
            if (str_contains($uaLower, strtolower($bot))) {
                return true;
            }
        }

        return false;
    }

    private static function isExcludedPath(string $uri): bool
    {
        $excluded = ['/admin', '/api/', '/setup', '/assets/', '/storage/', '/sitemap', '/robots.txt'];
        foreach ($excluded as $prefix) {
            if (str_starts_with($uri, $prefix)) {
                return true;
            }
        }
        return false;
    }

    private static function isSecure(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ($_SERVER['SERVER_PORT'] ?? 0) == 443
            || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
    }
}
