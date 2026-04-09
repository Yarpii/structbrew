<?php

declare(strict_types=1);

namespace App\Core;

class Translator
{
    private static array $translations = [];
    private static string $locale = 'en_US';
    private static string $fallback = 'en_US';

    public static function setLocale(string $locale): void
    {
        self::$locale = $locale;
        self::loadTranslations($locale);
    }

    public static function getLocale(): string
    {
        return self::$locale;
    }

    public static function setFallback(string $locale): void
    {
        self::$fallback = $locale;
    }

    private static function loadTranslations(string $locale): void
    {
        if (isset(self::$translations[$locale])) return;

        $path = dirname(__DIR__) . '/Locale/' . $locale . '.php';
        if (file_exists($path)) {
            self::$translations[$locale] = require $path;
        } else {
            self::$translations[$locale] = [];
        }
    }

    public static function get(string $key, array $replace = [], ?string $locale = null): string
    {
        $locale = $locale ?? self::$locale;
        self::loadTranslations($locale);

        // Try current locale
        $translation = self::$translations[$locale][$key] ?? null;

        // Fallback
        if ($translation === null && $locale !== self::$fallback) {
            self::loadTranslations(self::$fallback);
            $translation = self::$translations[self::$fallback][$key] ?? null;
        }

        // If still not found, return the key itself
        if ($translation === null) {
            $translation = $key;
        }

        // Replace placeholders :name
        foreach ($replace as $search => $value) {
            $translation = str_replace(':' . $search, (string) $value, $translation);
        }

        return $translation;
    }

    public static function has(string $key, ?string $locale = null): bool
    {
        $locale = $locale ?? self::$locale;
        self::loadTranslations($locale);
        return isset(self::$translations[$locale][$key]);
    }

    public static function choice(string $key, int $count, array $replace = []): string
    {
        $replace['count'] = $count;
        $translation = self::get($key, $replace);

        // Support "one|many" syntax
        if (str_contains($translation, '|')) {
            $parts = explode('|', $translation);
            $translation = $count === 1 ? $parts[0] : ($parts[1] ?? $parts[0]);
        }

        return $translation;
    }

    public static function allForLocale(?string $locale = null): array
    {
        $locale = $locale ?? self::$locale;
        self::loadTranslations($locale);
        return self::$translations[$locale] ?? [];
    }

    /**
     * Load translations from database for the current store view
     */
    public static function loadFromDatabase(): void
    {
        $storeViewId = StoreResolver::storeViewId();
        if (!$storeViewId) return;

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
        } catch (\Throwable $e) {
            // Database might not be set up yet
        }
    }
}

// Global helper function
function __( string $key, array $replace = []): string
{
    return Translator::get($key, $replace);
}
