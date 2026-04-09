<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Currency extends Model
{
    protected static string $table = 'currencies';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'code',
        'name',
        'symbol',
        'decimal_places',
        'is_active',
    ];

    /**
     * Convert an amount from one currency to another using stored exchange rates.
     */
    public static function convert(float $amount, string $from, string $to): float
    {
        if ($from === $to) {
            return $amount;
        }

        $db = Database::getInstance();

        $rate = $db->table('currency_rates')
            ->where('from_currency', $from)
            ->where('to_currency', $to)
            ->first();

        if ($rate) {
            return $amount * (float) $rate['rate'];
        }

        // Try inverse rate
        $inverse = $db->table('currency_rates')
            ->where('from_currency', $to)
            ->where('to_currency', $from)
            ->first();

        if ($inverse && (float) $inverse['rate'] > 0) {
            return $amount / (float) $inverse['rate'];
        }

        throw new \RuntimeException("No exchange rate found for {$from} to {$to}.");
    }
}
