<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class CmsPage extends Model
{
    protected static string $table = 'cms_pages';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'slug',
        'is_active',
    ];

    /**
     * Get the translation for a specific store view (or the first available).
     */
    public function translation(?int $storeViewId = null): ?array
    {
        $db = Database::getInstance();
        $query = $db->table('cms_page_translations')
            ->where('cms_page_id', $this->getId());

        if ($storeViewId !== null) {
            $query->where('store_view_id', $storeViewId);
        }

        return $query->first();
    }
}
