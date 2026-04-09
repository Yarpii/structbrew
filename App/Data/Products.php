<?php
declare(strict_types=1);

namespace App\Data;

use App\Core\Database;
use App\Core\StoreResolver;

final class Products
{
    public static function all(): array
    {
        $db = Database::getInstance();
        $rows = $db->table('products')
            ->where('is_active', 1)
            ->orderBy('id', 'DESC')
            ->get();

        return self::hydrateProducts($rows);
    }

    public static function find(int $id): ?array
    {
        $row = Database::getInstance()->table('products')->where('id', $id)->first();
        if (!$row || (int) ($row['is_active'] ?? 0) !== 1) {
            return null;
        }

        $products = self::hydrateProducts([$row]);
        return $products[0] ?? null;
    }

    public static function findBySlug(string $slug): ?array
    {
        $row = Database::getInstance()->table('products')->where('slug', $slug)->first();
        if (!$row || (int) ($row['is_active'] ?? 0) !== 1) {
            return null;
        }

        $products = self::hydrateProducts([$row]);
        return $products[0] ?? null;
    }

    public static function byCategory(string $category): array
    {
        $db = Database::getInstance();
        $categoryRow = $db->table('categories')->where('slug', $category)->first();
        if (!$categoryRow) {
            return [];
        }

        $allCategories = $db->table('categories')->get();
        $childrenByParent = [];
        foreach ($allCategories as $cat) {
            $parent = $cat['parent_id'] !== null ? (int) $cat['parent_id'] : 0;
            $childrenByParent[$parent][] = (int) $cat['id'];
        }

        $categoryIds = [];
        $stack = [(int) $categoryRow['id']];
        while (!empty($stack)) {
            $id = array_pop($stack);
            if (in_array($id, $categoryIds, true)) {
                continue;
            }
            $categoryIds[] = $id;
            foreach ($childrenByParent[$id] ?? [] as $childId) {
                $stack[] = $childId;
            }
        }

        if (empty($categoryIds)) {
            return [];
        }

        $productLinks = $db->table('product_categories')
            ->whereIn('category_id', $categoryIds)
            ->get();

        $productIds = array_values(array_unique(array_map(static fn(array $r): int => (int) $r['product_id'], $productLinks)));
        if (empty($productIds)) {
            return [];
        }

        $products = $db->table('products')
            ->whereIn('id', $productIds)
            ->where('is_active', 1)
            ->orderBy('id', 'DESC')
            ->get();

        return self::hydrateProducts($products);
    }

    public static function featured(int $limit = 8): array
    {
        $db = Database::getInstance();

        $rows = $db->table('products')
            ->where('is_active', 1)
            ->where('is_featured', 1)
            ->orderBy('id', 'DESC')
            ->limit($limit)
            ->get();

        if (count($rows) < $limit) {
            $missing = $limit - count($rows);
            $fallback = $db->table('products')
                ->where('is_active', 1)
                ->orderBy('id', 'DESC')
                ->limit($limit + 10)
                ->get();

            $existingIds = array_map(static fn(array $r): int => (int) $r['id'], $rows);
            foreach ($fallback as $item) {
                if (count($rows) >= $limit) {
                    break;
                }
                if (!in_array((int) $item['id'], $existingIds, true)) {
                    $rows[] = $item;
                    $existingIds[] = (int) $item['id'];
                }
            }
        }

        return self::hydrateProducts($rows);
    }

    public static function onSale(): array
    {
        $db = Database::getInstance();
        $rows = $db->table('products')
            ->where('is_active', 1)
            ->orderBy('id', 'DESC')
            ->get();

        $products = self::hydrateProducts($rows);
        return array_values(array_filter($products, static fn(array $p): bool => $p['sale_price'] !== null && $p['sale_price'] < $p['price']));
    }

    public static function categories(): array
    {
        $db = Database::getInstance();
        $storeViewId = self::storeViewId();

        $categories = $db->table('categories')
            ->whereNull('parent_id')
            ->where('is_active', 1)
            ->orderBy('id', 'ASC')
            ->get();

        $result = [];
        foreach ($categories as $category) {
            $translation = $db->table('category_translations')
                ->where('category_id', (int) $category['id'])
                ->where('store_view_id', $storeViewId)
                ->first();

            if (!$translation) {
                $translation = $db->table('category_translations')
                    ->where('category_id', (int) $category['id'])
                    ->first();
            }

            $result[(string) $category['slug']] = (string) ($translation['name'] ?? $category['slug']);
        }

        return $result;
    }

