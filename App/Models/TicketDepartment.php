<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class TicketDepartment extends Model
{
    protected static string $table = 'ticket_departments';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'name',
        'code',
        'description',
        'color',
        'is_active',
        'sort_order',
    ];

    public function categories(): array
    {
        return $this->hasMany(TicketCategory::class, 'department_id');
    }
}
