<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Cart extends Model
{
    protected static string $table = 'carts';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'customer_id',
        'store_view_id',
        'session_id',
        'currency_code',
        'coupon_code',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'grand_total',
    ];

    /**
     * Get all items in this cart.
     */
    public function items(): array
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }

    /**
     * Add a product to the cart or increment its quantity if it already exists.
     */
    public function addItem(int $productId, int $qty = 1): void
    {
        $db = Database::getInstance();

        $existing = $db->table('cart_items')
            ->where('cart_id', $this->getId())
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $newQty = (int) $existing['qty'] + $qty;
            $rowTotal = (float) $existing['price'] * $newQty;

            $db->table('cart_items')
                ->where('id', $existing['id'])
                ->update([
                    'qty' => $newQty,
                    'row_total' => $rowTotal,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        } else {
            $product = Product::find($productId);
            $pricing = $product ? $product->pricing() : null;
            $price = $pricing['price'] ?? 0;
            $rowTotal = (float) $price * $qty;

            $db->table('cart_items')->insert([
                'cart_id' => $this->getId(),
                'product_id' => $productId,
                'qty' => $qty,
                'price' => $price,
                'row_total' => $rowTotal,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->recalculate();
    }

    /**
     * Update the quantity of a specific cart item.
     */
    public function updateItem(int $cartItemId, int $qty): void
    {
        $db = Database::getInstance();

        if ($qty <= 0) {
            $this->removeItem($cartItemId);
            return;
        }

        $item = $db->table('cart_items')
            ->where('id', $cartItemId)
            ->where('cart_id', $this->getId())
            ->first();

        if (!$item) {
            return;
        }

        $rowTotal = (float) $item['price'] * $qty;

        $db->table('cart_items')
            ->where('id', $cartItemId)
            ->update([
                'qty' => $qty,
                'row_total' => $rowTotal,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $this->recalculate();
    }

    /**
     * Remove a specific item from the cart.
     */
    public function removeItem(int $cartItemId): void
    {
        $db = Database::getInstance();

        $db->table('cart_items')
            ->where('id', $cartItemId)
            ->where('cart_id', $this->getId())
            ->delete();

        $this->recalculate();
    }

    /**
     * Recalculate the cart totals based on current items.
     */
    public function recalculate(): void
    {
        $db = Database::getInstance();

        $subtotal = $db->table('cart_items')
            ->where('cart_id', $this->getId())
            ->sum('row_total');

        $taxAmount = (float) ($this->tax_amount ?? 0);
        $discountAmount = (float) ($this->discount_amount ?? 0);
        $grandTotal = $subtotal + $taxAmount - $discountAmount;

        $this->subtotal = $subtotal;
        $this->grand_total = $grandTotal;

        static::query()
            ->where(static::$primaryKey, $this->getId())
            ->update([
                'subtotal' => $subtotal,
                'grand_total' => $grandTotal,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    }

    /**
     * Remove all items from the cart and reset totals.
     */
    public function clear(): void
    {
        $db = Database::getInstance();

        $db->table('cart_items')
            ->where('cart_id', $this->getId())
            ->delete();

        $this->subtotal = 0;
        $this->tax_amount = 0;
        $this->discount_amount = 0;
        $this->grand_total = 0;

        static::query()
            ->where(static::$primaryKey, $this->getId())
            ->update([
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'grand_total' => 0,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    }
}
