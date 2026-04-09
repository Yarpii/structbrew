<?php
declare(strict_types=1);
namespace App\Core;

use RuntimeException;
use Throwable;

final class View
{
    private static string $viewsPath = '';
    private static array $sharedData = [];
    private static ?string $defaultLayout = 'layout/app';
    public static function setViewsPath(string $path): void
    {
        self::$viewsPath = rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }
    public static function share(string $key, mixed $value): void
    {
        self::$sharedData[$key] = $value;
    }
    public static function setDefaultLayout(?string $layout): void
    {
        self::$defaultLayout = $layout;
    }
    public static function exists(string $view): bool
    {
        return is_file(self::resolvePath($view));
    }
    public static function render(string $view, array $data = []): string
    {
        $path = self::resolvePath($view);
        if (!is_file($path)) {
            throw new RuntimeException("View not found: " . basename($view));
        }
        $data = array_merge(self::$sharedData, $data);

        // Render in isolated closure scope to prevent variable pollution
        $content = self::renderFile($path, $data);

        if (self::$defaultLayout && self::exists(self::$defaultLayout)) {
            $layoutPath = self::resolvePath(self::$defaultLayout);
            return self::renderFile($layoutPath, $data + ['content' => $content]);
        }
        return $content;
    }
    private static function renderFile(string $__path, array $__data): string
    {
        // Use closure to isolate variable scope from the rest of the application
        $__render = static function (string $__file, array $__vars): string {
            extract($__vars, EXTR_SKIP);
            ob_start();
            try {
                include $__file;
            } catch (Throwable $e) {
                ob_end_clean();
                throw $e;
            }
            return ob_get_clean() ?: '';
        };
        return $__render($__path, $__data);
    }
    private static function resolvePath(string $view): string
    {
        if (self::$viewsPath === '') {
            self::$viewsPath = dirname(__DIR__) . '/Views/';
        }
        $relative = str_replace('.', '/', $view) . '.php';
        return self::$viewsPath . $relative;
    }
}