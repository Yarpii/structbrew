<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class TicketSlaPolicy extends Model
{
    protected static string $table = 'ticket_sla_policies';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'name',
        'applies_to_priority',
        'first_response_hours',
        'resolution_hours',
        'escalation_hours',
        'business_hours_only',
        'is_active',
    ];
}
