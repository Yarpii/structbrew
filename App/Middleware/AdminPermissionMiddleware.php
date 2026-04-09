<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\MiddlewareInterface;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

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
