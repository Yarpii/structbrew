<?php

declare(strict_types=1);

namespace App\Core;

class Cache
{
    private static ?string $path = null;

    private static function path(): string
    {
        if (self::$path === null) {
            self::$path = Config::get('cache.path', dirname(__DIR__, 2) . '/storage/cache');
            if (!is_dir(self::$path)) {
                mkdir(self::$path, 0755, true);
            }
        }
        return self::$path;
    }

    private static function file(string $key): string
    {
        return self::path() . '/' . md5($key) . '.cache';
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $file = self::file($key);
        if (!file_exists($file)) return $default;

        $raw = file_get_contents($file);
        if ($raw === false) return $default;
        $data = json_decode($raw, true);
        if (!is_array($data) || !array_key_exists('value', $data) || !array_key_exists('expires', $data)) {
            unlink($file);
            return $default;
        }
        if ($data['expires'] !== 0 && $data['expires'] < time()) {
            unlink($file);
            return $default;
        }

        return $data['value'];
    }

    public static function set(string $key, mixed $value, int $ttl = 3600): void
    {
        $data = [
            'value' => $value,
            'expires' => $ttl > 0 ? time() + $ttl : 0,
        ];
        file_put_contents(self::file($key), json_encode($data, JSON_THROW_ON_ERROR), LOCK_EX);
    }

    public static function has(string $key): bool
    {
        return self::get($key, '__CACHE_MISS__') !== '__CACHE_MISS__';
    }

    public static function forget(string $key): void
    {
        $file = self::file($key);
        if (file_exists($file)) {
            unlink($file);
        }
    }

    public static function remember(string $key, int $ttl, callable $callback): mixed
    {
        $value = self::get($key);
        if ($value !== null) return $value;

        $value = $callback();
        self::set($key, $value, $ttl);
        return $value;
    }

    public static function flush(): void
    {
        $files = glob(self::path() . '/*.cache');
        foreach ($files as $file) {
            unlink($file);
        }
    }

    public static function flushExpired(): int
    {
        $count = 0;
        $files = glob(self::path() . '/*.cache');
        foreach ($files as $file) {
            $raw = file_get_contents($file);
            if ($raw === false) continue;
            $data = json_decode($raw, true);
            if (!is_array($data) || !array_key_exists('expires', $data)) {
                unlink($file);
                $count++;
                continue;
            }
            if ($data['expires'] !== 0 && $data['expires'] < time()) {
                unlink($file);
                $count++;
            }
        }
        return $count;
    }
}
