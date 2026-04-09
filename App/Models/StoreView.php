<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class StoreView extends Model
{
    protected static string $table = 'store_views';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'store_id',
        'code',
        'name',
        'locale',
        'country_code',
        'hreflang',
        'timezone',
        'currency_code',
        'theme',
        'is_default',
        'sort_order',
        'is_active',
    ];

    /**
     * Get the store this view belongs to.
     */
    public function store(): ?array
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    /**
     * Get all domains associated with this store view.
     */
    public function domains(): array
    {
        return $this->hasMany(StoreDomain::class, 'store_view_id');
    }
}