    public static function search(string $query): array
    {
        $q = trim($query);
        if ($q === '') {
            return self::all();
        }

        $db = Database::getInstance();
        $storeViewId = self::storeViewId();

        $sql = "
            SELECT DISTINCT p.*
            FROM products p
            LEFT JOIN product_translations pt ON pt.product_id = p.id AND pt.store_view_id = :sv
            WHERE p.is_active = 1
              AND (
                    p.sku LIKE :q1
                 OR p.slug LIKE :q2
                 OR pt.name LIKE :q3
                 OR pt.short_description LIKE :q4
                 OR pt.description LIKE :q5
              )
            ORDER BY p.id DESC
        ";

        $rows = $db->raw($sql, [
            ':sv' => $storeViewId,
            ':q1' => "%{$q}%",
            ':q2' => "%{$q}%",
            ':q3' => "%{$q}%",
            ':q4' => "%{$q}%",
            ':q5' => "%{$q}%",
        ])->fetchAll();

        return self::hydrateProducts($rows);
    }

    public static function related(int $productId, int $limit = 4): array
    {
        $product = self::find($productId);
        if (!$product) {
            return [];
        }

        $candidates = self::byCategory($product['category']);
        $related = array_values(array_filter($candidates, static fn(array $p): bool => (int) $p['id'] !== $productId));

        return array_slice($related, 0, $limit);
    }

    public static function newArrivals(int $limit = 4): array
    {
        $rows = Database::getInstance()->table('products')
            ->where('is_active', 1)
            ->orderBy('id', 'DESC')
            ->limit($limit)
            ->get();

        return self::hydrateProducts($rows);
    }

    public static function trending(int $limit = 4): array
    {
        $rows = Database::getInstance()->table('products')
            ->where('is_active', 1)
            ->orderBy('stock_qty', 'DESC')
            ->orderBy('id', 'DESC')
            ->limit($limit)
            ->get();

        return self::hydrateProducts($rows);
    }

