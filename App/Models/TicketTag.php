<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class TicketTag extends Model
{
    protected static string $table = 'ticket_tags';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'name',
        'slug',
        'color',
    ];
}
