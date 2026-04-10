<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Advanced translation system with folder-based locale organization.
 *
 * Structure:
 *   App/Locale/{locale}/
 *     common.php    ← always loaded (nav, footer, general UI, errors)
 *     shop.php      ← loaded when Translator::page('shop') is called
 *     product.php   ← loaded when viewing a product detail page
 *     cart.php      ← cart-specific translations
 *     checkout.php  ← checkout flow translations
 *     account.php   ← account area translations
 *     auth.php      ← login/register translations
 *     vehicles.php  ← scooter/vehicle specific translations
 *     {custom}.php  ← any future page group
 *
 * Loading order:
 *   1. common.php always loads for the current locale
 *   2. Translator::page('shop') loads shop.php on top
 *   3. Database translations overlay on top of everything (per store view)
 *   4. Fallback: current locale → fallback locale (en_US) → key itself
 *
 * Backwards compatible: also supports legacy flat files (App/Locale/{locale}.php)
 */
final class Translator
{
    /** Merged translations per locale: locale → [key => value] */
    private static array $translations = [];

    /** Track which groups have been loaded per locale */
    private static array $loadedGroups = [];

    /** Current active locale */
    private static string $locale = 'en_US';

    /** Fallback locale when key not found */
    private static string $fallback = 'en_US';

    /** Current page group (e.g., 'shop', 'cart', 'checkout') */
    private static ?string $currentPage = null;

    /** Whether database translations have been loaded */
    private static bool $dbLoaded = false;

    /**
     * Set the active locale and load common translations.
     */
    public static function setLocale(string $locale): void
    {
        self::$locale = $locale;
        self::loadGroup($locale, 'common');
    }

    public static function getLocale(): string
    {
        return self::$locale;
    }

    public static function setFallback(string $locale): void
    {
        self::$fallback = $locale;
    }

    /**
     * Set the current page context. Loads page-specific translations.
     *
     * Example: Translator::page('shop') loads App/Locale/nl_NL/shop.php
     * Can be called multiple times to stack groups (e.g., page('shop') then page('product')).
     */
    public static function page(string $group): void
    {
        self::$currentPage = $group;
        self::loadGroup(self::$locale, $group);

        // Also load for fallback if different
        if (self::$locale !== self::$fallback) {
            self::loadGroup(self::$fallback, $group);
        }
    }

    /**
     * Get a translation by key, with placeholder replacement.
     *
     * Lookup order:
     *   1. Current locale (common + page groups + database)
     *   2. Fallback locale (common + page groups)
     *   3. The key itself
     */
    public static function get(string $key, array $replace = [], ?string $locale = null): string
    {
        $locale = $locale ?? self::$locale;

        // Ensure common is loaded for this locale
        self::loadGroup($locale, 'common');

        // Support dot-notation for explicit group access: 'shop.add_to_cart'
        if (str_contains($key, '.')) {
            [$group, $subKey] = explode('.', $key, 2);
            self::loadGroup($locale, $group);
            // Try group-prefixed key first, then just the subKey
            $translation = self::$translations[$locale][$subKey] ?? null;
            if ($translation === null) {
                $translation = self::$translations[$locale][$key] ?? null;
            }
        } else {
            $translation = self::$translations[$locale][$key] ?? null;
        }

        // Fallback to fallback locale
        if ($translation === null && $locale !== self::$fallback) {
            self::loadGroup(self::$fallback, 'common');
            $translation = self::$translations[self::$fallback][$key] ?? null;
        }

        // If still not found, return the key itself
        if ($translation === null) {
            $translation = $key;
        }

        // Replace :placeholders
        foreach ($replace as $search => $value) {
            $translation = str_replace(':' . $search, (string) $value, $translation);
        }

        return $translation;
    }

    /**
     * Check if a translation key exists.
     */
    public static function has(string $key, ?string $locale = null): bool
    {
        $locale = $locale ?? self::$locale;
        self::loadGroup($locale, 'common');
        return isset(self::$translations[$locale][$key]);
    }

