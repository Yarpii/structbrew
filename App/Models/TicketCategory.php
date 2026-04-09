<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class TicketCategory extends Model
{
    protected static string $table = 'ticket_categories';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'department_id',
        'name',
        'slug',
        'description',
        'default_priority',
        'auto_assign_agent_id',
        'is_active',
        'sort_order',
    ];

    public function department(): ?array
    {
        return $this->belongsTo(TicketDepartment::class, 'department_id');
    }
}
