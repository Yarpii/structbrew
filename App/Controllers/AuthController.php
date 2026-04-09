<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Core\StoreResolver;
use App\Core\Translator;
use App\Core\Validator;
use App\Data\CustomerPortal;
use App\Services\TurnstileService;
use App\Services\TwoFactorService;
use App\Services\WalletService;
use Throwable;

final class AuthController extends BaseStorefrontController
{
    /**
     * @throws Throwable
     */
    public function login(): Response
    {
        Translator::page('auth');
        if (Auth::isLoggedIn()) {
            return $this->redirect('/account');
        }

        $turnstile = new TurnstileService();

        return $this->storefrontView('auth.login', [
            'title' => 'Login',
            'turnstileSiteKey' => $turnstile->siteKey(),
            'turnstileEnabled' => $turnstile->isEnabled(),
        ]);
    }

    public function twoFactor(): Response
    {
        if (Auth::isLoggedIn()) {
            return $this->redirect('/account');
        }

        $twoFactorService = new TwoFactorService();
        if (!$twoFactorService->isFeatureEnabled()) {
            $this->clearPendingTwoFactor();
            Session::flash('error', 'Two-factor authentication is currently disabled.');
            return $this->redirect('/login');
        }

        $customerId = (int) Session::get('login_2fa_customer_id', 0);
        if ($customerId <= 0) {
            Session::flash('error', 'Your login session expired. Please log in again.');
            return $this->redirect('/login');
        }

        $customer = Database::getInstance()->table('customers')
            ->where('id', $customerId)
            ->where('is_active', 1)
            ->first();

        if (!$customer) {
            $this->clearPendingTwoFactor();
            Session::flash('error', 'Unable to continue login.');
            return $this->redirect('/login');
        }

        $turnstile = new TurnstileService();

        return $this->storefrontView('auth.two-factor', [
            'title' => 'Two-factor verification',
            'maskedEmail' => $this->maskEmail((string) ($customer['email'] ?? '')),
            'turnstileSiteKey' => $turnstile->siteKey(),
            'turnstileEnabled' => $turnstile->isEnabled(),
        ]);
    }

    public function verifyTwoFactor(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/login/2fa');
        }

        $twoFactor = new TwoFactorService();
        if (!$twoFactor->isFeatureEnabled()) {
            $this->clearPendingTwoFactor();
            Session::flash('error', 'Two-factor authentication is currently disabled.');
            return $this->redirect('/login');
        }

        $customerId = (int) Session::get('login_2fa_customer_id', 0);
        if ($customerId <= 0) {
            Session::flash('error', 'Your login session expired. Please log in again.');
            return $this->redirect('/login');
        }

        $turnstile = new TurnstileService();
        if ($redirect = $this->verifyHuman('/login/2fa', $turnstile)) {
            return $redirect;
        }

        $code = preg_replace('/\D+/', '', (string) $this->input('code', '')) ?? '';
        if (strlen($code) !== 6) {
            Session::flash('error', 'Enter a valid 6-digit code.');
            return $this->redirect('/login/2fa');
        }

        $customer = Database::getInstance()->table('customers')
            ->where('id', $customerId)
            ->where('is_active', 1)
            ->first();

        if (!$customer) {
            $this->clearPendingTwoFactor();
            Session::flash('error', 'Unable to continue login.');
            return $this->redirect('/login');
        }

        $secret = (string) ($customer['two_factor_secret'] ?? '');
        $enabled = (int) ($customer['two_factor_enabled'] ?? 0) === 1;

        if (!$enabled || $secret === '') {
            $this->clearPendingTwoFactor();
            Auth::loginCustomer($customer);
            Session::flash('success', 'Welcome back.');
            return $this->redirect('/account');
        }

        $twoFactor = new TwoFactorService();
        if (!$twoFactor->verifyCode($secret, $code)) {
            Session::flash('error', 'The verification code is invalid.');
            return $this->redirect('/login/2fa');
        }

        $this->clearPendingTwoFactor();
        Auth::loginCustomer($customer);