    /**
     * Handle plural forms using "singular|plural" syntax.
     */
    public static function choice(string $key, int $count, array $replace = []): string
    {
        $replace['count'] = $count;
        $translation = self::get($key, $replace);

        if (str_contains($translation, '|')) {
            $parts = explode('|', $translation);
            $translation = $count === 1 ? $parts[0] : ($parts[1] ?? $parts[0]);
        }

        return $translation;
    }

    /**
     * Get all loaded translations for a locale (common + all loaded groups + DB).
     */
    public static function allForLocale(?string $locale = null): array
    {
        $locale = $locale ?? self::$locale;
        self::loadGroup($locale, 'common');
        return self::$translations[$locale] ?? [];
    }

    /**
     * Load translations from database for the current store view.
     * Database translations override file-based ones.
     */
    public static function loadFromDatabase(): void
    {
        if (self::$dbLoaded) {
            return;
        }

        $storeViewId = StoreResolver::storeViewId();
        if (!$storeViewId) {
            return;
        }

        try {
            $db = Database::getInstance();
            $rows = $db->table('translations')
                ->where('store_view_id', $storeViewId)
                ->get();

            $locale = StoreResolver::locale();
            if (!isset(self::$translations[$locale])) {
                self::$translations[$locale] = [];
            }

            foreach ($rows as $row) {
                self::$translations[$locale][$row['key']] = $row['value'];
            }

            self::$dbLoaded = true;
        } catch (\Throwable $e) {
            // Database might not be set up yet
        }
    }

    /**
     * Get list of available groups (translation files) for a locale.
     */
    public static function availableGroups(?string $locale = null): array
    {
        $locale = $locale ?? self::$locale;
        $dir = self::localeDir($locale);

        if (!is_dir($dir)) {
            return [];
        }

        $groups = [];
        foreach (glob($dir . '/*.php') as $file) {
            $groups[] = basename($file, '.php');
        }

        return $groups;
    }

    /**
     * Get list of available locales (folders in App/Locale/).
     */
    public static function availableLocales(): array
    {
        $baseDir = dirname(__DIR__) . '/Locale';
        $locales = [];

        foreach (scandir($baseDir) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $path = $baseDir . '/' . $entry;
            if (is_dir($path) && preg_match('/^[a-z]{2}_[A-Z]{2}$/', $entry)) {
                $locales[] = $entry;
            }
        }

        sort($locales);
        return $locales;
    }

    // ─── Private Loading Logic ───────────────────────────────

    /**
     * Load a translation group file for a locale.
     * Merges into the locale's translation array (later loads override earlier ones).
     */
    private static function loadGroup(string $locale, string $group): void
    {
        $key = $locale . '/' . $group;
        if (isset(self::$loadedGroups[$key])) {
            return;
        }
        self::$loadedGroups[$key] = true;

        if (!isset(self::$translations[$locale])) {
            self::$translations[$locale] = [];
        }

        // Try new folder structure first: App/Locale/{locale}/{group}.php
        $dir = self::localeDir($locale);
        $file = $dir . '/' . $group . '.php';

        if (is_file($file)) {
            $data = require $file;
            if (is_array($data)) {
                self::$translations[$locale] = array_merge(self::$translations[$locale], $data);
            }
            return;
        }

        // Fallback: legacy flat file App/Locale/{locale}.php (only for 'common' group)
        if ($group === 'common') {
            $legacyFile = dirname(__DIR__) . '/Locale/' . $locale . '.php';
            if (is_file($legacyFile)) {
                $data = require $legacyFile;
                if (is_array($data)) {
                    self::$translations[$locale] = array_merge(self::$translations[$locale], $data);
                }
            }
        }
    }

    /**
     * Get the directory path for a locale.
     */
    private static function localeDir(string $locale): string
    {
        return dirname(__DIR__) . '/Locale/' . $locale;
    }
}
