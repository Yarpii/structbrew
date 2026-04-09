<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class OrderItem extends Model
{
    protected static string $table = 'order_items';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'order_id',
        'product_id',
        'sku',
        'name',
        'qty',
        'price',
        'row_total',
        'tax_amount',
        'discount_amount',
        'options',
    ];
    protected static array $casts = [
        'options' => 'json',
    ];
}
