<?php

declare(strict_types=1);

/** @var Brew\Core\App $this */

// ─── Admin Auth (no auth middleware needed) ──────────────────
$this->get('/admin/login', ['Brew\Controllers\Admin\AuthController', 'showLogin']);
$this->post('/admin/login', ['Brew\Controllers\Admin\AuthController', 'login']);
$this->post('/admin/logout', ['Brew\Controllers\Admin\AuthController', 'logout']);

// ─── Dashboard ───────────────────────────────────────────────
$this->get('/admin', ['Brew\Controllers\Admin\DashboardController', 'index']);

// ─── Products ────────────────────────────────────────────────
$this->get('/admin/products', ['Brew\Controllers\Admin\ProductController', 'index']);
$this->get('/admin/products/create', ['Brew\Controllers\Admin\ProductController', 'create']);
$this->post('/admin/products', ['Brew\Controllers\Admin\ProductController', 'store']);
$this->get('/admin/products/{id}/edit', ['Brew\Controllers\Admin\ProductController', 'edit']);
$this->post('/admin/products/{id}', ['Brew\Controllers\Admin\ProductController', 'update']);
$this->post('/admin/products/{id}/delete', ['Brew\Controllers\Admin\ProductController', 'delete']);

// ─── Categories ──────────────────────────────────────────────
$this->get('/admin/categories', ['Brew\Controllers\Admin\CategoryController', 'index']);
$this->get('/admin/categories/create', ['Brew\Controllers\Admin\CategoryController', 'create']);
$this->post('/admin/categories', ['Brew\Controllers\Admin\CategoryController', 'store']);
$this->get('/admin/categories/{id}/edit', ['Brew\Controllers\Admin\CategoryController', 'edit']);
$this->post('/admin/categories/{id}', ['Brew\Controllers\Admin\CategoryController', 'update']);
$this->post('/admin/categories/{id}/delete', ['Brew\Controllers\Admin\CategoryController', 'delete']);

// ─── Brands ──────────────────────────────────────────────────
$this->get('/admin/brands', ['Brew\Controllers\Admin\BrandController', 'index']);
$this->get('/admin/brands/create', ['Brew\Controllers\Admin\BrandController', 'create']);
$this->post('/admin/brands', ['Brew\Controllers\Admin\BrandController', 'store']);
$this->get('/admin/brands/{id}/edit', ['Brew\Controllers\Admin\BrandController', 'edit']);
$this->post('/admin/brands/{id}', ['Brew\Controllers\Admin\BrandController', 'update']);
$this->post('/admin/brands/{id}/delete', ['Brew\Controllers\Admin\BrandController', 'delete']);

// ─── Vehicles ────────────────────────────────────────────────
$this->get('/admin/vehicles', ['Brew\Controllers\Admin\VehicleController', 'index']);
$this->get('/admin/vehicles/create', ['Brew\Controllers\Admin\VehicleController', 'create']);
$this->post('/admin/vehicles', ['Brew\Controllers\Admin\VehicleController', 'store']);
$this->get('/admin/vehicles/{id}/edit', ['Brew\Controllers\Admin\VehicleController', 'edit']);
$this->post('/admin/vehicles/{id}', ['Brew\Controllers\Admin\VehicleController', 'update']);
$this->post('/admin/vehicles/{id}/delete', ['Brew\Controllers\Admin\VehicleController', 'delete']);

// ─── Orders ──────────────────────────────────────────────────
$this->get('/admin/orders', ['Brew\Controllers\Admin\OrderController', 'index']);
$this->get('/admin/orders/{id}', ['Brew\Controllers\Admin\OrderController', 'show']);
$this->post('/admin/orders/{id}/status', ['Brew\Controllers\Admin\OrderController', 'updateStatus']);

// ─── Customers ───────────────────────────────────────────────
$this->get('/admin/customers', ['Brew\Controllers\Admin\CustomerController', 'index']);
$this->get('/admin/customers/{id}', ['Brew\Controllers\Admin\CustomerController', 'show']);
$this->get('/admin/customers/{id}/edit', ['Brew\Controllers\Admin\CustomerController', 'edit']);
$this->post('/admin/customers/{id}', ['Brew\Controllers\Admin\CustomerController', 'update']);

