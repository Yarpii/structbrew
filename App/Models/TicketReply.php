<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class TicketReply extends Model
{
    protected static string $table = 'ticket_replies';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'ticket_id',
        'author_type',
        'author_id',
        'author_name',
        'author_email',
        'body',
        'is_internal',
        'is_resolution',
        'time_spent_minutes',
    ];

    public function attachments(): array
    {
        return $this->hasMany(TicketAttachment::class, 'reply_id');
    }
}
