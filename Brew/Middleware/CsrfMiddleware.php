<?php

declare(strict_types=1);

namespace Brew\Middleware;

use Brew\Core\MiddlewareInterface;
use Brew\Core\Request;
use Brew\Core\Response;
use Brew\Core\Session;

class CsrfMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        $method = $request->method();

        if (in_array($method, ['POST', 'PUT', 'DELETE'], true)) {
            // Skip CSRF verification for API routes
            $path = $request->path();
            if (str_starts_with($path, '/api/')) {
                return $next($request);
            }

            $token = $request->input('_token', '');

            if (!is_string($token) || $token === '' || !Session::verifyCsrf($token)) {
                return Response::html('403 Forbidden - Invalid CSRF token.', 403);
            }
        }

        return $next($request);
    }
}
