<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Vehicle extends Model
{
    protected static string $table = 'vehicles';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'brand_id',
        'model',
        'year_from',
        'year_to',
        'engine_cc',
        'slug',
        'is_active',
    ];

    /**
     * Get the brand this vehicle belongs to.
     */
    public function brand(): ?array
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    /**
     * Get all products associated with this vehicle through the pivot table.
     */
    public function products(): array
    {
        $db = Database::getInstance();
        $pivotRows = $db->table('product_vehicles')
            ->where('vehicle_id', $this->getId())
            ->get();

        if (empty($pivotRows)) {
            return [];
        }

        $productIds = array_column($pivotRows, 'product_id');

        return Product::query()
            ->whereIn('id', $productIds)
            ->get();
    }

    /**
     * Get a human-readable display name for this vehicle.
     * Format: "Brand Model (year_from-year_to) engine_cccc"
     */
    public function displayName(): string
    {
        $brand = $this->brand();
        $brandName = $brand['name'] ?? 'Unknown';

        $yearRange = $this->year_from;
        if ($this->year_to) {
            $yearRange .= '-' . $this->year_to;
        }

        $cc = $this->engine_cc ? $this->engine_cc . 'cc' : '';

        $name = "{$brandName} {$this->model} ({$yearRange})";
        if ($cc !== '') {
            $name .= " {$cc}";
        }

        return $name;
    }
}
