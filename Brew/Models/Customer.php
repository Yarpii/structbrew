<?php

declare(strict_types=1);

namespace Brew\Models;

use Brew\Core\Model;

class Customer extends Model
{
    protected static string $table = 'customers';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'store_view_id',
        'email',
        'password_hash',
        'first_name',
        'last_name',
        'phone',
        'date_of_birth',
        'gender',
        'is_active',
    ];
    protected static array $hidden = [
        'password_hash',
    ];

    /**
     * Get all orders placed by this customer.
     */
    public function orders(): array
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    /**
     * Get all addresses belonging to this customer.
     */
    public function addresses(): array
    {
        return $this->hasMany(Address::class, 'customer_id');
    }

    /**
     * Get the default billing address.
     */
    public function billingAddress(): ?array
    {
        return Address::query()
            ->where('customer_id', $this->getId())
            ->where('type', 'billing')
            ->where('is_default', 1)
            ->first();
    }

    /**
     * Get the default shipping address.
     */
    public function shippingAddress(): ?array
    {
        return Address::query()
            ->where('customer_id', $this->getId())
            ->where('type', 'shipping')
            ->where('is_default', 1)
            ->first();
    }

    /**
     * Get the customer's full name.
     */
    public function fullName(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? ''));
    }
}
