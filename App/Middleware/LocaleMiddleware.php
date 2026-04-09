<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\MiddlewareInterface;
use App\Core\Request;
use App\Core\Response;
use App\Core\StoreResolver;

class LocaleMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        $locale = StoreResolver::locale();
        $language = StoreResolver::language();

        // Set PHP locale for date/number formatting
        setlocale(LC_ALL, $locale . '.UTF-8', $locale . '.utf8', $locale);

        /** @var Response $response */
        $response = $next($request);

        // Set Content-Language header on the outgoing response
        $response->header('Content-Language', $language);

        return $response;
    }
}
