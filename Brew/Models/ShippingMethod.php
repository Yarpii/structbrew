<?php

declare(strict_types=1);

namespace Brew\Models;

use Brew\Core\Model;
use Brew\Core\Database;

class ShippingMethod extends Model
{
    protected static string $table = 'shipping_methods';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'code',
        'name',
        'description',
        'is_active',
        'sort_order',
    ];

    /**
     * Get all rates for this shipping method.
     */
    public function rates(): array
    {
        $db = Database::getInstance();

        return $db->table('shipping_rates')
            ->where('shipping_method_id', $this->getId())
            ->get();
    }

    /**
     * Get the applicable rate for a specific zone, weight, and order total.
     */
    public function getRateForZone(int $zoneId, float $weight = 0, float $orderTotal = 0): ?array
    {
        $db = Database::getInstance();

        $query = $db->table('shipping_rates')
            ->where('shipping_method_id', $this->getId())
            ->where('tax_zone_id', $zoneId)
            ->where('is_active', 1);

        if ($weight > 0) {
            $query->where('min_weight', '<=', $weight)
                ->where('max_weight', '>=', $weight);
        }

        if ($orderTotal > 0) {
            $query->where('min_order_total', '<=', $orderTotal);
        }

        return $query->orderBy('price', 'ASC')->first();
    }
}
