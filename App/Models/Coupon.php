<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Coupon extends Model
{
    protected static string $table = 'coupons';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'price_rule_id',
        'code',
        'usage_limit',
        'usage_per_customer',
        'times_used',
        'is_active',
    ];

    /**
     * Get the price rule this coupon belongs to.
     */
    public function priceRule(): ?array
    {
        return $this->belongsTo(PriceRule::class, 'price_rule_id');
    }

    /**
     * Check whether this coupon is currently valid.
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->usage_limit && $this->times_used >= $this->usage_limit) {
            return false;
        }

        // Also check if the associated price rule is valid
        $rule = $this->priceRule();
        if (!$rule) {
            return false;
        }

        $ruleModel = PriceRule::find((int) $rule['id']);

        return $ruleModel !== null && $ruleModel->isValid();
    }
}
