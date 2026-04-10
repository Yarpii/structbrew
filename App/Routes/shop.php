<?php
declare(strict_types=1);

/** @var App\Core\App $this */

use App\Controllers\AccountController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\PartnerController;
use App\Controllers\ShopController;

$this->get('/shop', [ShopController::class, 'index']);
$this->get('/categories', [ShopController::class, 'categories']);
$this->get('/shop/{slug}', [ShopController::class, 'show']);
$this->get('/cart', [CartController::class, 'index']);
$this->post('/cart/checkout', [CartController::class, 'checkout']);

$this->get('/login', [AuthController::class, 'login']);
$this->post('/login', [AuthController::class, 'authenticate']);
$this->get('/login/2fa', [AuthController::class, 'twoFactor']);
$this->post('/login/2fa', [AuthController::class, 'verifyTwoFactor']);
$this->get('/register', [AuthController::class, 'register']);
$this->post('/register', [AuthController::class, 'store']);
$this->post('/logout', [AuthController::class, 'logout']);

$this->get('/account', [AccountController::class, 'index']);
$this->get('/account/profile', [AccountController::class, 'profile']);
$this->get('/account/orders', [AccountController::class, 'orders']);
$this->get('/account/addresses', [AccountController::class, 'addresses']);
$this->get('/account/garage', [AccountController::class, 'garage']);
$this->post('/account/profile', [AccountController::class, 'updateProfile']);
$this->post('/account/profile/2fa/setup', [AccountController::class, 'setupTwoFactor']);
$this->post('/account/profile/2fa/enable', [AccountController::class, 'enableTwoFactor']);
$this->post('/account/profile/2fa/disable', [AccountController::class, 'disableTwoFactor']);
$this->post('/account/addresses', [AccountController::class, 'storeAddress']);
$this->post('/account/addresses/{id}/update', [AccountController::class, 'updateAddress']);
$this->post('/account/addresses/{id}/delete', [AccountController::class, 'deleteAddress']);
$this->post('/account/addresses/{id}/default', [AccountController::class, 'defaultAddress']);
$this->post('/account/garage', [AccountController::class, 'storeGarageVehicle']);
$this->post('/account/garage/{id}/delete', [AccountController::class, 'deleteGarageVehicle']);
$this->post('/account/garage/{id}/select', [AccountController::class, 'selectGarageVehicle']);
$this->post('/account/garage/{id}/update', [AccountController::class, 'updateGarageVehicle']);
$this->post('/account/garage/{id}/photo', [AccountController::class, 'uploadGaragePhoto']);
$this->post('/account/garage/{id}/spec', [AccountController::class, 'updateGarageSpec']);
$this->post('/account/garage/{id}/mods', [AccountController::class, 'storeGarageMod']);
$this->post('/account/garage/{id}/mods/{modId}/toggle', [AccountController::class, 'toggleGarageMod']);
$this->post('/account/garage/{id}/mods/{modId}/delete', [AccountController::class, 'deleteGarageMod']);
$this->post('/account/credits/purchase', [AccountController::class, 'purchaseCredits']);
$this->get('/account/orders/{id}', [AccountController::class, 'order']);
$this->get('/account/orders/{id}/invoice', [AccountController::class, 'invoice']);

// ─── Account Tickets ──────────────────────────────────────────────────────────
$this->get('/account/tickets', [AccountController::class, 'tickets']);
$this->get('/account/tickets/create', [AccountController::class, 'ticketCreate']);
$this->post('/account/tickets', [AccountController::class, 'ticketStore']);
$this->get('/account/tickets/{id}', [AccountController::class, 'ticketShow']);
$this->post('/account/tickets/{id}/reply', [AccountController::class, 'ticketReply']);
$this->post('/account/tickets/{id}/close', [AccountController::class, 'ticketClose']);
$this->post('/account/tickets/{id}/reopen', [AccountController::class, 'ticketReopen']);

// ─── Account Partner ──────────────────────────────────────────────────────────
$this->get('/account/partner', [PartnerController::class, 'dashboard']);

$this->get('/api/products', [ShopController::class, 'apiProducts']);
