<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class TicketCannedResponse extends Model
{
    protected static string $table = 'ticket_canned_responses';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'department_id',
        'name',
        'subject',
        'body',
        'is_active',
        'sort_order',
    ];
}
