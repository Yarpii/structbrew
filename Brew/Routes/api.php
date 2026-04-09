<?php

declare(strict_types=1);

/** @var Brew\Core\App $this */

// ─── Storefront API ──────────────────────────────────────────

// Products
$this->get('/api/v1/products', ['Brew\Controllers\Storefront\ApiController', 'products']);
$this->get('/api/v1/products/{slug}', ['Brew\Controllers\Storefront\ApiController', 'product']);
$this->get('/api/v1/search', ['Brew\Controllers\Storefront\ApiController', 'search']);

// Categories
$this->get('/api/v1/categories', ['Brew\Controllers\Storefront\ApiController', 'categories']);

// Cart
$this->get('/api/v1/cart', ['Brew\Controllers\Storefront\ApiController', 'getCart']);
$this->post('/api/v1/cart/items', ['Brew\Controllers\Storefront\ApiController', 'addToCart']);
$this->put('/api/v1/cart/items/{id}', ['Brew\Controllers\Storefront\ApiController', 'updateCartItem']);
$this->delete('/api/v1/cart/items/{id}', ['Brew\Controllers\Storefront\ApiController', 'removeCartItem']);
$this->post('/api/v1/cart/coupon', ['Brew\Controllers\Storefront\ApiController', 'applyCoupon']);

// Checkout
$this->post('/api/v1/checkout', ['Brew\Controllers\Storefront\ApiController', 'checkout']);

// Auth
$this->post('/api/v1/auth/register', ['Brew\Controllers\Storefront\ApiController', 'register']);
$this->post('/api/v1/auth/login', ['Brew\Controllers\Storefront\ApiController', 'login']);

// Account
$this->get('/api/v1/account', ['Brew\Controllers\Storefront\ApiController', 'account']);
$this->get('/api/v1/account/orders', ['Brew\Controllers\Storefront\ApiController', 'accountOrders']);

// Store Config
$this->get('/api/v1/store/config', ['Brew\Controllers\Storefront\ApiController', 'storeConfig']);
$this->get('/api/v1/store/translations', ['Brew\Controllers\Storefront\ApiController', 'storeTranslations']);

// Vehicles (for fitment finder)
$this->get('/api/v1/vehicles', ['Brew\Controllers\Storefront\ApiController', 'vehicles']);
$this->get('/api/v1/vehicles/{id}/products', ['Brew\Controllers\Storefront\ApiController', 'vehicleProducts']);
