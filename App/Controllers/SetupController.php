<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Config;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Migration;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;
use PDO;
use PDOException;
use Throwable;

class SetupController extends Controller
{
    private string $rootPath;

    public function __construct()
    {
        parent::__construct();
        $this->rootPath = dirname(__DIR__, 2);
        View::setDefaultLayout(null);
    }

    /**
     * Check if setup is needed (no .env file exists).
     */
    public static function needsSetup(): bool
    {
        $rootPath = dirname(__DIR__, 2);
        return !file_exists($rootPath . '/.env');
    }

    /**
     * Step 1: Welcome & requirements check.
     */
    public function index(): Response
    {
        $checks = $this->checkRequirements();
        $allPassed = !in_array(false, array_column($checks, 'passed'), true);

        return $this->renderSetup('setup/index', [
            'checks' => $checks,
            'allPassed' => $allPassed,
            'step' => 1,
        ]);
    }

    /**
     * Step 2: Database configuration form.
     */
    public function database(): Response
    {
        $saved = Session::get('setup_database', []);

        return $this->renderSetup('setup/database', [
            'step' => 2,
            'db' => array_merge([
                'host' => '127.0.0.1',
                'port' => '3306',
                'name' => 'structbrew',
                'user' => 'root',
                'pass' => '',
                'prefix' => '',
            ], $saved),
            'error' => Session::getFlash('setup_error'),
        ]);
    }

    /**
     * Step 2: Test database connection and save.
     */
    public function databaseSave(): Response
    {
        $data = [
            'host' => trim($this->input('db_host', '127.0.0.1')),
            'port' => trim($this->input('db_port', '3306')),
            'name' => trim($this->input('db_name', 'structbrew')),
            'user' => trim($this->input('db_user', 'root')),
            'pass' => $this->input('db_pass', ''),
            'prefix' => trim($this->input('db_prefix', '')),
        ];

        // Test the connection
        $error = $this->testDatabaseConnection($data);
        if ($error !== null) {
            Session::flash('setup_error', $error);
            Session::set('setup_database', $data);
            return $this->redirect('/setup/database');
        }

        Session::set('setup_database', $data);
        return $this->redirect('/setup/application');
    }

    /**
     * Step 3: Application settings form.
     */
    public function application(): Response
    {
        if (!Session::has('setup_database')) {
            return $this->redirect('/setup/database');
        }

        $saved = Session::get('setup_application', []);

        return $this->renderSetup('setup/application', [
            'step' => 3,
            'app' => array_merge([
                'name' => 'StructBrew',
                'url' => $this->guessAppUrl(),
                'timezone' => 'UTC',
                'debug' => 'true',
                'mail_host' => 'localhost',
                'mail_port' => '587',
                'mail_username' => '',
                'mail_password' => '',
                'mail_from_address' => 'noreply@structbrew.com',
                'mail_from_name' => 'StructBrew',
            ], $saved),
            'timezones' => $this->getTimezones(),
            'error' => Session::getFlash('setup_error'),
        ]);
    }

    /**
     * Step 3: Save application settings.
     */
    public function applicationSave(): Response
    {
        $data = [
            'name' => trim($this->input('app_name', 'StructBrew')),
            'url' => rtrim(trim($this->input('app_url', 'http://localhost')), '/'),
            'timezone' => trim($this->input('app_timezone', 'UTC')),
            'debug' => $this->input('app_debug', 'true'),
            'mail_host' => trim($this->input('mail_host', 'localhost')),
            'mail_port' => trim($this->input('mail_port', '587')),
            'mail_username' => trim($this->input('mail_username', '')),
            'mail_password' => $this->input('mail_password', ''),
            'mail_from_address' => trim($this->input('mail_from_address', 'noreply@structbrew.com')),
            'mail_from_name' => trim($this->input('mail_from_name', 'StructBrew')),
        ];

        Session::set('setup_application', $data);
        return $this->redirect('/setup/admin');
    }

