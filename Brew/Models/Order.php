<?php

declare(strict_types=1);

namespace Brew\Models;

use Brew\Core\Model;
use Brew\Core\Database;

class Order extends Model
{
    protected static string $table = 'orders';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'order_number',
        'customer_id',
        'store_view_id',
        'status',
        'currency_code',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'discount_amount',
        'grand_total',
        'coupon_code',
        'shipping_method',
        'payment_method',
        'billing_address',
        'shipping_address',
        'customer_email',
        'customer_note',
        'ip_address',
    ];
    protected static array $casts = [
        'billing_address' => 'json',
        'shipping_address' => 'json',
    ];

    /**
     * Get all items belonging to this order.
     */
    public function items(): array
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    /**
     * Get the customer who placed this order.
     */
    public function customer(): ?array
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get the status history entries for this order.
     */
    public function statusHistory(): array
    {
        $db = Database::getInstance();

        return $db->table('order_status_history')
            ->where('order_id', $this->getId())
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    /**
     * Add a status history entry to this order.
     */
    public function addHistory(string $status, string $comment = '', bool $notify = false): void
    {
        $db = Database::getInstance();

        $db->table('order_status_history')->insert([
            'order_id' => $this->getId(),
            'status' => $status,
            'comment' => $comment,
            'is_customer_notified' => $notify ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->status = $status;
        static::query()
            ->where(static::$primaryKey, $this->getId())
            ->update([
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
    }

    /**
     * Generate a unique order number.
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $timestamp = date('Ymd');
        $random = strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));

        return "{$prefix}-{$timestamp}-{$random}";
    }
}
