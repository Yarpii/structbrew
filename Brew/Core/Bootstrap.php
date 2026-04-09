<?php
declare(strict_types=1);
namespace Brew\Core;

use Throwable;

final class Bootstrap
{
    public static function run(): void
    {
        $rootPath   = dirname(__DIR__, 2);
        $brewPath   = $rootPath . '/Brew';
        $routesPath = $brewPath . '/Routes';
        spl_autoload_register(function (string $class) use ($brewPath): void {
            $prefix  = 'Brew\\';
            $baseDir = $brewPath . '/';
            if (!str_starts_with($class, $prefix)) {
                return;
            }
            $relative = substr($class, strlen($prefix));
            $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
            if (is_file($file)) {
                require $file;
            }
        });
        try {
            $app = new App($rootPath, [
                'debug'       => true,
                'timezone'    => 'Europe/Amsterdam',
                'routes_path' => $routesPath,
            ]);
            $app->run();
        } catch (Throwable $e) {
            self::handleFatal($e);
        }
    }
    private static function handleFatal(Throwable $e): void
    {
        http_response_code(500);
        header('Content-Type: text/plain; charset=utf-8');
        echo "Fatal error in Structbrew bootstrap:\n\n";
        echo $e->getMessage() . "\n\n" . $e->getTraceAsString();
    }
}