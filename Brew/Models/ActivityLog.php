<?php

declare(strict_types=1);

namespace Brew\Models;

use Brew\Core\Model;

class ActivityLog extends Model
{
    protected static string $table = 'activity_log';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'admin_user_id',
        'action',
        'entity_type',
        'entity_id',
        'old_data',
        'new_data',
        'ip_address',
    ];
    protected static array $casts = [
        'old_data' => 'json',
        'new_data' => 'json',
    ];

    /**
     * Log an activity entry.
     */
    public static function log(
        string $action,
        string $entityType,
        int|string $entityId,
        ?array $oldData = null,
        ?array $newData = null
    ): static {
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $adminUserId = $_SESSION['admin_user_id'] ?? null;

        return static::create([
            'admin_user_id' => $adminUserId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_data' => $oldData !== null ? json_encode($oldData) : null,
            'new_data' => $newData !== null ? json_encode($newData) : null,
            'ip_address' => $ipAddress,
        ]);
    }
}
