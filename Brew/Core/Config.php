<?php

declare(strict_types=1);

namespace Brew\Core;

class Config
{
    private static array $config = [];
    private static bool $loaded = false;

    public static function load(string $configPath): void
    {
        if (self::$loaded) return;

        $configFile = $configPath . '/config.php';
        if (file_exists($configFile)) {
            self::$config = require $configFile;
        }

        // Load environment overrides
        $envFile = dirname($configPath) . '/.env';
        if (file_exists($envFile)) {
            self::loadEnv($envFile);
        }

        self::$loaded = true;
    }

    private static function loadEnv(string $path): void
    {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) continue;

            $parts = explode('=', $line, 2);
            if (count($parts) !== 2) continue;

            $key = trim($parts[0]);
            $value = trim($parts[1], " \t\n\r\0\x0B\"'");

            $_ENV[$key] = $value;
            putenv("{$key}={$value}");
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        // Support dot notation: 'database.host'
        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $k) {
            if (!is_array($value) || !array_key_exists($k, $value)) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    public static function set(string $key, mixed $value): void
    {
        $keys = explode('.', $key);
        $config = &self::$config;

        foreach ($keys as $i => $k) {
            if ($i === count($keys) - 1) {
                $config[$k] = $value;
            } else {
                if (!isset($config[$k]) || !is_array($config[$k])) {
                    $config[$k] = [];
                }
                $config = &$config[$k];
            }
        }
    }

    public static function has(string $key): bool
    {
        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $k) {
            if (!is_array($value) || !array_key_exists($k, $value)) {
                return false;
            }
            $value = $value[$k];
        }

        return true;
    }

    public static function all(): array
    {
        return self::$config;
    }

    public static function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? getenv($key);
        if ($value === false) return $default;

        return match (strtolower((string) $value)) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'null', '(null)' => null,
            default => $value,
        };
    }
}
