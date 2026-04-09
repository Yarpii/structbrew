<?php

declare(strict_types=1);

namespace Brew\Models;

use Brew\Core\Model;

class Address extends Model
{
    protected static string $table = 'addresses';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'customer_id',
        'type',
        'is_default',
        'first_name',
        'last_name',
        'company',
        'street_1',
        'street_2',
        'city',
        'state',
        'postcode',
        'country_code',
        'phone',
    ];
}
