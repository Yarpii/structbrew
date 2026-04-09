<?php

declare(strict_types=1);

/** @var App\Core\App $this */

// ─── Storefront API ──────────────────────────────────────────

// Products
$this->get('/api/v1/products', ['App\Controllers\Storefront\ApiController', 'products']);
$this->get('/api/v1/products/{slug}', ['App\Controllers\Storefront\ApiController', 'product']);
$this->get('/api/v1/search', ['App\Controllers\Storefront\ApiController', 'search']);

// Categories
$this->get('/api/v1/categories', ['App\Controllers\Storefront\ApiController', 'categories']);

// Cart
$this->get('/api/v1/cart', ['App\Controllers\Storefront\ApiController', 'getCart']);
$this->post('/api/v1/cart/items', ['App\Controllers\Storefront\ApiController', 'addToCart']);
$this->put('/api/v1/cart/items/{id}', ['App\Controllers\Storefront\ApiController', 'updateCartItem']);
$this->delete('/api/v1/cart/items/{id}', ['App\Controllers\Storefront\ApiController', 'removeCartItem']);
$this->post('/api/v1/cart/coupon', ['App\Controllers\Storefront\ApiController', 'applyCoupon']);

// Checkout
$this->post('/api/v1/checkout', ['App\Controllers\Storefront\ApiController', 'checkout']);

// Auth
$this->post('/api/v1/auth/register', ['App\Controllers\Storefront\ApiController', 'register']);
$this->post('/api/v1/auth/login', ['App\Controllers\Storefront\ApiController', 'login']);

// Account
$this->get('/api/v1/account', ['App\Controllers\Storefront\ApiController', 'account']);
$this->get('/api/v1/account/orders', ['App\Controllers\Storefront\ApiController', 'accountOrders']);

// Store Config
$this->get('/api/v1/store/config', ['App\Controllers\Storefront\ApiController', 'storeConfig']);
$this->get('/api/v1/store/translations', ['App\Controllers\Storefront\ApiController', 'storeTranslations']);

// Vehicles (for fitment finder)
$this->get('/api/v1/vehicles', ['App\Controllers\Storefront\ApiController', 'vehicles']);
$this->get('/api/v1/vehicles/{id}/products', ['App\Controllers\Storefront\ApiController', 'vehicleProducts']);
