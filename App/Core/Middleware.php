<?php

declare(strict_types=1);

namespace App\Core;

class Middleware
{
    private static array $middleware = [];
    private static array $groups = [];

    public static function register(string $name, string $class): void
    {
        self::$middleware[$name] = $class;
    }

    public static function group(string $name, array $middleware): void
    {
        self::$groups[$name] = $middleware;
    }

    public static function resolve(array $middleware): array
    {
        $resolved = [];

        foreach ($middleware as $m) {
            if (isset(self::$groups[$m])) {
                $resolved = array_merge($resolved, self::resolve(self::$groups[$m]));
            } elseif (isset(self::$middleware[$m])) {
                $resolved[] = self::$middleware[$m];
            } elseif (class_exists($m)) {
                $resolved[] = $m;
            }
        }

        return $resolved;
    }

    public static function run(array $middleware, Request $request, callable $handler): Response
    {
        $resolved = self::resolve($middleware);

        $pipeline = array_reduce(
            array_reverse($resolved),
            function (callable $next, string $middlewareClass) {
                return function (Request $request) use ($middlewareClass, $next): Response {
                    $instance = new $middlewareClass();
                    return $instance->handle($request, $next);
                };
            },
            $handler
        );

        return $pipeline($request);
    }
}

// Base middleware interface
interface MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response;
}
