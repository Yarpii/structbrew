<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class TaxRate extends Model
{
    protected static string $table = 'tax_rates';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'tax_zone_id',
        'name',
        'rate',
        'is_active',
    ];

    /**
     * Get all active tax rates applicable to a given country code.
     */
    public static function getForCountry(string $countryCode): array
    {
        $db = Database::getInstance();

        return $db->table('tax_rates')
            ->join('tax_zones', 'tax_rates.tax_zone_id', '=', 'tax_zones.id')
            ->where('tax_zones.country_code', $countryCode)
            ->where('tax_zones.is_active', 1)
            ->where('tax_rates.is_active', 1)
            ->get();
    }
}
