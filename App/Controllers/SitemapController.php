<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;

/**
 * Generates XML sitemaps with hreflang annotations for international SEO.
 *
 * Routes:
 *   GET /sitemap.xml         → Sitemap index pointing to sub-sitemaps
 *   GET /sitemap-pages.xml   → Static pages (home, about, contact, shop, etc.)
 *   GET /sitemap-products.xml → All products
 *   GET /sitemap-categories.xml → All categories
 */
final class SitemapController extends Controller
{
    /**
     * Sitemap index: lists all sub-sitemaps.
     */
    public function index(): Response
    {
        $host = $this->currentBaseUrl();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        $sitemaps = ['sitemap-pages.xml', 'sitemap-products.xml', 'sitemap-categories.xml'];
        foreach ($sitemaps as $map) {
            $xml .= '  <sitemap>' . "\n";
            $xml .= '    <loc>' . $host . '/' . $map . '</loc>' . "\n";
            $xml .= '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
            $xml .= '  </sitemap>' . "\n";
        }

        $xml .= '</sitemapindex>';
        return $this->xmlResponse($xml);
    }

    /**
     * Sitemap for static pages.
     */
    public function pages(): Response
    {
        $alternates = $this->buildAlternatesMap();

        $staticPages = ['/', '/shop', '/about', '/contact', '/support'];
        $entries = [];

        foreach ($staticPages as $pagePath) {
            $entries[] = $this->buildUrlEntry($pagePath, $alternates, 'weekly', $pagePath === '/' ? '1.0' : '0.7');
        }

        // CMS pages
        $db = Database::getInstance();
        $cmsPages = $db->table('cms_pages')
            ->where('is_active', 1)
            ->get();

        foreach ($cmsPages as $page) {
            $slug = $page['slug'] ?? '';
            if ($slug !== '') {
                $entries[] = $this->buildUrlEntry('/page/' . $slug, $alternates, 'monthly', '0.5');
            }
        }

        return $this->xmlResponse($this->wrapUrlSet($entries));
    }

    /**
     * Sitemap for products.
     */
    public function products(): Response
    {
        $alternates = $this->buildAlternatesMap();
        $db = Database::getInstance();

        $products = $db->table('products')
            ->where('is_active', 1)
            ->orderBy('id', 'ASC')
            ->get();

        $entries = [];
        foreach ($products as $product) {
            $slug = $product['slug'] ?? '';
            if ($slug === '') {
                continue;
            }

            $lastmod = $product['updated_at'] ?? $product['created_at'] ?? date('Y-m-d');
            $entries[] = $this->buildUrlEntry(
                '/shop/' . $slug,
                $alternates,
                'weekly',
                '0.8',
                substr($lastmod, 0, 10)
            );
        }

        return $this->xmlResponse($this->wrapUrlSet($entries));
    }

    /**
     * Sitemap for categories.
     */
    public function categories(): Response
    {
        $alternates = $this->buildAlternatesMap();
        $db = Database::getInstance();

        $categories = $db->table('categories')
            ->where('is_active', 1)
            ->orderBy('id', 'ASC')
            ->get();

        $entries = [];
        foreach ($categories as $cat) {
            $slug = $cat['slug'] ?? '';
            if ($slug === '') {
                continue;
            }

            $entries[] = $this->buildUrlEntry(
                '/shop?category=' . urlencode($slug),
                $alternates,
                'weekly',
                '0.6'
            );
        }

        return $this->xmlResponse($this->wrapUrlSet($entries));
    }

    /**
     * Build the alternates map: array of [hreflang => base_url] for all active store domains.
     */
    private function buildAlternatesMap(): array
    {
        $db = Database::getInstance();

        $domains = $db->table('store_domains')
            ->join('store_views', 'store_domains.store_view_id', '=', 'store_views.id')
            ->where('store_domains.is_active', 1)
            ->where('store_views.is_active', 1)
            ->get();

        $alternates = [];
        foreach ($domains as $d) {
            $hreflang = $d['hreflang'] ?? str_replace('_', '-', $d['locale'] ?? 'en');
            $domain = $d['domain'] ?? '';
            $pathPrefix = $d['path_prefix'] ?? '/';

            if ($domain === '') {
                continue;
            }

            $baseUrl = 'https://' . $domain . rtrim($pathPrefix, '/');
            $alternates[] = [
                'hreflang' => strtolower($hreflang),
                'base_url' => $baseUrl,
            ];
        }

        // Add x-default
        $alternates[] = [
            'hreflang' => 'x-default',
            'base_url' => 'https://scooterdynamics.com',
        ];

        return $alternates;
    }

    /**
     * Build a single <url> entry with xhtml:link hreflang alternates.
     */
    private function buildUrlEntry(
        string $pagePath,
        array $alternates,
        string $changefreq = 'weekly',
        string $priority = '0.5',
        ?string $lastmod = null
    ): string {
        $currentBase = $this->currentBaseUrl();
        $loc = $currentBase . $pagePath;

        $xml = "  <url>\n";
        $xml .= "    <loc>" . $this->escXml($loc) . "</loc>\n";

        if ($lastmod !== null) {
            $xml .= "    <lastmod>" . $this->escXml($lastmod) . "</lastmod>\n";
        }

        $xml .= "    <changefreq>" . $changefreq . "</changefreq>\n";
        $xml .= "    <priority>" . $priority . "</priority>\n";

        // hreflang alternates
        foreach ($alternates as $alt) {
            $href = $alt['base_url'] . $pagePath;
            $xml .= '    <xhtml:link rel="alternate" hreflang="'
                . $this->escXml($alt['hreflang']) . '" href="'
                . $this->escXml($href) . '" />' . "\n";
        }

        $xml .= "  </url>\n";
        return $xml;
    }

    private function wrapUrlSet(array $entries): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'
            . ' xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
        $xml .= implode('', $entries);
        $xml .= '</urlset>';
        return $xml;
    }

    private function currentBaseUrl(): string
    {
        $host = $_SERVER['HTTP_HOST'] ?? 'scooterdynamics.com';
        $host = preg_replace('/:\d+$/', '', $host);
        return 'https://' . $host;
    }

    private function escXml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function xmlResponse(string $xml): Response
    {
        return new Response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