        Session::flash('success', 'Welcome back.');
        return $this->redirect('/account');
    }

    /**
     * @throws Throwable
     */
    public function register(): Response
    {
        if (Auth::isLoggedIn()) {
            return $this->redirect('/account');
        }

        $turnstile = new TurnstileService();

        return $this->storefrontView('auth.register', [
            'title' => 'Create Account',
            'customerGroups' => CustomerPortal::groups(),
            'supportsCustomerGroups' => CustomerPortal::supportsCustomerGroups(),
            'defaultCustomerGroup' => 'retail',
            'turnstileSiteKey' => $turnstile->siteKey(),
            'turnstileEnabled' => $turnstile->isEnabled(),
        ]);
    }

    public function authenticate(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/login');
        }

        $data = [
            'email' => strtolower(trim((string) $this->input('email', ''))),
            'password' => (string) $this->input('password', ''),
        ];

        $validator = Validator::make($data, [
            'email' => 'required|email|max:255',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(static fn ($errors) => $errors[0], $validator->errors())));
            return $this->redirect('/login');
        }

        $turnstile = new TurnstileService();
        if ($redirect = $this->verifyHuman('/login', $turnstile)) {
            return $redirect;
        }

        $customer = Database::getInstance()->table('customers')
            ->where('email', $data['email'])
            ->where('is_active', 1)
            ->first();

        if (!$customer || !password_verify($data['password'], (string) ($customer['password_hash'] ?? ''))) {
            Session::flash('error', 'Invalid email or password.');
            return $this->redirect('/login');
        }

        $twoFactorEnabled = (int) ($customer['two_factor_enabled'] ?? 0) === 1;
        $twoFactorSecret = trim((string) ($customer['two_factor_secret'] ?? ''));
        $twoFactorService = new TwoFactorService();

        if ($twoFactorService->isFeatureEnabled() && $twoFactorEnabled && $twoFactorSecret !== '') {
            Session::set('login_2fa_customer_id', (int) $customer['id']);
            Session::set('login_2fa_started_at', time());

            return $this->redirect('/login/2fa');
        }

        Auth::loginCustomer($customer);
        Session::flash('success', 'Welcome back.');
        return $this->redirect('/account');
    }

    public function store(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/register');
        }

        $turnstile = new TurnstileService();
        if ($redirect = $this->verifyHuman('/register', $turnstile)) {
            return $redirect;
        }

        $dateOfBirth = trim((string) $this->input('date_of_birth', ''));

        $data = [
            'first_name' => trim((string) $this->input('first_name', '')),
            'last_name' => trim((string) $this->input('last_name', '')),
            'email' => strtolower(trim((string) $this->input('email', ''))),
            'password' => (string) $this->input('password', ''),
            'password_confirmation' => (string) $this->input('password_confirmation', ''),
            'customer_group' => CustomerPortal::normalizeGroup((string) $this->input('customer_group', 'retail')),
            'date_of_birth' => $dateOfBirth,
        ];

        $validator = Validator::make($data, [
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'email' => 'required|email|max:255|unique:customers,email',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(static fn ($errors) => $errors[0], $validator->errors())));
            return $this->redirect('/register');
        }

        if ($dateOfBirth !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateOfBirth)) {
            Session::flash('error', 'Please enter a valid date of birth.');
            return $this->redirect('/register');
        }

        $storeViewId = $this->resolveStoreViewId();
        if ($storeViewId === null) {
            Session::flash('error', 'No active store view is configured for customer accounts.');
            return $this->redirect('/register');
        }

        $insertData = [
            'store_view_id' => $storeViewId,
            'email' => $data['email'],
            'password_hash' => Auth::hashPassword($data['password']),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'date_of_birth' => $dateOfBirth !== '' ? $dateOfBirth : null,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if (CustomerPortal::supportsCustomerGroups()) {
            $insertData['customer_group'] = $data['customer_group'];
        }

        $customer = Database::getInstance()->table('customers')->insert($insertData);

        $createdCustomer = Database::getInstance()->table('customers')->where('id', $customer)->first();
        if ($createdCustomer) {
            Auth::loginCustomer($createdCustomer);
            (new WalletService())->awardSignupPoints((int) $createdCustomer['id'], $storeViewId);
        }

        Session::flash('success', 'Your account has been created.');
        return $this->redirect('/account');
    }

    public function logout(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/account');
        }

        $this->clearPendingTwoFactor();
        Auth::logout();
        Session::flash('success', 'You have been logged out.');
        return $this->redirect('/login');
    }

    private function resolveStoreViewId(): ?int
    {
        $storeViewId = StoreResolver::storeViewId();
        if ($storeViewId !== null) {
            return $storeViewId;
        }

        $db = Database::getInstance();
        $defaultStoreView = $db->table('store_views')
            ->where('is_default', 1)
            ->where('is_active', 1)
            ->first();

        if ($defaultStoreView) {
            return (int) $defaultStoreView['id'];
        }

        $fallbackStoreView = $db->table('store_views')
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->first();

        return $fallbackStoreView ? (int) $fallbackStoreView['id'] : null;
    }

    private function verifyHuman(string $redirectPath, TurnstileService $turnstile): ?Response
    {
        if (!$turnstile->isEnabled()) {
            return null;
        }

        $token = (string) $this->input('cf-turnstile-response', '');
        if ($turnstile->verify($token, $this->request->ip())) {
            return null;
        }

        Session::flash('error', 'Captcha verification failed. Please try again.');
        return $this->redirect($redirectPath);
    }

    private function clearPendingTwoFactor(): void
    {
        Session::remove('login_2fa_customer_id');
        Session::remove('login_2fa_started_at');
    }

    private function maskEmail(string $email): string
    {
        if (!str_contains($email, '@')) {
            return $email;
        }

        [$name, $domain] = explode('@', $email, 2);
        if ($name === '') {
            return '***@' . $domain;
        }

        $prefix = substr($name, 0, 1);
        return $prefix . str_repeat('*', max(strlen($name) - 1, 2)) . '@' . $domain;
    }
}
