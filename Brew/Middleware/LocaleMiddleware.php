<?php

declare(strict_types=1);

namespace Brew\Middleware;

use Brew\Core\MiddlewareInterface;
use Brew\Core\Request;
use Brew\Core\Response;
use Brew\Core\StoreResolver;

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