// ─── Marketing ───────────────────────────────────────────────
$this->get('/admin/marketing/price-rules', ['Brew\Controllers\Admin\MarketingController', 'priceRules']);
$this->get('/admin/marketing/price-rules/create', ['Brew\Controllers\Admin\MarketingController', 'createPriceRule']);
$this->post('/admin/marketing/price-rules', ['Brew\Controllers\Admin\MarketingController', 'storePriceRule']);
$this->get('/admin/marketing/price-rules/{id}/edit', ['Brew\Controllers\Admin\MarketingController', 'editPriceRule']);
$this->post('/admin/marketing/price-rules/{id}', ['Brew\Controllers\Admin\MarketingController', 'updatePriceRule']);
$this->get('/admin/marketing/coupons', ['Brew\Controllers\Admin\MarketingController', 'coupons']);
$this->get('/admin/marketing/coupons/create', ['Brew\Controllers\Admin\MarketingController', 'createCoupon']);
$this->post('/admin/marketing/coupons', ['Brew\Controllers\Admin\MarketingController', 'storeCoupon']);
$this->get('/admin/marketing/coupons/{id}/edit', ['Brew\Controllers\Admin\MarketingController', 'editCoupon']);
$this->post('/admin/marketing/coupons/{id}', ['Brew\Controllers\Admin\MarketingController', 'updateCoupon']);

// ─── Content ─────────────────────────────────────────────────
$this->get('/admin/content/pages', ['Brew\Controllers\Admin\ContentController', 'pages']);
$this->get('/admin/content/pages/create', ['Brew\Controllers\Admin\ContentController', 'createPage']);
$this->post('/admin/content/pages', ['Brew\Controllers\Admin\ContentController', 'storePage']);
$this->get('/admin/content/pages/{id}/edit', ['Brew\Controllers\Admin\ContentController', 'editPage']);
$this->post('/admin/content/pages/{id}', ['Brew\Controllers\Admin\ContentController', 'updatePage']);
$this->post('/admin/content/pages/{id}/delete', ['Brew\Controllers\Admin\ContentController', 'deletePage']);
$this->get('/admin/content/translations', ['Brew\Controllers\Admin\ContentController', 'translations']);
$this->post('/admin/content/translations', ['Brew\Controllers\Admin\ContentController', 'saveTranslations']);

// ─── Stores ──────────────────────────────────────────────────
$this->get('/admin/stores/websites', ['Brew\Controllers\Admin\StoreController', 'websites']);
$this->post('/admin/stores/websites', ['Brew\Controllers\Admin\StoreController', 'createWebsite']);
$this->post('/admin/stores/websites/{id}', ['Brew\Controllers\Admin\StoreController', 'updateWebsite']);
$this->post('/admin/stores/websites/{id}/delete', ['Brew\Controllers\Admin\StoreController', 'deleteWebsite']);
$this->get('/admin/stores/views', ['Brew\Controllers\Admin\StoreController', 'views']);
$this->post('/admin/stores/views', ['Brew\Controllers\Admin\StoreController', 'createView']);
$this->get('/admin/stores/views/{id}/edit', ['Brew\Controllers\Admin\StoreController', 'editView']);
$this->post('/admin/stores/views/{id}', ['Brew\Controllers\Admin\StoreController', 'updateView']);
$this->post('/admin/stores/views/{id}/delete', ['Brew\Controllers\Admin\StoreController', 'deleteView']);
$this->get('/admin/stores/domains', ['Brew\Controllers\Admin\StoreController', 'domains']);
$this->post('/admin/stores/domains', ['Brew\Controllers\Admin\StoreController', 'createDomain']);
$this->post('/admin/stores/domains/{id}/delete', ['Brew\Controllers\Admin\StoreController', 'deleteDomain']);

// ─── Configuration ───────────────────────────────────────────
$this->get('/admin/config', ['Brew\Controllers\Admin\ConfigController', 'index']);
$this->post('/admin/config', ['Brew\Controllers\Admin\ConfigController', 'save']);

// ─── System ──────────────────────────────────────────────────
$this->get('/admin/system/users', ['Brew\Controllers\Admin\SystemController', 'users']);
$this->get('/admin/system/users/create', ['Brew\Controllers\Admin\SystemController', 'createUser']);
$this->post('/admin/system/users', ['Brew\Controllers\Admin\SystemController', 'storeUser']);
$this->get('/admin/system/users/{id}/edit', ['Brew\Controllers\Admin\SystemController', 'editUser']);
$this->post('/admin/system/users/{id}', ['Brew\Controllers\Admin\SystemController', 'updateUser']);
$this->post('/admin/system/users/{id}/delete', ['Brew\Controllers\Admin\SystemController', 'deleteUser']);
$this->get('/admin/system/activity', ['Brew\Controllers\Admin\SystemController', 'activity']);
