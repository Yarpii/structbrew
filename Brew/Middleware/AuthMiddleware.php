<?php

declare(strict_types=1);

namespace Brew\Middleware;

use Brew\Core\Auth;
use Brew\Core\MiddlewareInterface;
use Brew\Core\Request;
use Brew\Core\Response;
use Brew\Core\Session;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        if (!Auth::isLoggedIn()) {
            Session::flash('error', 'Please log in to continue.');
            return Response::redirect('/login');
        }

        return $next($request);
    }
}
