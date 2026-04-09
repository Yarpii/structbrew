<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class TicketAttachment extends Model
{
    protected static string $table = 'ticket_attachments';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'ticket_id',
        'reply_id',
        'uploader_type',
        'uploader_id',
        'original_name',
        'stored_name',
        'mime_type',
        'file_size',
        'disk',
        'path',
    ];
}
