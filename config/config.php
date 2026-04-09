<?php

declare(strict_types=1);

use Brew\Core\Config;

return [
    // ─── Application ─────────────────────────────────────────
    'app' => [
        'name' => Config::env('APP_NAME', 'StructBrew'),
        'url' => Config::env('APP_URL', 'http://localhost'),
        'debug' => Config::env('APP_DEBUG', true),
        'timezone' => Config::env('APP_TIMEZONE', 'UTC'),
    ],

    // ─── Database ────────────────────────────────────────────
    'database' => [
        'host' => Config::env('DB_HOST', '127.0.0.1'),
        'port' => (int) Config::env('DB_PORT', 3306),
        'name' => Config::env('DB_NAME', 'structbrew'),
        'user' => Config::env('DB_USER', 'root'),
        'pass' => Config::env('DB_PASS', ''),
        'charset' => 'utf8mb4',
        'prefix' => Config::env('DB_PREFIX', ''),
    ],

    // ─── Session ─────────────────────────────────────────────
    'session' => [
        'name' => 'brew_session',
        'lifetime' => 7200,
        'save_path' => dirname(__DIR__) . '/storage/sessions',
        'secure' => Config::env('SESSION_SECURE', false),
    ],

    // ─── Cache ───────────────────────────────────────────────
    'cache' => [
        'path' => dirname(__DIR__) . '/storage/cache',
        'ttl' => 3600,
    ],

    // ─── Mail ────────────────────────────────────────────────
    'mail' => [
        'driver' => Config::env('MAIL_DRIVER', 'smtp'),
        'host' => Config::env('MAIL_HOST', 'localhost'),
        'port' => (int) Config::env('MAIL_PORT', 587),
        'username' => Config::env('MAIL_USERNAME', ''),
        'password' => Config::env('MAIL_PASSWORD', ''),
        'from_address' => Config::env('MAIL_FROM_ADDRESS', 'noreply@structbrew.com'),
        'from_name' => Config::env('MAIL_FROM_NAME', 'StructBrew'),
    ],

    // ─── Uploads ─────────────────────────────────────────────
    'uploads' => [
        'path' => dirname(__DIR__) . '/public/uploads',
        'url' => '/uploads',
        'max_size' => 5 * 1024 * 1024, // 5MB
        'allowed_types' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'],
    ],

    // ─── Default Store ───────────────────────────────────────
    'store' => [
        'default_locale' => 'en_US',
        'default_currency' => 'EUR',
        'default_theme' => 'default',
        'fallback_locale' => 'en_US',
    ],
];