    private static function hydrateProducts(array $rows): array
    {
        $db = Database::getInstance();
        $storeViewId = self::storeViewId();
        $output = [];

        foreach ($rows as $row) {
            $productId = (int) $row['id'];

            $translation = $db->table('product_translations')
                ->where('product_id', $productId)
                ->where('store_view_id', $storeViewId)
                ->first();
            if (!$translation) {
                $translation = $db->table('product_translations')
                    ->where('product_id', $productId)
                    ->first();
            }

            $pricing = $db->table('product_pricing')
                ->where('product_id', $productId)
                ->where('store_view_id', $storeViewId)
                ->first();
            if (!$pricing) {
                $pricing = $db->table('product_pricing')
                    ->where('product_id', $productId)
                    ->first();
            }

            $categoryLink = $db->table('product_categories')
                ->where('product_id', $productId)
                ->orderBy('position', 'ASC')
                ->first();

            $categorySlug = 'uncategorized';
            if ($categoryLink) {
                $category = $db->table('categories')->where('id', (int) $categoryLink['category_id'])->first();
                if ($category) {
                    $categorySlug = (string) $category['slug'];
                }
            }

            $price = isset($pricing['price']) ? (float) $pricing['price'] : 0.0;
            $salePrice = isset($pricing['sale_price']) && $pricing['sale_price'] !== null ? (float) $pricing['sale_price'] : null;
            $inStock = ((int) ($row['manage_stock'] ?? 1) === 0) || ((int) ($row['stock_qty'] ?? 0) > 0);

            $badge = null;
            if (!$inStock) {
                $badge = 'Out of Stock';
            } elseif ($salePrice !== null && $salePrice < $price) {
                $badge = 'Sale';
            } elseif ((int) ($row['is_featured'] ?? 0) === 1) {
                $badge = 'Popular';
            }

            $imageRows = $db->table('product_images')
                ->where('product_id', $productId)
                ->orderBy('position', 'ASC')
                ->get();

            $images = [];
            foreach ($imageRows as $img) {
                if (empty($img['path'])) {
                    continue;
                }
                $images[] = [
                    'url' => (string) $img['path'],
                    'alt' => (string) ($img['alt_text'] ?? ($translation['name'] ?? $row['slug'])),
                    'is_main' => (int) ($img['is_main'] ?? 0) === 1,
                ];
            }

            $primaryImage = null;
            foreach ($images as $img) {
                if ($img['is_main']) {
                    $primaryImage = $img['url'];
                    break;
                }
            }
            if ($primaryImage === null && !empty($images)) {
                $primaryImage = $images[0]['url'];
            }

            $attributeRows = $db->table('product_attributes')
                ->where('product_id', $productId)
                ->get();

            $attributeMap = [];
            foreach ($attributeRows as $attributeRow) {
                $key = (string) ($attributeRow['attribute_key'] ?? '');
                if ($key === '') {
                    continue;
                }
                $sv = $attributeRow['store_view_id'] ?? null;
                if ($sv !== null && (int) $sv !== $storeViewId) {
                    continue;
                }
                $attributeMap[$key] = (string) ($attributeRow['attribute_value'] ?? '');
            }

            $docs = [];
            $videos = [];
            $specs = [];
            $searchAttributes = [];
            foreach ($attributeMap as $key => $value) {
                if ($value === '') {
                    continue;
                }
                if (str_starts_with($key, 'doc_')) {
                    $docs[] = [
                        'label' => ucwords(str_replace('_', ' ', substr($key, 4))),
                        'url' => $value,
                    ];
                } elseif (str_starts_with($key, 'video_')) {
                    $videos[] = [
                        'label' => ucwords(str_replace('_', ' ', substr($key, 6))),
                        'url' => $value,
                    ];
                } elseif (str_starts_with($key, 'spec_')) {
                    $specKey = (string) substr($key, 5);
                    $specs[ucwords(str_replace('_', ' ', $specKey))] = $value;
                    $searchAttributes[$specKey] = $value;
                }
            }

            $features = [];
            if (!empty($attributeMap['feature_list'])) {
                $parts = preg_split('/\s*\|\s*/', $attributeMap['feature_list']) ?: [];
                foreach ($parts as $part) {
                    if ($part !== '') {
                        $features[] = $part;
                    }
                }
            }

            $description = (string) ($translation['description'] ?? $translation['short_description'] ?? '');
            if (!empty($attributeMap['description_long'])) {
                $description = $attributeMap['description_long'];
            }

            $fitmentNotes = (string) ($attributeMap['fitment_notes'] ?? '');
            $installationNotes = (string) ($attributeMap['installation_notes'] ?? '');

            $output[] = [
                'id' => $productId,
                'slug' => (string) $row['slug'],
                'name' => (string) ($translation['name'] ?? $row['slug']),
                'price' => $price,
                'sale_price' => $salePrice,
                'category' => $categorySlug,
                'description' => $description,
                'short_description' => (string) ($translation['short_description'] ?? ''),
                'features' => $features,
                'sku' => (string) $row['sku'],
                'oem_number' => (string) ($row['oem_number'] ?? ''),
                'weight' => $row['weight'] !== null ? (float) $row['weight'] : null,
                'stock_qty' => (int) ($row['stock_qty'] ?? 0),
                'manage_stock' => (int) ($row['manage_stock'] ?? 1),
                'in_stock' => $inStock,
                'rating' => 4.5,
                'reviews' => 0,
                'badge' => $badge,
                'image' => $primaryImage,
                'images' => $images,
                'documents' => $docs,
                'videos' => $videos,
                'specs' => $specs,
                'search_attributes' => $searchAttributes,
                'fitment_notes' => $fitmentNotes,
                'installation_notes' => $installationNotes,
            ];
        }

        return $output;
    }

    private static function storeViewId(): int
    {
        $current = StoreResolver::storeViewId();
        if ($current !== null) {
            return $current;
        }

        $defaultView = Database::getInstance()->table('store_views')
            ->where('is_default', 1)
            ->first();

        return (int) ($defaultView['id'] ?? 1);
    }
}
