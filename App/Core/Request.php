<?php
declare(strict_types=1);

namespace App\Core;

final class Request
{
    private string $method;
    private string $uri;
    private string $path;
    private array $headers;
    private array $query;
    private array $body;
    private ?array $json = null;
    public function __construct(string $method, string $uri, array $headers = [], array $query = [], array $body = [])
    {
        $this->method  = strtoupper($method);
        $this->uri     = $uri;
        $this->path    = parse_url($uri, PHP_URL_PATH) ?? '/';
        $this->headers = $headers;
        $this->query   = $query;
        $this->body    = $body;
    }
    public static function fromGlobals(): self
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri    = $_SERVER['REQUEST_URI'] ?? '/';
        $query = $_GET ?? [];
        $body = $_POST ?? [];
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace('_', '-', strtolower(substr($key, 5)));
                $headers[$name] = $value;
            }
        }
        $contentType = $headers['content-type'] ?? '';
        if (empty($body) && in_array(strtoupper($method), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            $raw = file_get_contents('php://input') ?: '';
            if (str_contains($contentType, 'application/json')) {
                $json = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
                    $body = $json;
                }
            } elseif (str_contains($contentType, 'application/x-www-form-urlencoded')) {
                parse_str($raw, $parsed);
                $body = is_array($parsed) ? $parsed : [];
            } elseif (str_contains($contentType, 'multipart/form-data')) {
                $body = $_POST ?? [];
            }
        }
        return new self($method, $uri, $headers, $query, $body);
    }
    public function method(): string
    {
        return $this->method;
    }
    public function uri(): string
    {
        return $this->uri;
    }
    public function path(): string
    {
        return $this->path;
    }
    public function query(): array
    {
        return $this->query;
    }
    public function body(): array
    {
        return $this->body;
    }
    public function header(string $name, mixed $default = null): mixed
    {
        $key = strtolower($name);
        return $this->headers[$key] ?? $default;
    }
    public function input(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->body)) {
            return $this->body[$key];
        }
        if (array_key_exists($key, $this->query)) {
            return $this->query[$key];
        }
        return $default;
    }
    public function json(): ?array
    {
        if ($this->json !== null) {
            return $this->json;
        }
        $contentType = $this->header('content-type');
        if ($contentType && str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input') ?: '';
            $decoded = json_decode($raw, true);
            $this->json = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : null;
        }
        return $this->json;
    }
    public function isJson(): bool
    {
        $contentType = $this->header('content-type');
        return $contentType && str_contains($contentType, 'application/json');
    }
    public function isAjax(): bool
    {
        return strtolower((string) $this->header('x-requested-with')) === 'xmlhttprequest';
    }
    public function ip(): ?string
    {
        return $_SERVER['REMOTE_ADDR'] ?? null;
    }
    public function url(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return "$scheme://$host$this->uri";
    }
}