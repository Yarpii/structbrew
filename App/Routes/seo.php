<?php

declare(strict_types=1);

/** @var App\Core\App $this */

use App\Controllers\SitemapController;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\StoreResolver;

// ─── XML Sitemaps ────────────────────────────────────────────

$this->get('/sitemap.xml', [SitemapController::class, 'index']);
$this->get('/sitemap-pages.xml', [SitemapController::class, 'pages']);
$this->get('/sitemap-products.xml', [SitemapController::class, 'products']);
$this->get('/sitemap-categories.xml', [SitemapController::class, 'categories']);

// ─── Dynamic robots.txt ──────────────────────────────────────

$this->get('/robots.txt', function (Request $request) {
    $host = $_SERVER['HTTP_HOST'] ?? 'scooterdynamics.com';
    $host = preg_replace('/:\d+$/', '', $host);
    $sitemapUrl = 'https://' . $host . '/sitemap.xml';

    // Check admin-configured robots.txt
    $customRobots = null;
    try {
        $customRobots = StoreResolver::getConfig('seo/robots_txt');
    } catch (\Throwable $e) {
        // DB not available, use default
    }

    if ($customRobots && trim($customRobots) !== '') {
        $body = trim($customRobots) . "\n\nSitemap: " . $sitemapUrl . "\n";
    } else {
        $body = "User-agent: *\n";
        $body .= "Allow: /\n";
        $body .= "Disallow: /admin/\n";
        $body .= "Disallow: /api/\n";
        $body .= "Disallow: /setup/\n";
        $body .= "Disallow: /storage/\n";
        $body .= "Disallow: /store/stay\n";
        $body .= "Disallow: /store/reset-geo\n";
        $body .= "\n";
        $body .= "Sitemap: " . $sitemapUrl . "\n";
    }

    return new Response($body, 200, [
        'Content-Type' => 'text/plain; charset=UTF-8',
        'Cache-Control' => 'public, max-age=86400',
    ]);
});
