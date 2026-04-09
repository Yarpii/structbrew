<?php

declare(strict_types=1);

namespace App\Core;

class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        $config = Config::get('session', []);

        session_set_cookie_params([
            'lifetime' => $config['lifetime'] ?? 7200,
            'path' => '/',
            'domain' => $config['domain'] ?? '',
            'secure' => $config['secure'] ?? false,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        $savePath = $config['save_path'] ?? dirname(__DIR__, 2) . '/storage/sessions';
        if (!is_dir($savePath)) {
            mkdir($savePath, 0755, true);
        }
        session_save_path($savePath);
        session_name($config['name'] ?? 'brew_session');

        session_start();
        self::$started = true;

        // Regenerate session ID periodically to prevent fixation
        if (!self::has('_created')) {
            self::set('_created', time());
        } elseif (time() - self::get('_created') > 1800) {
            session_regenerate_id(true);
            self::set('_created', time());
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::ensureStarted();
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        self::ensureStarted();
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        self::ensureStarted();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::ensureStarted();
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, mixed $value): void
    {
        self::ensureStarted();
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key, mixed $default = null): mixed
    {
        self::ensureStarted();
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    public static function hasFlash(string $key): bool
    {
        self::ensureStarted();
        return isset($_SESSION['_flash'][$key]);
    }

    public static function destroy(): void
    {
        self::ensureStarted();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        session_destroy();
        self::$started = false;
    }

    public static function regenerate(): void
    {
        self::ensureStarted();
        session_regenerate_id(true);
    }

    public static function id(): string
    {
        self::ensureStarted();
        return session_id();
    }

    public static function csrfToken(): string
    {
        self::ensureStarted();
        if (!self::has('_csrf_token')) {
            self::set('_csrf_token', bin2hex(random_bytes(32)));
        }
        return self::get('_csrf_token');
    }

    public static function verifyCsrf(string $token): bool
    {
        return hash_equals(self::csrfToken(), $token);
    }

    private static function ensureStarted(): void
    {
        if (!self::$started) {
            self::start();
        }
    }
}
