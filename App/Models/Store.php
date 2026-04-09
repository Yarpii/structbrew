<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Store extends Model
{
    protected static string $table = 'stores';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'website_id',
        'code',
        'name',
        'is_default',
        'sort_order',
        'is_active',
    ];

    /**
     * Get the website this store belongs to.
     */
    public function website(): ?array
    {
        return $this->belongsTo(Website::class, 'website_id');
    }

    /**
     * Get all store views belonging to this store.
     */
    public function storeViews(): array
    {
        return $this->hasMany(StoreView::class, 'store_id');
    }

    /**
     * Get the default store view for this store.
     */
    public function defaultView(): ?array
    {
        return StoreView::query()
            ->where('store_id', $this->getId())
            ->where('is_default', 1)
            ->first();
    }
}
