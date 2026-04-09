<?php

declare(strict_types=1);

namespace Brew\Models;

use Brew\Core\Model;

class AdminUser extends Model
{
    protected static string $table = 'admin_users';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'role_id',
        'email',
        'password_hash',
        'first_name',
        'last_name',
        'is_active',
        'is_superadmin',
    ];
    protected static array $hidden = [
        'password_hash',
    ];

    /**
     * Get the role assigned to this admin user.
     */
    public function role(): ?array
    {
        return $this->belongsTo(AdminRole::class, 'role_id');
    }

    /**
     * Get the admin user's full name.
     */
    public function fullName(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }
}
