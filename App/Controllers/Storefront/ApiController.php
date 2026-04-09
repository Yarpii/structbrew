<?php

declare(strict_types=1);

namespace App\Controllers\Storefront;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\StoreResolver;
use App\Core\Translator;
use App\Core\Validator;

class ApiController extends Controller
{
    private Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    // ─── Products ────────────────────────────────────────────

    public function products(Request $request): Response
    {
        $storeViewId = StoreResolver::storeViewId();
        $page = (int) ($request->input('page', 1));
        $perPage = min((int) ($request->input('per_page', 20)), 100);
        $category = $request->input('category');
        $brand = $request->input('brand');
        $sort = $request->input('sort', 'newest');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');

        $query = $this->db->table('products')
            ->select('products.id', 'products.sku', 'products.slug', 'products.brand_id',
                     'products.stock_qty', 'products.is_featured', 'products.oem_number',
                     'pt.name', 'pt.short_description', 'pt.url_key',
                     'pp.price', 'pp.sale_price', 'pp.currency_code',
                     'b.name as brand_name',
                     'pi.path as image')
            ->leftJoin('product_translations', 'products.id', '=', 'pt.product_id')
            ->leftJoin('product_pricing', 'products.id', '=', 'pp.product_id')
            ->leftJoin('brands', 'products.brand_id', '=', 'b.id')
            ->leftJoin('product_images', 'products.id', '=', 'pi.product_id')
            ->where('products.is_active', 1);

        // Alias workaround: we use raw where for store view joins
        if ($storeViewId) {
            $query->whereRaw('pt.store_view_id = :sv_pt', [':sv_pt' => $storeViewId]);
            $query->whereRaw('pp.store_view_id = :sv_pp', [':sv_pp' => $storeViewId]);
        }

        $query->whereRaw('(pi.is_main = 1 OR pi.id IS NULL)');

        if ($category) {
            $query->whereRaw('products.id IN (SELECT product_id FROM product_categories WHERE category_id = :cat_id)', [':cat_id' => $category]);
        }

        if ($brand) {
            $query->where('products.brand_id', $brand);
        }

        if ($minPrice) {
            $query->whereRaw('pp.price >= :min_p', [':min_p' => $minPrice]);
        }
        if ($maxPrice) {
            $query->whereRaw('pp.price <= :max_p', [':max_p' => $maxPrice]);
        }

        match ($sort) {
            'price_asc' => $query->orderBy('pp.price', 'ASC'),
            'price_desc' => $query->orderBy('pp.price', 'DESC'),
            'name_asc' => $query->orderBy('pt.name', 'ASC'),
            'name_desc' => $query->orderBy('pt.name', 'DESC'),
            'oldest' => $query->orderBy('products.created_at', 'ASC'),
            default => $query->orderBy('products.created_at', 'DESC'),
        };

        $result = $query->paginate($perPage, $page);

        return $this->json([
            'success' => true,
            'data' => $result['data'],
            'meta' => [
                'total' => $result['total'],
                'per_page' => $result['per_page'],
                'current_page' => $result['current_page'],
                'last_page' => $result['last_page'],
            ],
        ]);
    }

