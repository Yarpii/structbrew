<?php

declare(strict_types=1);

/** @var App\Core\App $this */

// ─── Admin Auth (no auth middleware needed) ──────────────────
$this->get('/admin/login', ['App\Controllers\Admin\AuthController', 'showLogin']);
$this->post('/admin/login', ['App\Controllers\Admin\AuthController', 'login']);
$this->post('/admin/logout', ['App\Controllers\Admin\AuthController', 'logout']);

// ─── Dashboard ───────────────────────────────────────────────
$this->get('/admin', ['App\Controllers\Admin\DashboardController', 'index']);

// ─── Products ────────────────────────────────────────────────
$this->get('/admin/products', ['App\Controllers\Admin\ProductController', 'index']);
$this->get('/admin/products/create', ['App\Controllers\Admin\ProductController', 'create']);
$this->post('/admin/products', ['App\Controllers\Admin\ProductController', 'store']);
$this->get('/admin/products/{id}/edit', ['App\Controllers\Admin\ProductController', 'edit']);
$this->post('/admin/products/{id}', ['App\Controllers\Admin\ProductController', 'update']);
$this->post('/admin/products/{id}/delete', ['App\Controllers\Admin\ProductController', 'delete']);

// ─── Categories ──────────────────────────────────────────────
$this->get('/admin/categories', ['App\Controllers\Admin\CategoryController', 'index']);
$this->get('/admin/categories/create', ['App\Controllers\Admin\CategoryController', 'create']);
$this->post('/admin/categories', ['App\Controllers\Admin\CategoryController', 'store']);
$this->get('/admin/categories/{id}/edit', ['App\Controllers\Admin\CategoryController', 'edit']);
$this->post('/admin/categories/{id}', ['App\Controllers\Admin\CategoryController', 'update']);
$this->post('/admin/categories/{id}/delete', ['App\Controllers\Admin\CategoryController', 'delete']);

// ─── Brands ──────────────────────────────────────────────────
$this->get('/admin/brands', ['App\Controllers\Admin\BrandController', 'index']);
$this->get('/admin/brands/create', ['App\Controllers\Admin\BrandController', 'create']);
$this->post('/admin/brands', ['App\Controllers\Admin\BrandController', 'store']);
$this->get('/admin/brands/{id}/edit', ['App\Controllers\Admin\BrandController', 'edit']);
$this->post('/admin/brands/{id}', ['App\Controllers\Admin\BrandController', 'update']);
$this->post('/admin/brands/{id}/delete', ['App\Controllers\Admin\BrandController', 'delete']);

// ─── Vehicles ────────────────────────────────────────────────
$this->get('/admin/vehicles', ['App\Controllers\Admin\VehicleController', 'index']);
$this->get('/admin/vehicles/create', ['App\Controllers\Admin\VehicleController', 'create']);
$this->post('/admin/vehicles', ['App\Controllers\Admin\VehicleController', 'store']);
$this->get('/admin/vehicles/{id}/edit', ['App\Controllers\Admin\VehicleController', 'edit']);
$this->post('/admin/vehicles/{id}', ['App\Controllers\Admin\VehicleController', 'update']);
$this->post('/admin/vehicles/{id}/delete', ['App\Controllers\Admin\VehicleController', 'delete']);

// ─── Orders ──────────────────────────────────────────────────
$this->get('/admin/orders', ['App\Controllers\Admin\OrderController', 'index']);
$this->get('/admin/orders/{id}', ['App\Controllers\Admin\OrderController', 'show']);
$this->post('/admin/orders/{id}/status', ['App\Controllers\Admin\OrderController', 'updateStatus']);

// ─── Customers ───────────────────────────────────────────────
$this->get('/admin/customers', ['App\Controllers\Admin\CustomerController', 'index']);
$this->get('/admin/customers/{id}', ['App\Controllers\Admin\CustomerController', 'show']);
$this->get('/admin/customers/{id}/edit', ['App\Controllers\Admin\CustomerController', 'edit']);
$this->post('/admin/customers/{id}', ['App\Controllers\Admin\CustomerController', 'update']);

