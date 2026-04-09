<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Brand extends Model
{
    protected static string $table = 'brands';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'slug',
        'name',
        'logo',
        'website_url',
        'is_active',
        'sort_order',
    ];

    /**
     * Get all vehicles for this brand.
     */
    public function vehicles(): array
    {
        return $this->hasMany(Vehicle::class, 'brand_id');
    }

    /**
     * Get all products for this brand.
     */
    public function products(): array
    {
        return $this->hasMany(Product::class, 'brand_id');
    }
}
