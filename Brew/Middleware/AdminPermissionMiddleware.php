<?php

declare(strict_types=1);

namespace Brew\Middleware;

use Brew\Core\Auth;
use Brew\Core\MiddlewareInterface;
use Brew\Core\Request;
use Brew\Core\Response;
use Brew\Core\Session;

class AdminPermissionMiddleware implements MiddlewareInterface
{
    private string $permission;

    public function __construct(string $permission)
    {
        $this->permission = $permission;
    }

    public function handle(Request $request, callable $next): Response
    {
        if (!Auth::adminCan($this->permission)) {
            Session::flash('error', 'Access denied.');
            return Response::redirect('/admin');
        }

        return $next($request);
    }
}
