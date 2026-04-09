<?php
declare(strict_types=1);
namespace Brew\Core;

use ReflectionMethod;
use RuntimeException;
use Throwable;

final class Router
{
    private array $routes = [];
    private const SUPPORTED_METHODS = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
    public function add(string $method, string $path, callable|array $handler): void
    {
        $method = strtoupper($method);
        if (!in_array($method, self::SUPPORTED_METHODS, true)) {
            throw new RuntimeException("Unsupported HTTP method: $method");
        }
        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_-]*)}#', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . rtrim($pattern, '/') . '/?$#';
        $this->routes[$method][] = [
            'pattern' => $pattern,
            'handler' => $handler,
        ];
    }
    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $uri    = $request->path();
        if (!isset($this->routes[$method])) {
            $this->sendResponse(Response::text("405 Method Not Allowed"), 405);
            return;
        }
        foreach ($this->routes[$method] as $route) {
            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->handle($route['handler'], $request, $params);
                return;
            }
        }
        $this->sendResponse(Response::text("404 Not Found: $uri"), 404);
    }
    private function handle(callable|array $handler, Request $request, array $params): void
    {
        try {
            $response = null;
            if (is_array($handler)) {
                [$controllerName, $method] = $handler;
                $controllerName = $this->resolveControllerName($controllerName);
                if (!class_exists($controllerName)) {
                    throw new RuntimeException("Controller not found: $controllerName");
                }
                $controller = new $controllerName();
                if (!method_exists($controller, $method)) {
                    throw new RuntimeException("Controller method not found: $controllerName::$method");
                }
                $reflection = new ReflectionMethod($controller, $method);
                $args = [];
                foreach ($reflection->getParameters() as $param) {
                    $name = $param->getName();
                    if ($param->getType() && $param->getType()->getName() === Request::class) {
                        $args[] = $request;
                    } elseif (isset($params[$name])) {
                        $args[] = $params[$name];
                    } elseif ($param->isDefaultValueAvailable()) {
                        $args[] = $param->getDefaultValue();
                    } else {
                        $args[] = null;
                    }
                }
                $response = $reflection->invokeArgs($controller, $args);
            } else {
                $response = call_user_func($handler, $request, ...array_values($params));
            }
            $this->sendResponse($response);
        } catch (Throwable $e) {
            if (!headers_sent()) {
                http_response_code(500);
                header('Content-Type: text/plain; charset=utf-8');
            }
            echo "🔥 Internal Server Error\n\n" . $e->getMessage();
        }
    }
    private function resolveControllerName(string $controllerName): string
    {
        if (str_contains($controllerName, '\\')) {
            return $controllerName;
        }
        $candidate = "Brew\\Controllers\\$controllerName";
        if (class_exists($candidate)) {
            return $candidate;
        }
        $alt = "App\\Controllers\\$controllerName";
        if (class_exists($alt)) {
            return $alt;
        }
        return $controllerName;
    }
    private function sendResponse(mixed $response, int $code = 200): void
    {
        if ($response instanceof Response) {
            $response->send();
            return;
        }
        if (is_array($response) || is_object($response)) {
            Response::json($response)->send();
            return;
        }
        if (is_string($response)) {
            Response::text($response, $code)->send();
            return;
        }
        Response::text('Empty response', $code)->send();
    }
}