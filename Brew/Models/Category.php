<?php

declare(strict_types=1);

namespace Brew\Models;

use Brew\Core\Model;
use Brew\Core\Database;

class Category extends Model
{
    protected static string $table = 'categories';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'parent_id',
        'slug',
        'position',
        'is_active',
        'image',
    ];

    /**
     * Get the parent category.
     */
    public function parent(): ?array
    {
        if ($this->parent_id === null) {
            return null;
        }

        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Get all direct child categories.
     */
    public function children(): array
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Get the translation for a specific store view (or the first available).
     */
    public function translation(?int $storeViewId = null): ?array
    {
        $query = Database::getInstance()
            ->table('category_translations')
            ->where('category_id', $this->getId());

        if ($storeViewId !== null) {
            $query->where('store_view_id', $storeViewId);
        }

        return $query->first();
    }

    /**
     * Get all products in this category through the pivot table.
     */
    public function products(): array
    {
        $db = Database::getInstance();
        $pivotRows = $db->table('product_categories')
            ->where('category_id', $this->getId())
            ->get();

        if (empty($pivotRows)) {
            return [];
        }

        $productIds = array_column($pivotRows, 'product_id');

        return Product::query()
            ->whereIn('id', $productIds)
            ->get();
    }
}
