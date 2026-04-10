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

// ─── Attributes ──────────────────────────────────────────────
$this->get('/admin/attributes', ['App\Controllers\Admin\AttributeController', 'index']);
$this->get('/admin/attributes/create', ['App\Controllers\Admin\AttributeController', 'create']);
$this->post('/admin/attributes', ['App\Controllers\Admin\AttributeController', 'store']);
$this->get('/admin/attributes/{id}/edit', ['App\Controllers\Admin\AttributeController', 'edit']);
$this->post('/admin/attributes/{id}', ['App\Controllers\Admin\AttributeController', 'update']);
$this->post('/admin/attributes/{id}/delete', ['App\Controllers\Admin\AttributeController', 'delete']);

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
$this->get('/admin/marketing/ads', ['App\Controllers\Admin\MarketingController', 'ads']);
$this->post('/admin/marketing/ads', ['App\Controllers\Admin\MarketingController', 'storeAd']);
$this->post('/admin/marketing/ads/{id}', ['App\Controllers\Admin\MarketingController', 'updateAd']);
$this->post('/admin/marketing/ads/{id}/delete', ['App\Controllers\Admin\MarketingController', 'deleteAd']);

// ─── Content ─────────────────────────────────────────────────
$this->get('/admin/content/pages', ['App\Controllers\Admin\ContentController', 'pages']);
$this->get('/admin/content/pages/create', ['App\Controllers\Admin\ContentController', 'createPage']);
$this->post('/admin/content/pages', ['App\Controllers\Admin\ContentController', 'storePage']);
$this->get('/admin/content/pages/{id}/edit', ['App\Controllers\Admin\ContentController', 'editPage']);
$this->post('/admin/content/pages/{id}', ['App\Controllers\Admin\ContentController', 'updatePage']);
$this->post('/admin/content/pages/{id}/delete', ['App\Controllers\Admin\ContentController', 'deletePage']);
$this->get('/admin/content/translations', ['App\Controllers\Admin\ContentController', 'translations']);
$this->post('/admin/content/translations', ['App\Controllers\Admin\ContentController', 'saveTranslations']);
$this->post('/admin/content/translations/key', ['App\Controllers\Admin\ContentController', 'addTranslationKey']);
$this->post('/admin/content/translations/key/{id}/delete', ['App\Controllers\Admin\ContentController', 'deleteTranslationKey']);
$this->get('/admin/content/translations/export', ['App\Controllers\Admin\ContentController', 'exportTranslations']);
$this->post('/admin/content/translations/import', ['App\Controllers\Admin\ContentController', 'importTranslations']);

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

// ─── Partners ────────────────────────────────────────────────
$this->get('/admin/partners', ['App\Controllers\Admin\PartnerController', 'applications']);
$this->get('/admin/partners/accounts', ['App\Controllers\Admin\PartnerController', 'accounts']);
$this->get('/admin/partners/accounts/{id}', ['App\Controllers\Admin\PartnerController', 'showAccount']);
$this->post('/admin/partners/accounts/{id}', ['App\Controllers\Admin\PartnerController', 'updateAccount']);
$this->post('/admin/partners/accounts/{id}/referrals', ['App\Controllers\Admin\PartnerController', 'addReferral']);
$this->post('/admin/partners/accounts/{id}/referrals/{refId}/status', ['App\Controllers\Admin\PartnerController', 'updateReferralStatus']);
$this->get('/admin/partners/{id}', ['App\Controllers\Admin\PartnerController', 'showApplication']);
$this->post('/admin/partners/{id}/approve', ['App\Controllers\Admin\PartnerController', 'approveApplication']);
$this->post('/admin/partners/{id}/reject', ['App\Controllers\Admin\PartnerController', 'rejectApplication']);

// ─── Dealers ─────────────────────────────────────────────────
$this->get('/admin/dealers', ['App\Controllers\Admin\DealerController', 'applications']);
$this->get('/admin/dealers/accounts', ['App\Controllers\Admin\DealerController', 'accounts']);
$this->get('/admin/dealers/accounts/{id}', ['App\Controllers\Admin\DealerController', 'showAccount']);
$this->post('/admin/dealers/accounts/{id}', ['App\Controllers\Admin\DealerController', 'updateAccount']);
$this->get('/admin/dealers/{id}', ['App\Controllers\Admin\DealerController', 'showApplication']);
$this->post('/admin/dealers/{id}/approve', ['App\Controllers\Admin\DealerController', 'approveApplication']);
$this->post('/admin/dealers/{id}/reject', ['App\Controllers\Admin\DealerController', 'rejectApplication']);

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

