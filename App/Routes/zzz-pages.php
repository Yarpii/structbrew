<?php
declare(strict_types=1);

/** @var App\Core\App $this */

use App\Controllers\PagesController;

$this->get('/{slug}', [PagesController::class, 'show']);
