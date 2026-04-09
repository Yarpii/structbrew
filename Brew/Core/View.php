<?php
declare(strict_types=1);
namespace Brew\Core;

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
            throw new RuntimeException("View not found: $view (searched in: $path)");
        }
        $data = array_merge(self::$sharedData, $data);
        extract($data, EXTR_SKIP);
        ob_start();
        try {
            include $path;
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }
        $content = ob_get_clean() ?: '';
        if (self::$defaultLayout && self::exists(self::$defaultLayout)) {
            $layoutPath = self::resolvePath(self::$defaultLayout);
            ob_start();
            include $layoutPath;
            return ob_get_clean() ?: $content;
        }
        return $content;
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