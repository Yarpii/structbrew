<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class StoreDomain extends Model
{
    protected static string $table = 'store_domains';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'store_view_id',
        'domain',
        'is_active',
        'is_primary',
    ];

    /**
     * Get the store view this domain belongs to.
     */
    public function storeView(): ?array
    {
        return $this->belongsTo(StoreView::class, 'store_view_id');
    }
}
