<?php
declare(strict_types=1);
namespace Brew\Controllers;

use Brew\Core\Controller;
use Brew\Core\Request;
use Brew\Core\Response;
use Brew\Data\Products;
use Throwable;

final class ShopController extends Controller
{
    /**
     * @throws Throwable
     */
    public function index(Request $req): Response
    {
        $category = $req->input('category', '');
        $sort = $req->input('sort', 'default');
        $q = $req->input('q', '');

        $products = $q ? Products::search($q) : ($category ? Products::byCategory($category) : Products::all());

        if ($sort === 'price_asc') usort($products, fn($a, $b) => ($a['sale_price'] ?? $a['price']) <=> ($b['sale_price'] ?? $b['price']));
        if ($sort === 'price_desc') usort($products, fn($a, $b) => ($b['sale_price'] ?? $b['price']) <=> ($a['sale_price'] ?? $a['price']));
        if ($sort === 'rating') usort($products, fn($a, $b) => $b['rating'] <=> $a['rating']);
        if ($sort === 'name') usort($products, fn($a, $b) => $a['name'] <=> $b['name']);

        return $this->view('shop.index', [
            'title'      => $q ? "Search: $q" : ($category ? Products::categories()[$category] ?? 'Shop' : 'Shop'),
            'products'   => $products,
            'categories' => Products::categories(),
            'activeCategory' => $category,
            'activeSort' => $sort,
            'searchQuery' => $q,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function show(Request $req, string $slug): Response
    {
        $product = Products::findBySlug($slug);
        if (!$product) {
            return $this->text('Product not found', 404);
        }
        $related = Products::related($product['id']);
        return $this->view('shop.show', [
            'title'   => $product['name'],
            'product' => $product,
            'related' => $related,
        ]);
    }

    public function apiProducts(Request $req): Response
    {
        $category = $req->input('category', '');
        $products = $category ? Products::byCategory($category) : Products::all();
        return $this->json(['products' => $products, 'total' => count($products)]);
    }
}