    /**
     * Step 4: Admin account creation form.
     */
    public function admin(): Response
    {
        if (!Session::has('setup_database') || !Session::has('setup_application')) {
            return $this->redirect('/setup');
        }

        return $this->renderSetup('setup/admin', [
            'step' => 4,
            'error' => Session::getFlash('setup_error'),
        ]);
    }

    /**
     * Step 4: Run the full installation.
     */
    public function install(): Response
    {
        $dbConfig = Session::get('setup_database');
        $appConfig = Session::get('setup_application');
        $adminEmail = trim($this->input('admin_email', ''));
        $adminPassword = $this->input('admin_password', '');
        $adminPasswordConfirm = $this->input('admin_password_confirm', '');
        $adminFirstName = trim($this->input('admin_first_name', ''));
        $adminLastName = trim($this->input('admin_last_name', ''));

        if (!$dbConfig || !$appConfig) {
            return $this->redirect('/setup');
        }

        // Validate admin fields
        if ($adminEmail === '' || $adminPassword === '' || $adminFirstName === '') {
            Session::flash('setup_error', 'Please fill in all required fields (email, password, first name).');
            return $this->redirect('/setup/admin');
        }

        if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
            Session::flash('setup_error', 'Please enter a valid email address.');
            return $this->redirect('/setup/admin');
        }

        if (strlen($adminPassword) < 8) {
            Session::flash('setup_error', 'Password must be at least 8 characters long.');
            return $this->redirect('/setup/admin');
        }

        if ($adminPassword !== $adminPasswordConfirm) {
            Session::flash('setup_error', 'Passwords do not match.');
            return $this->redirect('/setup/admin');
        }

        // 1. Write .env file
        $envContent = $this->buildEnvContent($dbConfig, $appConfig);
        $envWritten = @file_put_contents($this->rootPath . '/.env', $envContent);
        if ($envWritten === false) {
            Session::flash('setup_error', 'Could not write .env file. Check that the root directory is writable.');
            return $this->redirect('/setup/admin');
        }

        // 2. Reload config so Database class picks up the new settings
        $this->reloadConfig($dbConfig, $appConfig);

        // 3. Create storage directories
        $this->ensureDirectories();

        // Reset the Database singleton so it picks up the new config
        Database::resetInstance();

