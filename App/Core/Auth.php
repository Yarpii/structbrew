<?php

declare(strict_types=1);

namespace App\Core;

class Auth
{
    private static ?array $user = null;
    private static ?array $admin = null;

    // ─── Brute-force protection ────────────────────────────────

    private static function isRateLimited(string $key): bool
    {
        $attempts = Cache::get($key, 0);
        return $attempts >= 5;
    }

    private static function incrementAttempts(string $key): void
    {
        $attempts = Cache::get($key, 0);
        Cache::set($key, $attempts + 1, 900); // 15 min lockout window
    }

    private static function clearAttempts(string $key): void
    {
        Cache::forget($key);
    }

    // ─── Customer Auth ───────────────────────────────────────

    public static function attempt(string $email, string $password): bool
    {
        // Rate limit by email to prevent brute force
        $rateLimitKey = 'login_attempts:' . hash('sha256', strtolower($email));
        if (self::isRateLimited($rateLimitKey)) {
            usleep(random_int(400000, 600000)); // Constant-ish delay
            return false;
        }

        $db = Database::getInstance();
        $customer = $db->table('customers')
            ->where('email', $email)
            ->where('is_active', 1)
            ->first();

        // Constant-time comparison: always run password_verify to prevent timing attacks
        $dummyHash = '$2y$12$StructBrewDummyHashForTimingAttackPreventionXXXXXXXXX';
        $hash = $customer['password_hash'] ?? $dummyHash;
        $valid = password_verify($password, $hash);

        if (!$customer || !$valid) {
            self::incrementAttempts($rateLimitKey);
            usleep(random_int(200000, 500000)); // Add jitter to prevent timing analysis
            return false;
        }

        self::clearAttempts($rateLimitKey);
        self::loginCustomer($customer);
        return true;
    }

    public static function loginCustomer(array $customer): void
    {
        Session::regenerate();
        Session::set('customer_id', $customer['id']);
        Session::set('customer_email', $customer['email']);
        self::$user = $customer;

        // Update last login
        Database::getInstance()->table('customers')
            ->where('id', $customer['id'])
            ->update(['last_login_at' => date('Y-m-d H:i:s')]);
    }

    public static function customer(): ?array
    {
        if (self::$user !== null) return self::$user;

        $id = Session::get('customer_id');
        if (!$id) return null;

        self::$user = Database::getInstance()->table('customers')
            ->where('id', $id)
            ->first();

        return self::$user;
    }

    public static function customerId(): ?int
    {
        return Session::get('customer_id') ? (int) Session::get('customer_id') : null;
    }

    public static function isLoggedIn(): bool
    {
        return Session::has('customer_id');
    }

    public static function logout(): void
    {
        self::$user = null;
        Session::remove('customer_id');
        Session::remove('customer_email');
        Session::regenerate();
    }

    // ─── Admin Auth ──────────────────────────────────────────

    public static function adminAttempt(string $email, string $password): bool
    {
        // Rate limit admin login (stricter: 3 attempts)
        $rateLimitKey = 'admin_login_attempts:' . hash('sha256', strtolower($email));
        $attempts = Cache::get($rateLimitKey, 0);
        if ($attempts >= 3) {
            usleep(random_int(400000, 600000));
            return false;
        }

        $db = Database::getInstance();
        $admin = $db->table('admin_users')
            ->where('email', $email)
            ->where('is_active', 1)
            ->first();

        // Constant-time: always run password_verify
        $dummyHash = '$2y$12$StructBrewDummyHashForTimingAttackPreventionXXXXXXXXX';
        $hash = $admin['password_hash'] ?? $dummyHash;
        $valid = password_verify($password, $hash);

        if (!$admin || !$valid) {
            self::incrementAttempts($rateLimitKey);
            usleep(random_int(200000, 500000));
            return false;
        }

        self::clearAttempts($rateLimitKey);
        self::loginAdmin($admin);
        return true;
    }

    public static function loginAdmin(array $admin): void
    {
        Session::regenerate();
        Session::set('admin_id', $admin['id']);
        Session::set('admin_email', $admin['email']);
        Session::set('admin_role_id', $admin['role_id']);
        self::$admin = $admin;

        // Update last login
        Database::getInstance()->table('admin_users')
            ->where('id', $admin['id'])
            ->update(['last_login_at' => date('Y-m-d H:i:s')]);
    }

    public static function admin(): ?array
    {
        if (self::$admin !== null) return self::$admin;

        $id = Session::get('admin_id');
        if (!$id) return null;

        self::$admin = Database::getInstance()->table('admin_users')
            ->where('id', $id)
            ->first();

        return self::$admin;
    }

    public static function adminId(): ?int
    {
        return Session::get('admin_id') ? (int) Session::get('admin_id') : null;
    }

    public static function isAdmin(): bool
    {
        return Session::has('admin_id');
    }

    public static function adminLogout(): void
    {
        self::$admin = null;
        Session::remove('admin_id');
        Session::remove('admin_email');
        Session::remove('admin_role_id');
        Session::regenerate();
    }

    // ─── Admin Permissions ───────────────────────────────────

    public static function adminCan(string $permission): bool
    {
        $admin = self::admin();
        if (!$admin) return false;

        // Super admin can do everything
        if (($admin['is_superadmin'] ?? false)) return true;

        $roleId = $admin['role_id'] ?? null;
        if (!$roleId) return false;

        $role = Database::getInstance()->table('admin_roles')
            ->where('id', $roleId)
            ->first();

        if (!$role) return false;

        $permissions = json_decode($role['permissions'] ?? '[]', true);
        return in_array($permission, $permissions) || in_array('*', $permissions);
    }

    // ─── Password Helpers ────────────────────────────────────

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
    }
}
