<?php
declare(strict_types=1);

/** @var Brew\Core\App $this */

use Brew\Controllers\PagesController;

$this->get('/{slug}', [PagesController::class, 'show']);
