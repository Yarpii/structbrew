<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class PriceRule extends Model
{
    protected static string $table = 'price_rules';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'name',
        'description',
        'type',
        'value',
        'min_order_total',
        'starts_at',
        'expires_at',
        'is_active',
        'usage_limit',
        'times_used',
        'store_view_ids',
    ];
    protected static array $casts = [
        'store_view_ids' => 'json',
    ];

    /**
     * Get all coupons associated with this price rule.
     */
    public function coupons(): array
    {
        return $this->hasMany(Coupon::class, 'price_rule_id');
    }

    /**
     * Check whether this price rule is currently valid.
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = date('Y-m-d H:i:s');

        if ($this->starts_at && $this->starts_at > $now) {
            return false;
        }

        if ($this->expires_at && $this->expires_at < $now) {
            return false;
        }

        if ($this->usage_limit && $this->times_used >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Calculate the discount amount for a given subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if (!$this->isValid()) {
            return 0.0;
        }

        if ($this->min_order_total && $subtotal < (float) $this->min_order_total) {
            return 0.0;
        }

        $value = (float) ($this->value ?? 0);

        return match ($this->type) {
            'percentage' => round($subtotal * ($value / 100), 2),
            'fixed' => min($value, $subtotal),
            default => 0.0,
        };
    }
}
