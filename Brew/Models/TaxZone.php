<?php

declare(strict_types=1);

namespace Brew\Models;

use Brew\Core\Model;

class TaxZone extends Model
{
    protected static string $table = 'tax_zones';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'name',
        'country_code',
        'state',
        'postcode_pattern',
        'is_active',
    ];
}
