<?php

declare(strict_types=1);

namespace Brew\Models;

use Brew\Core\Model;

class CartItem extends Model
{
    protected static string $table = 'cart_items';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'cart_id',
        'product_id',
        'qty',
        'price',
        'row_total',
    ];
}