    public function product(Request $request, string $slug): Response
    {
        $storeViewId = StoreResolver::storeViewId();

        $product = $this->db->table('products')
            ->where('slug', $slug)
            ->where('is_active', 1)
            ->first();

        if (!$product) {
            return $this->json(['success' => false, 'error' => 'Product not found'], 404);
        }

        // Translation
        $translation = $this->db->table('product_translations')
            ->where('product_id', $product['id'])
            ->where('store_view_id', $storeViewId)
            ->first();

        // Pricing
        $pricing = $this->db->table('product_pricing')
            ->where('product_id', $product['id'])
            ->where('store_view_id', $storeViewId)
            ->first();

        // Images
        $images = $this->db->table('product_images')
            ->where('product_id', $product['id'])
            ->orderBy('position', 'ASC')
            ->get();

        // Categories
        $categories = $this->db->table('product_categories')
            ->select('categories.id', 'categories.slug', 'ct.name')
            ->join('categories', 'product_categories.category_id', '=', 'categories.id')
            ->leftJoin('category_translations', 'categories.id', '=', 'ct.category_id')
            ->where('product_categories.product_id', $product['id'])
            ->get();

        // Compatible vehicles
        $vehicles = $this->db->table('product_vehicles')
            ->select('vehicles.*', 'b.name as brand_name', 'product_vehicles.notes')
            ->join('vehicles', 'product_vehicles.vehicle_id', '=', 'vehicles.id')
            ->leftJoin('brands', 'vehicles.brand_id', '=', 'b.id')
            ->where('product_vehicles.product_id', $product['id'])
            ->get();

        // Brand
        $brand = null;
        if ($product['brand_id']) {
            $brand = $this->db->table('brands')->where('id', $product['brand_id'])->first();
        }

        // Attributes
        $attributes = $this->db->table('product_attributes')
            ->where('product_id', $product['id'])
            ->get();

        return $this->json([
            'success' => true,
            'data' => array_merge($product, [
                'translation' => $translation,
                'pricing' => $pricing,
                'images' => $images,
                'categories' => $categories,
                'vehicles' => $vehicles,
                'brand' => $brand,
                'attributes' => $attributes,
                'in_stock' => $product['stock_qty'] > 0,
            ]),
        ]);
    }

    public function search(Request $request): Response
    {
        $q = trim($request->input('q', ''));
        if (strlen($q) < 2) {
            return $this->json(['success' => true, 'data' => [], 'meta' => ['total' => 0]]);
        }

        $storeViewId = StoreResolver::storeViewId();
        $page = (int) ($request->input('page', 1));

        $results = $this->db->table('products')
            ->select('products.id', 'products.sku', 'products.slug', 'products.oem_number',
                     'pt.name', 'pt.short_description',
                     'pp.price', 'pp.sale_price', 'pp.currency_code')
            ->leftJoin('product_translations', 'products.id', '=', 'pt.product_id')
            ->leftJoin('product_pricing', 'products.id', '=', 'pp.product_id')
            ->where('products.is_active', 1)
            ->whereRaw('(pt.name LIKE :q1 OR products.sku LIKE :q2 OR products.oem_number LIKE :q3)', [
                ':q1' => "%{$q}%",
                ':q2' => "%{$q}%",
                ':q3' => "%{$q}%",
            ])
            ->paginate(20, $page);

        return $this->json([
            'success' => true,
            'data' => $results['data'],
            'meta' => [
                'total' => $results['total'],
                'current_page' => $results['current_page'],
                'last_page' => $results['last_page'],
                'query' => $q,
            ],
        ]);
    }

    // ─── Categories ──────────────────────────────────────────

    public function categories(): Response
    {
        $storeViewId = StoreResolver::storeViewId();

        $categories = $this->db->table('categories')
            ->select('categories.id', 'categories.slug', 'categories.parent_id', 'categories.position', 'categories.image',
                     'ct.name', 'ct.description')
            ->leftJoin('category_translations', 'categories.id', '=', 'ct.category_id')
            ->where('categories.is_active', 1)
            ->whereRaw('(ct.store_view_id = :sv OR ct.store_view_id IS NULL)', [':sv' => $storeViewId])
            ->orderBy('categories.position', 'ASC')
            ->get();

        // Build tree
        $tree = $this->buildTree($categories);

        return $this->json(['success' => true, 'data' => $tree]);
    }

    private function buildTree(array $items, ?int $parentId = null): array
    {
        $tree = [];
        foreach ($items as $item) {
            if ($item['parent_id'] == $parentId) {
                $item['children'] = $this->buildTree($items, (int) $item['id']);
                $tree[] = $item;
            }
        }
        return $tree;
    }

    // ─── Cart ────────────────────────────────────────────────

