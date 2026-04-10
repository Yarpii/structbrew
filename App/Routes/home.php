<?php
declare(strict_types=1);

/** @var App\Core\App $this */

use App\Controllers\HomeController;
use App\Controllers\PartnerController;
use App\Controllers\DealerController;
use App\Core\GeoRouter;
use App\Core\Request;
use App\Core\Response;

$this->get('/', [HomeController::class, 'index']);

// ─── Partner Program ─────────────────────────────────────────
$this->get('/partner-program', [PartnerController::class, 'landing']);

// ─── Dealer / B2B ────────────────────────────────────────────
$this->post('/dealer/apply', [DealerController::class, 'apply']);
$this->post('/partner-program/apply', [PartnerController::class, 'apply']);
$this->get('/r/{code}', [PartnerController::class, 'trackClick']);
$this->get('/about', [HomeController::class, 'about']);
$this->get('/contact', [HomeController::class, 'contact']);
$this->get('/support', [HomeController::class, 'support']);
$this->get('/ad/click/{id}', [HomeController::class, 'adClick']);
$this->get('/api', [HomeController::class, 'api']);

// Geo-routing: set override cookie so user stays on their chosen store
$this->get('/store/stay', function (Request $request) {
    GeoRouter::setOverrideCookie();
    $back = $request->query('redirect');
    // Only allow relative redirects to prevent open redirect
    if ($back && str_starts_with($back, '/') && !str_starts_with($back, '//')) {
        return Response::redirect($back);
    }
    return Response::redirect('/');
});

// Geo-routing: clear override cookie (allow geo-redirect again)
$this->get('/store/reset-geo', function (Request $request) {
    GeoRouter::clearOverrideCookie();
    return Response::redirect('/');
});
