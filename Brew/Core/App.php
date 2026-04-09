<?php
declare(strict_types=1);
namespace Brew\Core;

use ErrorException;
use Throwable;

final class App
{
    private string $basePath;
    private Router $router;
    private Request $request;
    private array $config;

    /**
     * @throws ErrorException
     */
    public function __construct(string $basePath, array $config = [])
    {
        $this->basePath = rtrim($basePath, DIRECTORY_SEPARATOR);
        $defaults = [
            'debug'       => true,
            'timezone'    => 'Europe/Amsterdam',
            'routes_path' => $this->basePath . '/Brew/Routes'
        ];
        $this->config  = $config + $defaults;
        $this->router  = new Router();
        $this->request = Request::fromGlobals();
        $this->bootstrapErrorHandling();
        $this->loadRoutesDirectory($this->config['routes_path']);
    }
    private function bootstrapErrorHandling(): void
    {
        if (isset($this->config['timezone']) && is_string($this->config['timezone'])) {
            @date_default_timezone_set($this->config['timezone']);
        }
        set_error_handler(/**
         * @throws ErrorException
         */ static function (int $severity, string $message, string $file = '', int $line = 0): bool {
            if (!(error_reporting() & $severity)) {
                return false;
            }
            throw new ErrorException($message, 0, $severity, $file, $line);
        });
        set_exception_handler(function (Throwable $e): void {
            http_response_code(500);

            if (!headers_sent()) {
                header('Content-Type: text/html; charset=utf-8');
            }
            echo '<h1>500 Internal Server Error</h1>';
            if (!empty($this->config['debug'])) {
                echo '<pre>' . htmlspecialchars((string)$e, ENT_QUOTES, 'UTF-8') . '</pre>';
            }
        });
    }
    private function loadRoutesDirectory(string|array $dirs): void
    {
        foreach ((array) $dirs as $dir) {
            if (!is_dir($dir)) continue;
            $files = glob($dir . DIRECTORY_SEPARATOR . '*.php') ?: [];
            sort($files);
            foreach ($files as $file) {
                /** @var self $this */
                require $file;
            }
        }
    }
    public function get(string $path, callable|array $handler): void
    {
        $this->router->add('GET', $path, $handler);
    }
    public function post(string $path, callable|array $handler): void
    {
        $this->router->add('POST', $path, $handler);
    }
    public function put(string $path, callable|array $handler): void
    {
        $this->router->add('PUT', $path, $handler);
    }
    public function patch(string $path, callable|array $handler): void
    {
        $this->router->add('PATCH', $path, $handler);
    }
    public function delete(string $path, callable|array $handler): void
    {
        $this->router->add('DELETE', $path, $handler);
    }
    public function any(string $path, callable|array $handler): void
    {
        foreach (['GET','POST','PUT','PATCH','DELETE'] as $method) {
            $this->router->add($method, $path, $handler);
        }
    }
    public function basePath(): string
    {
        return $this->basePath;
    }
    public function config(): array
    {
        return $this->config;
    }
    public function router(): Router
    {
        return $this->router;
    }
    public function request(): Request
    {
        return $this->request;
    }
    public function run(): void
    {
        if (isset($this->config['timezone']) && is_string($this->config['timezone'])) {
            @date_default_timezone_set($this->config['timezone']);
        }
        $this->router->dispatch($this->request);
    }
}
