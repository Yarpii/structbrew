<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class PartnerAccount extends Model
{
    protected static string $table = 'partner_accounts';
    protected static array $fillable = [
        'application_id', 'customer_id', 'first_name', 'last_name',
        'email', 'company', 'referral_code', 'commission_rate',
        'total_clicks', 'total_conversions', 'total_commission_earned',
        'balance', 'status',
    ];

    public function getId(): int
    {
        return (int) ($this->attributes['id'] ?? 0);
    }

    public function getReferralCode(): string
    {
        return (string) ($this->attributes['referral_code'] ?? '');
    }

    public function getStatus(): string
    {
        return (string) ($this->attributes['status'] ?? 'active');
    }

    public function isActive(): bool
    {
        return $this->getStatus() === 'active';
    }

    public function getReferralUrl(string $baseUrl = ''): string
    {
        return rtrim($baseUrl, '/') . '/r/' . $this->getReferralCode();
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}
