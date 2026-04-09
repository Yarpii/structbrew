<?php

declare(strict_types=1);

namespace Brew\Middleware;

use Brew\Core\MiddlewareInterface;
use Brew\Core\Request;
use Brew\Core\Response;
use Brew\Core\StoreResolver;
use Brew\Core\Translator;

class StoreResolverMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        // Resolve the current store based on the request host
        StoreResolver::resolve();

        // Set the translator locale from the resolved store view
        $locale = StoreResolver::locale();
        Translator::setLocale($locale);

        // Load database translations for the current store view
        Translator::loadFromDatabase();

        return $next($request);
    }
}
