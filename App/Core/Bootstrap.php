<?php
declare(strict_types=1);
namespace App\Core;

use App\Controllers\SetupController;
use Throwable;

final class Bootstrap
{
    public static function run(): void
    {
        $rootPath  = dirname(__DIR__, 2);
        $appPath   = $rootPath . '/App';
        $routesPath = $appPath . '/Routes';
        spl_autoload_register(function (string $class) use ($appPath): void {
            $prefix  = 'App\\';
            $baseDir = $appPath . '/';
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
            // Load configuration
            Config::load($rootPath . '/config');

            // Start session
            Session::start();

            // Check if setup is needed (no .env file = new installation)
            if (SetupController::needsSetup()) {
                $uri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
                $uri = rtrim($uri, '/') ?: '/';

                // Allow setup routes and static assets through
                if (!str_starts_with($uri, '/setup') && !str_starts_with($uri, '/assets')) {
                    header('Location: /setup');
                    exit;
                }

                // Run only the setup routes
                $app = new App($rootPath, [
                    'debug'       => true,
                    'timezone'    => Config::get('app.timezone', 'UTC'),
                    'routes_path' => $routesPath,
                ]);
                $app->run();
                return;
            }

            // Share auth state and CSRF token with all frontend views
            View::share('csrfToken', Session::csrfToken());
            View::share('isLoggedIn', Auth::isLoggedIn());
            View::share('currentCustomer', Auth::isLoggedIn() ? Auth::customer() : null);

            // Initialize store resolver (multi-store domain mapping)
            try {
                StoreResolver::resolve();
                // Set translator locale from store view
                $locale = StoreResolver::locale();
                Translator::setLocale($locale);
                Translator::loadFromDatabase();

                // Share store data with all views
                View::share('currentLocale', $locale);
                View::share('currentLanguage', StoreResolver::language());
                View::share('currentCurrency', StoreResolver::currency());
                View::share('currentCurrencySymbol', StoreResolver::currencySymbol());
                View::share('currentCountry', StoreResolver::country());
                View::share('currentStoreView', StoreResolver::storeView());
            } catch (Throwable $e) {
                // Database might not be set up yet — continue without store resolution
            }

            $app = new App($rootPath, [
                'debug'       => Config::get('app.debug', false),
                'timezone'    => Config::get('app.timezone', 'Europe/Amsterdam'),
                'routes_path' => $routesPath,
            ]);
            $app->run();
        } catch (Throwable $e) {
            self::handleFatal($e);
        }
    }
    private static function handleFatal(Throwable $e): void
    {
        error_log("StructBrew Fatal: " . $e->getMessage() . "\n" . $e->getTraceAsString());

        http_response_code(500);
        header('Content-Type: text/plain; charset=utf-8');

        $debug = Config::get('app.debug', false);
        if ($debug) {
            echo "Fatal error in Structbrew bootstrap:\n\n";
            echo htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "\n\n";
            echo htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8');
        } else {
            echo "An internal error occurred. Please try again later.";
        }
    }
}