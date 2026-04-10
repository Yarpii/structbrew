<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class DealerAccount extends Model
{
    protected static string $table = 'dealer_accounts';
    protected static array $fillable = [
        'application_id', 'customer_id', 'company_name', 'contact_name',
        'email', 'phone', 'account_number', 'discount_rate',
        'credit_limit', 'payment_terms', 'status', 'notes',
    ];

    public function getId(): int
    {
        return (int) ($this->attributes['id'] ?? 0);
    }

    public function getAccountNumber(): string
    {
        return (string) ($this->attributes['account_number'] ?? '');
    }

    public function getStatus(): string
    {
        return (string) ($this->attributes['status'] ?? 'active');
    }

    public function isActive(): bool
    {
        return $this->getStatus() === 'active';
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}
