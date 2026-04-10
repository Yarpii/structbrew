<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

final class PartnerApplication extends Model
{
    protected static string $table = 'partner_applications';
    protected static array $fillable = [
        'first_name', 'last_name', 'email', 'company', 'website',
        'country', 'message', 'status', 'admin_notes',
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
