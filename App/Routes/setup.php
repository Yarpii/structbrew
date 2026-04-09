<?php
declare(strict_types=1);

/** @var App\Core\App $this */

use App\Controllers\SetupController;

// Only register setup routes if setup is needed
if (!SetupController::needsSetup()) {
    return;
}

$this->get('/setup', [SetupController::class, 'index']);
$this->get('/setup/database', [SetupController::class, 'database']);
$this->post('/setup/database', [SetupController::class, 'databaseSave']);
$this->get('/setup/application', [SetupController::class, 'application']);
$this->post('/setup/application', [SetupController::class, 'applicationSave']);
$this->get('/setup/admin', [SetupController::class, 'admin']);
$this->post('/setup/install', [SetupController::class, 'install']);
$this->get('/setup/complete', [SetupController::class, 'complete']);
