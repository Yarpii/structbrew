<?php

declare(strict_types=1);

namespace Brew\Models;

use Brew\Core\Model;
use Brew\Core\Database;

class Product extends Model
{
    protected static string $table = 'products';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'sku',
        'slug',
        'brand_id',
        'weight',
        'is_active',
        'is_featured',
        'manage_stock',
        'stock_qty',
        'low_stock_threshold',
        'oem_number',
    ];

    /**
     * Get the brand this product belongs to.
     */
    public function brand(): ?array
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    /**
     * Get all categories for this product through the pivot table.
     */
    public function categories(): array
    {
        $db = Database::getInstance();
        $pivotRows = $db->table('product_categories')
            ->where('product_id', $this->getId())
            ->get();

        if (empty($pivotRows)) {
            return [];
        }

        $categoryIds = array_column($pivotRows, 'category_id');

        return Category::query()
            ->whereIn('id', $categoryIds)
            ->get();
    }

    /**
     * Get all vehicles for this product through the pivot table.
     */
    public function vehicles(): array
    {
        $db = Database::getInstance();
        $pivotRows = $db->table('product_vehicles')
            ->where('product_id', $this->getId())
            ->get();

        if (empty($pivotRows)) {
            return [];
        }

        $vehicleIds = array_column($pivotRows, 'vehicle_id');

        return Vehicle::query()
            ->whereIn('id', $vehicleIds)
            ->get();
    }

    /**
     * Get all images for this product.
     */
    public function images(): array
    {
        $db = Database::getInstance();

        return $db->table('product_images')
            ->where('product_id', $this->getId())
            ->orderBy('sort_order', 'ASC')
            ->get();
    }

    /**
     * Get the main (first) image for this product.
     */
    public function mainImage(): ?array
    {
        $db = Database::getInstance();

        return $db->table('product_images')
            ->where('product_id', $this->getId())
            ->orderBy('sort_order', 'ASC')
            ->first();
    }

    /**
     * Get the translation for a specific store view (or the first available).
     */
    public function translation(?int $storeViewId = null): ?array
    {
        $db = Database::getInstance();
        $query = $db->table('product_translations')
            ->where('product_id', $this->getId());

        if ($storeViewId !== null) {
            $query->where('store_view_id', $storeViewId);
        }

        return $query->first();
    }

    /**
     * Get the pricing for a specific store view (or the first available).
     */
    public function pricing(?int $storeViewId = null): ?array
    {
        $db = Database::getInstance();
        $query = $db->table('product_pricing')
            ->where('product_id', $this->getId());

        if ($storeViewId !== null) {
            $query->where('store_view_id', $storeViewId);
        }

        return $query->first();
    }

    /**
     * Get the attributes for a specific store view (or all for the first available).
     */
    public function attributes(?int $storeViewId = null): array
    {
        $db = Database::getInstance();
        $query = $db->table('product_attributes')
            ->where('product_id', $this->getId());

        if ($storeViewId !== null) {
            $query->where('store_view_id', $storeViewId);
        }

        return $query->get();
    }

    /**
     * Check whether the product is currently in stock.
     */
    public function isInStock(): bool
    {
        if (!$this->manage_stock) {
            return true;
        }

        return ($this->stock_qty ?? 0) > 0;
    }

    /**
     * Decrement the stock quantity by the given amount.
     */
    public function decrementStock(int $qty): bool
    {
        $currentQty = (int) ($this->stock_qty ?? 0);
        $newQty = $currentQty - $qty;

        $this->stock_qty = $newQty;

        return static::query()
            ->where(static::$primaryKey, $this->getId())
            ->update([
                'stock_qty' => $newQty,
                'updated_at' => date('Y-m-d H:i:s'),
            ]) > 0;
    }
}