// ─── Marketing ───────────────────────────────────────────────
$this->get('/admin/marketing/price-rules', ['App\Controllers\Admin\MarketingController', 'priceRules']);
$this->get('/admin/marketing/price-rules/create', ['App\Controllers\Admin\MarketingController', 'createPriceRule']);
$this->post('/admin/marketing/price-rules', ['App\Controllers\Admin\MarketingController', 'storePriceRule']);
$this->get('/admin/marketing/price-rules/{id}/edit', ['App\Controllers\Admin\MarketingController', 'editPriceRule']);
$this->post('/admin/marketing/price-rules/{id}', ['App\Controllers\Admin\MarketingController', 'updatePriceRule']);
$this->get('/admin/marketing/coupons', ['App\Controllers\Admin\MarketingController', 'coupons']);
$this->get('/admin/marketing/coupons/create', ['App\Controllers\Admin\MarketingController', 'createCoupon']);
$this->post('/admin/marketing/coupons', ['App\Controllers\Admin\MarketingController', 'storeCoupon']);
$this->get('/admin/marketing/coupons/{id}/edit', ['App\Controllers\Admin\MarketingController', 'editCoupon']);
$this->post('/admin/marketing/coupons/{id}', ['App\Controllers\Admin\MarketingController', 'updateCoupon']);

// ─── Content ─────────────────────────────────────────────────
$this->get('/admin/content/pages', ['App\Controllers\Admin\ContentController', 'pages']);
$this->get('/admin/content/pages/create', ['App\Controllers\Admin\ContentController', 'createPage']);
$this->post('/admin/content/pages', ['App\Controllers\Admin\ContentController', 'storePage']);
$this->get('/admin/content/pages/{id}/edit', ['App\Controllers\Admin\ContentController', 'editPage']);
$this->post('/admin/content/pages/{id}', ['App\Controllers\Admin\ContentController', 'updatePage']);
$this->post('/admin/content/pages/{id}/delete', ['App\Controllers\Admin\ContentController', 'deletePage']);
$this->get('/admin/content/translations', ['App\Controllers\Admin\ContentController', 'translations']);
$this->post('/admin/content/translations', ['App\Controllers\Admin\ContentController', 'saveTranslations']);

// ─── Stores ──────────────────────────────────────────────────
$this->get('/admin/stores/websites', ['App\Controllers\Admin\StoreController', 'websites']);
$this->post('/admin/stores/websites', ['App\Controllers\Admin\StoreController', 'createWebsite']);
$this->post('/admin/stores/websites/{id}', ['App\Controllers\Admin\StoreController', 'updateWebsite']);
$this->post('/admin/stores/websites/{id}/delete', ['App\Controllers\Admin\StoreController', 'deleteWebsite']);
$this->get('/admin/stores/views', ['App\Controllers\Admin\StoreController', 'views']);
$this->post('/admin/stores/views', ['App\Controllers\Admin\StoreController', 'createView']);
$this->get('/admin/stores/views/{id}/edit', ['App\Controllers\Admin\StoreController', 'editView']);
$this->post('/admin/stores/views/{id}', ['App\Controllers\Admin\StoreController', 'updateView']);
$this->post('/admin/stores/views/{id}/delete', ['App\Controllers\Admin\StoreController', 'deleteView']);
$this->get('/admin/stores/domains', ['App\Controllers\Admin\StoreController', 'domains']);
$this->post('/admin/stores/domains', ['App\Controllers\Admin\StoreController', 'createDomain']);
$this->post('/admin/stores/domains/{id}/delete', ['App\Controllers\Admin\StoreController', 'deleteDomain']);

// ─── Configuration ───────────────────────────────────────────
$this->get('/admin/config', ['App\Controllers\Admin\ConfigController', 'index']);
$this->post('/admin/config', ['App\Controllers\Admin\ConfigController', 'save']);

// ─── System ──────────────────────────────────────────────────
$this->get('/admin/system/users', ['App\Controllers\Admin\SystemController', 'users']);
$this->get('/admin/system/users/create', ['App\Controllers\Admin\SystemController', 'createUser']);
$this->post('/admin/system/users', ['App\Controllers\Admin\SystemController', 'storeUser']);
$this->get('/admin/system/users/{id}/edit', ['App\Controllers\Admin\SystemController', 'editUser']);
$this->post('/admin/system/users/{id}', ['App\Controllers\Admin\SystemController', 'updateUser']);
$this->post('/admin/system/users/{id}/delete', ['App\Controllers\Admin\SystemController', 'deleteUser']);
$this->get('/admin/system/activity', ['App\Controllers\Admin\SystemController', 'activity']);
