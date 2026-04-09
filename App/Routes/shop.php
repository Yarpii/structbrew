<?php
declare(strict_types=1);

/** @var App\Core\App $this */

use App\Controllers\ShopController;
use App\Controllers\CartController;
use App\Controllers\AuthController;

$this->get('/shop', [ShopController::class, 'index']);
$this->get('/shop/{slug}', [ShopController::class, 'show']);
$this->get('/cart', [CartController::class, 'index']);
$this->get('/login', [AuthController::class, 'login']);
$this->get('/register', [AuthController::class, 'register']);
$this->get('/api/products', [ShopController::class, 'apiProducts']);
