<?php
declare(strict_types=1);

/** @var App\Core\App $this */

use App\Controllers\HomeController;

$this->get('/', [HomeController::class, 'index']);
$this->get('/about', [HomeController::class, 'about']);
$this->get('/contact', [HomeController::class, 'contact']);
$this->get('/api', [HomeController::class, 'api']);
