<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class DealerApplication extends Model
{
    protected static string $table = 'dealer_applications';
    protected static array $fillable = [
        'company_name', 'contact_name', 'email', 'phone', 'website',
        'country', 'business_type', 'vat_number', 'annual_volume',
        'message', 'status', 'admin_notes',
    ];

    public function getId(): int
    {
        return (int) ($this->attributes['id'] ?? 0);
    }

    public function getStatus(): string
    {
        return (string) ($this->attributes['status'] ?? 'pending');
    }

    public function isPending(): bool
    {
        return $this->getStatus() === 'pending';
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}