        try {
            // 4. Run migrations
            $migration = new Migration();
            $migrationResult = $migration->run();

            // 5. Seed the database
            require_once $this->rootPath . '/App/Data/Seeder.php';
            \App\Data\Seeder::run();

            // 6. Create admin user (may already exist from seeder, update if so)
            $db = Database::getInstance();
            $existingAdmin = $db->table('admin_users')->where('email', $adminEmail)->first();
            if (!$existingAdmin) {
                $role = $db->table('admin_roles')->where('name', 'Super Admin')->first();
                $roleId = $role ? $role['id'] : $db->table('admin_roles')->insert([
                    'name' => 'Super Admin',
                    'permissions' => json_encode(['*']),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $db->table('admin_users')->insert([
                    'role_id' => $roleId,
                    'email' => $adminEmail,
                    'password_hash' => Auth::hashPassword($adminPassword),
                    'first_name' => $adminFirstName,
                    'last_name' => $adminLastName,
                    'is_active' => 1,
                    'is_superadmin' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }

            // Clean up session data
            Session::remove('setup_database');
            Session::remove('setup_application');
            Session::set('setup_complete', true);
            Session::set('setup_admin_email', $adminEmail);
            Session::set('setup_migrations', $migrationResult['migrations'] ?? []);

            return $this->redirect('/setup/complete');
        } catch (Throwable $e) {
            // Remove .env on failure so setup can be retried
            @unlink($this->rootPath . '/.env');
            Session::flash('setup_error', 'Installation failed: ' . $e->getMessage());
            return $this->redirect('/setup/admin');
        }
    }

    /**
     * Step 5: Installation complete.
     */
    public function complete(): Response
    {
        if (!Session::get('setup_complete')) {
            return $this->redirect('/setup');
        }

        $adminEmail = Session::get('setup_admin_email', '');
        $migrations = Session::get('setup_migrations', []);

        // Clean up
        Session::remove('setup_complete');
        Session::remove('setup_admin_email');
        Session::remove('setup_migrations');

        return $this->renderSetup('setup/complete', [
            'step' => 5,
            'adminEmail' => $adminEmail,
            'migrationCount' => count($migrations),
        ]);
    }

    // ─── Helpers ─────────────────────────────────────────────

    private function renderSetup(string $view, array $data = []): Response
    {
        $data['csrfToken'] = Session::csrfToken();
        $html = View::render($view, $data);
        return Response::html($html);
    }

    private function checkRequirements(): array
    {
        $checks = [];

        // PHP version
        $checks[] = [
            'name' => 'PHP Version',
            'required' => '>= 8.1',
            'current' => PHP_VERSION,
            'passed' => version_compare(PHP_VERSION, '8.1.0', '>='),
        ];

        // PDO MySQL extension
        $checks[] = [
            'name' => 'PDO MySQL Extension',
            'required' => 'Enabled',
            'current' => extension_loaded('pdo_mysql') ? 'Enabled' : 'Missing',
            'passed' => extension_loaded('pdo_mysql'),
        ];

        // mbstring extension
        $checks[] = [
            'name' => 'mbstring Extension',
            'required' => 'Enabled',
            'current' => extension_loaded('mbstring') ? 'Enabled' : 'Missing',
            'passed' => extension_loaded('mbstring'),
        ];

        // JSON extension
        $checks[] = [
            'name' => 'JSON Extension',
            'required' => 'Enabled',
            'current' => extension_loaded('json') ? 'Enabled' : 'Missing',
            'passed' => extension_loaded('json'),
        ];

        // Session extension
        $checks[] = [
            'name' => 'Session Extension',
            'required' => 'Enabled',
            'current' => extension_loaded('session') ? 'Enabled' : 'Missing',
            'passed' => extension_loaded('session'),
        ];

        // Root directory writable (for .env)
        $rootWritable = is_writable($this->rootPath);
        $checks[] = [
            'name' => 'Root Directory Writable',
            'required' => 'Writable',
            'current' => $rootWritable ? 'Writable' : 'Not writable',
            'passed' => $rootWritable,
        ];

        // Storage directory
        $storagePath = $this->rootPath . '/storage';
        $storageExists = is_dir($storagePath);
        $storageWritable = $storageExists && is_writable($storagePath);
        if (!$storageExists) {
            // Try to create it
            $storageWritable = @mkdir($storagePath, 0755, true);
        }
        $checks[] = [
            'name' => 'Storage Directory',
            'required' => 'Writable',
            'current' => $storageWritable ? 'Writable' : 'Not writable',
            'passed' => $storageWritable,
        ];

        // Public uploads directory
        $uploadsPath = $this->rootPath . '/public/uploads';
        $uploadsExists = is_dir($uploadsPath);
        $uploadsWritable = $uploadsExists && is_writable($uploadsPath);
        if (!$uploadsExists) {
            $uploadsWritable = @mkdir($uploadsPath, 0755, true);
        }
        $checks[] = [
            'name' => 'Public Uploads Directory',
            'required' => 'Writable',
            'current' => $uploadsWritable ? 'Writable' : 'Not writable',
            'passed' => $uploadsWritable,
        ];

        return $checks;
    }

    private function testDatabaseConnection(array $config): ?string
    {
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;charset=utf8mb4',
                $config['host'],
                $config['port']
            );
            $pdo = new PDO($dsn, $config['user'], $config['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 5,
            ]);

            // Check if database exists, create if not
            $dbName = $config['name'];
            $stmt = $pdo->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = " . $pdo->quote($dbName));
            if (!$stmt->fetch()) {
                $pdo->exec("CREATE DATABASE `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            }

            // Verify we can connect to the specific database
            $pdo->exec("USE `{$dbName}`");

            return null;
        } catch (PDOException $e) {
            return 'Database connection failed: ' . $e->getMessage();
        }
    }

    private function buildEnvContent(array $db, array $app): string
    {
        return implode("\n", [
            '# StructBrew Configuration',
            '# Generated by the web setup wizard on ' . date('Y-m-d H:i:s'),
            '',
            '# Application',
            'APP_NAME=' . $app['name'],
            'APP_URL=' . $app['url'],
            'APP_DEBUG=' . $app['debug'],
            'APP_TIMEZONE=' . $app['timezone'],
            '',
            '# Database',
            'DB_HOST=' . $db['host'],
            'DB_PORT=' . $db['port'],
            'DB_NAME=' . $db['name'],
            'DB_USER=' . $db['user'],
            'DB_PASS=' . $db['pass'],
            'DB_PREFIX=' . $db['prefix'],
            '',
            '# Session',
            'SESSION_SECURE=false',
            '',
            '# Mail (SMTP)',
            'MAIL_DRIVER=smtp',
            'MAIL_HOST=' . $app['mail_host'],
            'MAIL_PORT=' . $app['mail_port'],
            'MAIL_USERNAME=' . $app['mail_username'],
            'MAIL_PASSWORD=' . $app['mail_password'],
            'MAIL_FROM_ADDRESS=' . $app['mail_from_address'],
            'MAIL_FROM_NAME=' . $app['mail_from_name'],
            '',
        ]);
    }

    private function reloadConfig(array $db, array $app): void
    {
        // Put env vars so Config::env() picks them up
        $envVars = [
            'APP_NAME' => $app['name'],
            'APP_URL' => $app['url'],
            'APP_DEBUG' => $app['debug'],
            'APP_TIMEZONE' => $app['timezone'],
            'DB_HOST' => $db['host'],
            'DB_PORT' => $db['port'],
            'DB_NAME' => $db['name'],
            'DB_USER' => $db['user'],
            'DB_PASS' => $db['pass'],
            'DB_PREFIX' => $db['prefix'],
            'MAIL_HOST' => $app['mail_host'],
            'MAIL_PORT' => $app['mail_port'],
            'MAIL_USERNAME' => $app['mail_username'],
            'MAIL_PASSWORD' => $app['mail_password'],
            'MAIL_FROM_ADDRESS' => $app['mail_from_address'],
            'MAIL_FROM_NAME' => $app['mail_from_name'],
        ];

        foreach ($envVars as $key => $value) {
            $_ENV[$key] = $value;
            putenv("{$key}={$value}");
        }

        // Update Config values directly
        Config::set('database.host', $db['host']);
        Config::set('database.port', (int) $db['port']);
        Config::set('database.name', $db['name']);
        Config::set('database.user', $db['user']);
        Config::set('database.pass', $db['pass']);
        Config::set('database.prefix', $db['prefix']);
    }

    private function ensureDirectories(): void
    {
        $dirs = [
            $this->rootPath . '/storage',
            $this->rootPath . '/storage/sessions',
            $this->rootPath . '/storage/cache',
            $this->rootPath . '/public/uploads',
        ];

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
        }
    }

    private function guessAppUrl(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $scheme . '://' . $host;
    }

    private function getTimezones(): array
    {
        return [
            'UTC' => 'UTC',
            'Europe/Amsterdam' => 'Europe/Amsterdam',
            'Europe/Berlin' => 'Europe/Berlin',
            'Europe/Paris' => 'Europe/Paris',
            'Europe/London' => 'Europe/London',
            'Europe/Brussels' => 'Europe/Brussels',
            'Europe/Rome' => 'Europe/Rome',
            'Europe/Madrid' => 'Europe/Madrid',
            'Europe/Warsaw' => 'Europe/Warsaw',
            'Europe/Lisbon' => 'Europe/Lisbon',
            'Europe/Zurich' => 'Europe/Zurich',
            'Europe/Vienna' => 'Europe/Vienna',
            'America/New_York' => 'America/New York',
            'America/Chicago' => 'America/Chicago',
            'America/Denver' => 'America/Denver',
            'America/Los_Angeles' => 'America/Los Angeles',
            'Asia/Tokyo' => 'Asia/Tokyo',
            'Asia/Shanghai' => 'Asia/Shanghai',
            'Asia/Kolkata' => 'Asia/Kolkata',
            'Australia/Sydney' => 'Australia/Sydney',
        ];
    }
}
