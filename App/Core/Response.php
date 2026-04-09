<?php
declare(strict_types=1);
namespace App\Core;

final class Response
{
    private int $status;
    private array $headers;
    private string $body;
    public function __construct(string $body = '', int $status = 200, array $headers = [])
    {
        $this->status  = $status;
        $this->headers = $headers;
        $this->body    = $body;
    }
    public static function text(string $text, int $status = 200): self
    {
        return new self($text, $status, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }
    public static function html(string $html, int $status = 200): self
    {
        return new self($html, $status, [
            'Content-Type' => 'text/html; charset=utf-8',
        ]);
    }
    public static function json(array|object $data, int $status = 200): self
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if ($json === false) {
            $json = '{"error":"JSON encoding failed"}';
            $status = 500;
        }
        return new self($json, $status, [
            'Content-Type' => 'application/json; charset=utf-8',
        ]);
    }
    public static function redirect(string $url, int $status = 302): self
    {
        // Prevent open redirects: only allow relative paths or same-origin URLs
        if (preg_match('#^https?://#i', $url)) {
            $host = parse_url($url, PHP_URL_HOST);
            $currentHost = $_SERVER['HTTP_HOST'] ?? '';
            if ($host !== $currentHost) {
                $url = '/';
            }
        }
        // Prevent CRLF injection in redirect URL
        $url = str_replace(["\r", "\n", "\0"], '', $url);
        return new self('', $status, [
            'Location' => $url,
        ]);
    }
    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }
    public function status(int $status): self
    {
        $this->status = $status;
        return $this;
    }
    public function append(string $content): self
    {
        $this->body .= $content;
        return $this;
    }
    public function body(): string
    {
        return $this->body;
    }
    public function send(): void
    {
        if (!headers_sent()) {
            http_response_code($this->status);
            foreach ($this->headers as $name => $value) {
                // Prevent CRLF header injection
                $name = str_replace(["\r", "\n", "\0"], '', (string) $name);
                $value = str_replace(["\r", "\n", "\0"], '', (string) $value);
                header("$name: $value");
            }
        }

        echo $this->body;
    }
    public function dump(): void
    {
        echo "HTTP/1.1 $this->status\n";
        foreach ($this->headers as $k => $v) {
            echo "$k: $v\n";
        }
        echo "\n$this->body";
    }
}