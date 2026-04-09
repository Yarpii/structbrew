<?php

declare(strict_types=1);

namespace Brew\Models;

use Brew\Core\Model;

class Website extends Model
{
    protected static string $table = 'websites';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'code',
        'name',
        'is_default',
        'sort_order',
        'is_active',
    ];

    /**
     * Get all stores belonging to this website.
     */
    public function stores(): array
    {
        return $this->hasMany(Store::class, 'website_id');
    }

    /**
     * Get the default store for this website.
     */
    public function defaultStore(): ?array
    {
        return Store::query()
            ->where('website_id', $this->getId())
            ->where('is_default', 1)
            ->first();
    }
}
