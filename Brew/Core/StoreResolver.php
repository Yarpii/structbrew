<?php

declare(strict_types=1);

namespace Brew\Core;

class StoreResolver
{
    private static ?array $currentStore = null;
    private static ?array $currentStoreView = null;
    private static ?array $currentWebsite = null;

    public static function resolve(?string $host = null): void
    {
        $host = $host ?? ($_SERVER['HTTP_HOST'] ?? 'localhost');
        $host = strtolower(preg_replace('/:\d+$/', '', $host)); // Remove port

        $db = Database::getInstance();

        // Look up the domain in store_domains
        $domain = $db->table('store_domains')
            ->where('domain', $host)
            ->where('is_active', 1)
            ->first();

        if ($domain) {
            self::loadStoreView((int) $domain['store_view_id']);
        } else {
            // Fallback to default store view
            self::loadDefaultStoreView();
        }
    }

    private static function loadStoreView(int $storeViewId): void
    {
        $db = Database::getInstance();

        self::$currentStoreView = $db->table('store_views')
            ->where('id', $storeViewId)
            ->where('is_active', 1)
            ->first();

        if (!self::$currentStoreView) {
            self::loadDefaultStoreView();
            return;
        }

        self::$currentStore = $db->table('stores')
            ->where('id', self::$currentStoreView['store_id'])
            ->first();

        if (self::$currentStore) {
            self::$currentWebsite = $db->table('websites')
                ->where('id', self::$currentStore['website_id'])
                ->first();
        }
    }

    private static function loadDefaultStoreView(): void
    {
        $db = Database::getInstance();

        self::$currentStoreView = $db->table('store_views')
            ->where('is_default', 1)
            ->first();

        if (self::$currentStoreView) {
            self::$currentStore = $db->table('stores')
                ->where('id', self::$currentStoreView['store_id'])
                ->first();

            if (self::$currentStore) {
                self::$currentWebsite = $db->table('websites')
                    ->where('id', self::$currentStore['website_id'])
                    ->first();
            }
        }
    }

    // ─── Getters ─────────────────────────────────────────────

    public static function storeView(): ?array
    {
        return self::$currentStoreView;
    }

    public static function store(): ?array
    {
        return self::$currentStore;
    }

    public static function website(): ?array
    {
        return self::$currentWebsite;
    }

    public static function storeViewId(): ?int
    {
        return self::$currentStoreView ? (int) self::$currentStoreView['id'] : null;
    }

    public static function storeId(): ?int
    {
        return self::$currentStore ? (int) self::$currentStore['id'] : null;
    }

    public static function websiteId(): ?int
    {
        return self::$currentWebsite ? (int) self::$currentWebsite['id'] : null;
    }

    public static function locale(): string
    {
        return self::$currentStoreView['locale'] ?? 'en_US';
    }

    public static function currency(): string
    {
        return self::$currentStoreView['currency_code'] ?? 'USD';
    }

    public static function currencySymbol(): string
    {
        $symbols = [
            'EUR' => "\u{20AC}", 'USD' => '$', 'GBP' => "\u{00A3}",
            'CHF' => 'CHF', 'SEK' => 'kr', 'NOK' => 'kr',
            'DKK' => 'kr', 'PLN' => "z\u{0142}", 'CZK' => "K\u{010D}",
        ];
        return $symbols[self::currency()] ?? self::currency();
    }

    public static function theme(): string
    {
        return self::$currentStoreView['theme'] ?? 'default';
    }

    public static function language(): string
    {
        $locale = self::locale();
        return substr($locale, 0, 2);
    }

    public static function country(): string
    {
        $locale = self::locale();
        return strtoupper(substr($locale, 3, 2));
    }

    public static function taxRate(): float
    {
        $db = Database::getInstance();
        $country = self::country();

        $taxRate = $db->table('tax_rates')
            ->join('tax_zones', 'tax_rates.tax_zone_id', '=', 'tax_zones.id')
            ->where('tax_zones.country_code', $country)
            ->where('tax_rates.is_active', 1)
            ->first();

        return $taxRate ? (float) $taxRate['rate'] : 0.0;
    }

    // ─── Scoped Config ───────────────────────────────────────

    public static function getConfig(string $key, mixed $default = null): mixed
    {
        $db = Database::getInstance();

        // Try store view level first
        if (self::storeViewId()) {
            $config = $db->table('configurations')
                ->where('path', $key)
                ->where('scope', 'store_view')
                ->where('scope_id', self::storeViewId())
                ->first();
            if ($config) return $config['value'];
        }

        // Then store level
        if (self::storeId()) {
            $config = $db->table('configurations')
                ->where('path', $key)
                ->where('scope', 'store')
                ->where('scope_id', self::storeId())
                ->first();
            if ($config) return $config['value'];
        }

        // Then website level
        if (self::websiteId()) {
            $config = $db->table('configurations')
                ->where('path', $key)
                ->where('scope', 'website')
                ->where('scope_id', self::websiteId())
                ->first();
            if ($config) return $config['value'];
        }

        // Finally global
        $config = $db->table('configurations')
            ->where('path', $key)
            ->where('scope', 'global')
            ->where('scope_id', 0)
            ->first();

        return $config ? $config['value'] : $default;
    }

    public static function setConfig(string $key, mixed $value, string $scope = 'global', int $scopeId = 0): void
    {
        $db = Database::getInstance();

        $existing = $db->table('configurations')
            ->where('path', $key)
            ->where('scope', $scope)
            ->where('scope_id', $scopeId)
            ->first();

        if ($existing) {
            $db->table('configurations')
                ->where('id', $existing['id'])
                ->update(['value' => (string) $value, 'updated_at' => date('Y-m-d H:i:s')]);
        } else {
            $db->table('configurations')->insert([
                'path' => $key,
                'value' => (string) $value,
                'scope' => $scope,
                'scope_id' => $scopeId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    // ─── Format Helpers (locale-aware) ───────────────────────

    public static function formatPrice(float $amount): string
    {
        $symbol = self::currencySymbol();
        $locale = self::locale();

        // European formatting
        if (in_array(self::country(), ['NL', 'DE', 'FR', 'BE', 'AT', 'IT', 'ES', 'PT'])) {
            return $symbol . ' ' . number_format($amount, 2, ',', '.');
        }

        // UK formatting
        if (self::country() === 'GB') {
            return $symbol . number_format($amount, 2, '.', ',');
        }

        // Default US formatting
        return $symbol . number_format($amount, 2, '.', ',');
    }

    public static function formatDate(string $date): string
    {
        $timestamp = strtotime($date);
        return match (self::country()) {
            'NL' => date('d-m-Y', $timestamp),
            'DE', 'AT' => date('d.m.Y', $timestamp),
            'FR', 'BE' => date('d/m/Y', $timestamp),
            'GB' => date('d/m/Y', $timestamp),
            default => date('m/d/Y', $timestamp),
        };
    }
}