// ─── Support Tickets ─────────────────────────────────────────────────────────
$this->get('/admin/tickets', ['App\Controllers\Admin\TicketController', 'index']);
$this->get('/admin/tickets/export', ['App\Controllers\Admin\TicketController', 'export']);
$this->get('/admin/tickets/create', ['App\Controllers\Admin\TicketController', 'create']);
$this->post('/admin/tickets', ['App\Controllers\Admin\TicketController', 'store']);

// Departments (static — must be before /{id})
$this->get('/admin/tickets/departments', ['App\Controllers\Admin\TicketController', 'departments']);
$this->post('/admin/tickets/departments', ['App\Controllers\Admin\TicketController', 'storeDepartment']);
$this->post('/admin/tickets/departments/{id}', ['App\Controllers\Admin\TicketController', 'updateDepartment']);
$this->post('/admin/tickets/departments/{id}/delete', ['App\Controllers\Admin\TicketController', 'deleteDepartment']);

// Mailboxes (static — must be before /{id})
$this->get('/admin/tickets/mailboxes', ['App\Controllers\Admin\TicketController', 'mailboxes']);
$this->post('/admin/tickets/mailboxes', ['App\Controllers\Admin\TicketController', 'storeMailbox']);
$this->post('/admin/tickets/mailboxes/smtp', ['App\Controllers\Admin\TicketController', 'saveMailboxSmtp']);
$this->post('/admin/tickets/mailboxes/{id}', ['App\Controllers\Admin\TicketController', 'updateMailbox']);
$this->post('/admin/tickets/mailboxes/{id}/delete', ['App\Controllers\Admin\TicketController', 'deleteMailbox']);

// Categories (static — must be before /{id})
$this->get('/admin/tickets/categories', ['App\Controllers\Admin\TicketController', 'categories']);
$this->post('/admin/tickets/categories', ['App\Controllers\Admin\TicketController', 'storeCategory']);
$this->post('/admin/tickets/categories/{id}', ['App\Controllers\Admin\TicketController', 'updateCategory']);
$this->post('/admin/tickets/categories/{id}/delete', ['App\Controllers\Admin\TicketController', 'deleteCategory']);

// SLA Policies (static — must be before /{id})
$this->get('/admin/tickets/sla', ['App\Controllers\Admin\TicketController', 'sla']);
$this->post('/admin/tickets/sla', ['App\Controllers\Admin\TicketController', 'storeSla']);
$this->post('/admin/tickets/sla/{id}', ['App\Controllers\Admin\TicketController', 'updateSla']);
$this->post('/admin/tickets/sla/{id}/delete', ['App\Controllers\Admin\TicketController', 'deleteSla']);

// Canned Responses (static — must be before /{id})
$this->get('/admin/tickets/canned', ['App\Controllers\Admin\TicketController', 'canned']);
$this->post('/admin/tickets/canned', ['App\Controllers\Admin\TicketController', 'storeCanned']);
$this->post('/admin/tickets/canned/{id}', ['App\Controllers\Admin\TicketController', 'updateCanned']);
$this->post('/admin/tickets/canned/{id}/delete', ['App\Controllers\Admin\TicketController', 'deleteCanned']);

// Dynamic ticket routes (after all static ones)
$this->get('/admin/tickets/{id}', ['App\Controllers\Admin\TicketController', 'show']);
$this->post('/admin/tickets/{id}/update', ['App\Controllers\Admin\TicketController', 'update']);
$this->post('/admin/tickets/{id}/reply', ['App\Controllers\Admin\TicketController', 'reply']);
$this->post('/admin/tickets/{id}/escalate', ['App\Controllers\Admin\TicketController', 'escalate']);
$this->post('/admin/tickets/{id}/merge', ['App\Controllers\Admin\TicketController', 'merge']);
