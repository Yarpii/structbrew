<?php

declare(strict_types=1);

namespace Brew\Middleware;

use Brew\Core\Auth;
use Brew\Core\MiddlewareInterface;
use Brew\Core\Request;
use Brew\Core\Response;

class AdminAuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        if (!Auth::isAdmin()) {
            return Response::redirect('/admin/login');
        }

        return $next($request);
    }
}
