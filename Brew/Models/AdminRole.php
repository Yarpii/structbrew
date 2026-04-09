<?php

declare(strict_types=1);

namespace Brew\Models;

use Brew\Core\Model;

class AdminRole extends Model
{
    protected static string $table = 'admin_roles';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'name',
        'permissions',
    ];
    protected static array $casts = [
        'permissions' => 'json',
    ];

    /**
     * Get all admin users assigned to this role.
     */
    public function users(): array
    {
        return $this->hasMany(AdminUser::class, 'role_id');
    }

    /**
     * Check whether this role has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        $permissions = $this->getAttribute('permissions');

        if (!is_array($permissions)) {
            return false;
        }

        return in_array($permission, $permissions, true);
    }
}