    public function getCart(): Response
    {
        $cart = $this->getOrCreateCart();
        $items = $this->db->table('cart_items')
            ->select('cart_items.*', 'pt.name', 'pi.path as image')
            ->leftJoin('product_translations', 'cart_items.product_id', '=', 'pt.product_id')
            ->leftJoin('product_images', 'cart_items.product_id', '=', 'pi.product_id')
            ->where('cart_items.cart_id', $cart['id'])
            ->whereRaw('(pi.is_main = 1 OR pi.id IS NULL)')
            ->get();

        return $this->json([
            'success' => true,
            'data' => [
                'id' => $cart['id'],
                'items' => $items,
                'subtotal' => $cart['subtotal'],
                'tax_amount' => $cart['tax_amount'],
                'discount_amount' => $cart['discount_amount'],
                'grand_total' => $cart['grand_total'],
                'coupon_code' => $cart['coupon_code'],
                'currency_code' => $cart['currency_code'],
                'item_count' => count($items),
            ],
        ]);
    }

    public function addToCart(Request $request): Response
    {
        $productId = (int) $request->input('product_id');
        $qty = max(1, (int) ($request->input('qty', 1)));

        $product = $this->db->table('products')->where('id', $productId)->where('is_active', 1)->first();
        if (!$product) {
            return $this->json(['success' => false, 'error' => 'Product not found'], 404);
        }

        if ($product['manage_stock'] && $product['stock_qty'] < $qty) {
            return $this->json(['success' => false, 'error' => 'Insufficient stock'], 400);
        }

        $storeViewId = StoreResolver::storeViewId();
        $pricing = $this->db->table('product_pricing')
            ->where('product_id', $productId)
            ->where('store_view_id', $storeViewId)
            ->first();

        $price = (float) ($pricing['sale_price'] ?? $pricing['price'] ?? 0);
        $cart = $this->getOrCreateCart();

        // Check if already in cart
        $existing = $this->db->table('cart_items')
            ->where('cart_id', $cart['id'])
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $newQty = (int) $existing['qty'] + $qty;
            $this->db->table('cart_items')
                ->where('id', $existing['id'])
                ->update([
                    'qty' => $newQty,
                    'row_total' => $price * $newQty,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        } else {
            $this->db->table('cart_items')->insert([
                'cart_id' => $cart['id'],
                'product_id' => $productId,
                'qty' => $qty,
                'price' => $price,
                'row_total' => $price * $qty,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->recalculateCart($cart['id']);

        return $this->json(['success' => true, 'message' => 'Item added to cart']);
    }

    public function updateCartItem(Request $request, string $id): Response
    {
        $qty = max(1, (int) $request->input('qty', 1));
        $cart = $this->getOrCreateCart();

        $item = $this->db->table('cart_items')
            ->where('id', $id)
            ->where('cart_id', $cart['id'])
            ->first();

        if (!$item) {
            return $this->json(['success' => false, 'error' => 'Item not found'], 404);
        }

        $this->db->table('cart_items')
            ->where('id', $id)
            ->update([
                'qty' => $qty,
                'row_total' => (float) $item['price'] * $qty,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $this->recalculateCart($cart['id']);

        return $this->json(['success' => true, 'message' => 'Cart updated']);
    }

    public function removeCartItem(Request $request, string $id): Response
    {
        $cart = $this->getOrCreateCart();

        $this->db->table('cart_items')
            ->where('id', $id)
            ->where('cart_id', $cart['id'])
            ->delete();

        $this->recalculateCart($cart['id']);

        return $this->json(['success' => true, 'message' => 'Item removed']);
    }

    public function applyCoupon(Request $request): Response
    {
        $code = trim($request->input('code', ''));
        $cart = $this->getOrCreateCart();

        $coupon = $this->db->table('coupons')
            ->where('code', $code)
            ->where('is_active', 1)
            ->first();

        if (!$coupon) {
            return $this->json(['success' => false, 'error' => 'Invalid coupon code'], 400);
        }

        $priceRule = $this->db->table('price_rules')->where('id', $coupon['price_rule_id'])->first();
        if (!$priceRule || !$priceRule['is_active']) {
            return $this->json(['success' => false, 'error' => 'Coupon is not valid'], 400);
        }

        // Check usage limits
        if ($coupon['usage_limit'] && $coupon['times_used'] >= $coupon['usage_limit']) {
            return $this->json(['success' => false, 'error' => 'Coupon usage limit reached'], 400);
        }

        $this->db->table('carts')->where('id', $cart['id'])->update(['coupon_code' => $code]);
        $this->recalculateCart($cart['id']);

        return $this->json(['success' => true, 'message' => 'Coupon applied']);
    }

    // ─── Checkout ────────────────────────────────────────────

    public function checkout(Request $request): Response
    {
        $validator = Validator::make($request->body(), [
            'billing_first_name' => 'required|string',
            'billing_last_name' => 'required|string',
            'billing_street_1' => 'required|string',
            'billing_city' => 'required|string',
            'billing_postcode' => 'required|string',
            'billing_country_code' => 'required|string|min:2|max:2',
            'customer_email' => 'required|email',
            'payment_method' => 'required|string',
            'shipping_method' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $cart = $this->getOrCreateCart();
        $items = $this->db->table('cart_items')->where('cart_id', $cart['id'])->get();

        if (empty($items)) {
            return $this->json(['success' => false, 'error' => 'Cart is empty'], 400);
        }

        $body = $request->body();

        $billingAddress = [
            'first_name' => $body['billing_first_name'],
            'last_name' => $body['billing_last_name'],
            'company' => $body['billing_company'] ?? '',
            'street_1' => $body['billing_street_1'],
            'street_2' => $body['billing_street_2'] ?? '',
            'city' => $body['billing_city'],
            'state' => $body['billing_state'] ?? '',
            'postcode' => $body['billing_postcode'],
            'country_code' => $body['billing_country_code'],
            'phone' => $body['billing_phone'] ?? '',
        ];

        $shippingAddress = !empty($body['shipping_same_as_billing']) ? $billingAddress : [
            'first_name' => $body['shipping_first_name'] ?? $body['billing_first_name'],
            'last_name' => $body['shipping_last_name'] ?? $body['billing_last_name'],
            'company' => $body['shipping_company'] ?? '',
            'street_1' => $body['shipping_street_1'] ?? $body['billing_street_1'],
            'street_2' => $body['shipping_street_2'] ?? '',
            'city' => $body['shipping_city'] ?? $body['billing_city'],
            'state' => $body['shipping_state'] ?? '',
            'postcode' => $body['shipping_postcode'] ?? $body['billing_postcode'],
            'country_code' => $body['shipping_country_code'] ?? $body['billing_country_code'],
            'phone' => $body['shipping_phone'] ?? '',
        ];

        $orderNumber = 'SD-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));

        $this->db->beginTransaction();

        try {
            $orderId = $this->db->table('orders')->insert([
                'order_number' => $orderNumber,
                'customer_id' => Auth::customerId(),
                'store_view_id' => StoreResolver::storeViewId(),
                'status' => 'pending',
                'currency_code' => $cart['currency_code'],
                'subtotal' => $cart['subtotal'],
                'tax_amount' => $cart['tax_amount'],
                'shipping_amount' => $cart['shipping_amount'] ?? 0,
                'discount_amount' => $cart['discount_amount'],
                'grand_total' => $cart['grand_total'],
                'coupon_code' => $cart['coupon_code'],
                'shipping_method' => $body['shipping_method'],
                'payment_method' => $body['payment_method'],
                'billing_address' => json_encode($billingAddress),
                'shipping_address' => json_encode($shippingAddress),
                'customer_email' => $body['customer_email'],
                'customer_note' => $body['customer_note'] ?? null,
                'ip_address' => $request->ip(),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // Create order items
            foreach ($items as $item) {
                $product = $this->db->table('products')->where('id', $item['product_id'])->first();
                $translation = $this->db->table('product_translations')
                    ->where('product_id', $item['product_id'])
                    ->where('store_view_id', StoreResolver::storeViewId())
                    ->first();

                $this->db->table('order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'sku' => $product['sku'] ?? '',
                    'name' => $translation['name'] ?? $product['sku'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'row_total' => $item['row_total'],
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                // Decrement stock
                if ($product && $product['manage_stock']) {
                    $this->db->table('products')
                        ->where('id', $item['product_id'])
                        ->update(['stock_qty' => max(0, $product['stock_qty'] - $item['qty'])]);
                }
            }

            // Add status history
            $this->db->table('order_status_history')->insert([
                'order_id' => $orderId,
                'status' => 'pending',
                'comment' => 'Order placed',
                'is_customer_notified' => 1,
                'created_by' => 'system',
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // Clear cart
            $this->db->table('cart_items')->where('cart_id', $cart['id'])->delete();
            $this->db->table('carts')->where('id', $cart['id'])->update([
                'subtotal' => 0, 'tax_amount' => 0, 'discount_amount' => 0, 'grand_total' => 0,
                'coupon_code' => null, 'updated_at' => date('Y-m-d H:i:s'),
            ]);

            // Increment coupon usage
            if ($cart['coupon_code']) {
                $this->db->table('coupons')
                    ->where('code', $cart['coupon_code'])
                    ->whereRaw('times_used = times_used + 1');
            }

            $this->db->commit();

            return $this->json([
                'success' => true,
                'data' => [
                    'order_id' => $orderId,
                    'order_number' => $orderNumber,
                ],
                'message' => 'Order placed successfully',
            ]);

        } catch (\Throwable $e) {
            $this->db->rollback();
            return $this->json(['success' => false, 'error' => 'Failed to place order'], 500);
        }
    }

    // ─── Auth ────────────────────────────────────────────────

    public function register(Request $request): Response
    {
        $validator = Validator::make($request->body(), [
            'email' => 'required|email|unique:customers,email',
            'password' => 'required|min:8',
            'first_name' => 'required|string|min:1',
            'last_name' => 'required|string|min:1',
        ]);

        if ($validator->fails()) {
            return $this->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $body = $request->body();
        $customerId = $this->db->table('customers')->insert([
            'store_view_id' => StoreResolver::storeViewId(),
            'email' => $body['email'],
            'password_hash' => Auth::hashPassword($body['password']),
            'first_name' => $body['first_name'],
            'last_name' => $body['last_name'],
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $customer = $this->db->table('customers')->where('id', $customerId)->first();
        Auth::loginCustomer($customer);

        return $this->json([
            'success' => true,
            'message' => 'Account created successfully',
            'data' => ['id' => $customerId, 'email' => $body['email']],
        ]);
    }

    public function login(Request $request): Response
    {
        $email = $request->input('email', '');
        $password = $request->input('password', '');

        if (Auth::attempt($email, $password)) {
            return $this->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => ['email' => $email],
            ]);
        }

        return $this->json(['success' => false, 'error' => 'Invalid credentials'], 401);
    }

    // ─── Account ─────────────────────────────────────────────

    public function account(): Response
    {
        $customer = Auth::customer();
        if (!$customer) {
            return $this->json(['success' => false, 'error' => 'Not authenticated'], 401);
        }

        unset($customer['password_hash']);
        return $this->json(['success' => true, 'data' => $customer]);
    }

    public function accountOrders(): Response
    {
        $customerId = Auth::customerId();
        if (!$customerId) {
            return $this->json(['success' => false, 'error' => 'Not authenticated'], 401);
        }

        $orders = $this->db->table('orders')
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'DESC')
            ->get();

        return $this->json(['success' => true, 'data' => $orders]);
    }

    // ─── Store Config ────────────────────────────────────────

    public function storeConfig(): Response
    {
        return $this->json([
            'success' => true,
            'data' => [
                'locale' => StoreResolver::locale(),
                'language' => StoreResolver::language(),
                'country' => StoreResolver::country(),
                'currency' => [
                    'code' => StoreResolver::currency(),
                    'symbol' => StoreResolver::currencySymbol(),
                ],
                'theme' => StoreResolver::theme(),
                'store_view_id' => StoreResolver::storeViewId(),
                'store_name' => StoreResolver::storeView()['name'] ?? 'Store',
            ],
        ]);
    }

    public function storeTranslations(): Response
    {
        return $this->json([
            'success' => true,
            'data' => Translator::allForLocale(),
        ]);
    }

    // ─── Vehicles (Fitment Finder) ───────────────────────────

    public function vehicles(Request $request): Response
    {
        $brandId = $request->input('brand_id');

        $query = $this->db->table('vehicles')
            ->select('vehicles.*', 'b.name as brand_name')
            ->leftJoin('brands', 'vehicles.brand_id', '=', 'b.id')
            ->where('vehicles.is_active', 1);

        if ($brandId) {
            $query->where('vehicles.brand_id', $brandId);
        }

        $vehicles = $query->orderBy('b.name', 'ASC')->orderBy('vehicles.model', 'ASC')->get();

        return $this->json(['success' => true, 'data' => $vehicles]);
    }

    public function vehicleProducts(Request $request, string $id): Response
    {
        $storeViewId = StoreResolver::storeViewId();

        $products = $this->db->table('product_vehicles')
            ->select('products.id', 'products.sku', 'products.slug',
                     'pt.name', 'pp.price', 'pp.sale_price', 'pp.currency_code',
                     'pi.path as image', 'product_vehicles.notes')
            ->join('products', 'product_vehicles.product_id', '=', 'products.id')
            ->leftJoin('product_translations', 'products.id', '=', 'pt.product_id')
            ->leftJoin('product_pricing', 'products.id', '=', 'pp.product_id')
            ->leftJoin('product_images', 'products.id', '=', 'pi.product_id')
            ->where('product_vehicles.vehicle_id', $id)
            ->where('products.is_active', 1)
            ->whereRaw('(pi.is_main = 1 OR pi.id IS NULL)')
            ->get();

        return $this->json(['success' => true, 'data' => $products]);
    }

    // ─── Helpers ─────────────────────────────────────────────

    private function getOrCreateCart(): array
    {
        $sessionId = Session::id();
        $customerId = Auth::customerId();

        $cart = null;
        if ($customerId) {
            $cart = $this->db->table('carts')->where('customer_id', $customerId)->first();
        }
        if (!$cart) {
            $cart = $this->db->table('carts')->where('session_id', $sessionId)->first();
        }

        if (!$cart) {
            $cartId = $this->db->table('carts')->insert([
                'customer_id' => $customerId,
                'store_view_id' => StoreResolver::storeViewId(),
                'session_id' => $sessionId,
                'currency_code' => StoreResolver::currency(),
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'grand_total' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $cart = $this->db->table('carts')->where('id', $cartId)->first();
        }

        return $cart;
    }

    private function recalculateCart(int $cartId): void
    {
        $items = $this->db->table('cart_items')->where('cart_id', $cartId)->get();
        $subtotal = array_sum(array_column($items, 'row_total'));
        $taxRate = StoreResolver::taxRate();
        $taxAmount = round($subtotal * ($taxRate / 100), 2);

        // Check discount
        $cart = $this->db->table('carts')->where('id', $cartId)->first();
        $discountAmount = 0.0;

        if (!empty($cart['coupon_code'])) {
            $coupon = $this->db->table('coupons')->where('code', $cart['coupon_code'])->first();
            if ($coupon) {
                $rule = $this->db->table('price_rules')->where('id', $coupon['price_rule_id'])->first();
                if ($rule && $rule['is_active']) {
                    if ($rule['type'] === 'percentage') {
                        $discountAmount = round($subtotal * ((float) $rule['value'] / 100), 2);
                    } else {
                        $discountAmount = min((float) $rule['value'], $subtotal);
                    }
                }
            }
        }

        $grandTotal = max(0, $subtotal + $taxAmount - $discountAmount);

        $this->db->table('carts')->where('id', $cartId)->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'grand_total' => $grandTotal,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
