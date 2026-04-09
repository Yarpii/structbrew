<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Config;
use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Core\StoreResolver;
use App\Core\Validator;
use App\Data\CustomerPortal;
use App\Data\Products;
use App\Models\Customer;
use App\Models\Ticket;
use App\Services\PaymentMethodService;
use App\Services\TwoFactorService;
use App\Services\WalletService;

final class AccountController extends BaseStorefrontController
{
    public function index(): Response
    {
        return $this->renderAccountPage('account.index', 'My Account');
    }

    public function profile(): Response
    {
        return $this->renderAccountPage('account.profile', 'Account Profile');
    }

    public function orders(): Response
    {
        if ($response = $this->redirectIfGuest()) {
            return $response;
        }

        $customer = Customer::find((int) Auth::customerId());
        if (!$customer) {
            Auth::logout();
            Session::flash('error', 'Please log in to continue.');
            return $this->redirect('/login');
        }

        $db = Database::getInstance();
        $orders = $db->table('orders')
            ->where('customer_id', $customer->getId())
            ->orderBy('created_at', 'DESC')
            ->get();

        $storeViews = [];
        foreach ($db->table('store_views')->get() as $storeView) {
            $storeViews[(int) ($storeView['id'] ?? 0)] = [
                'name' => (string) ($storeView['name'] ?? 'Store'),
                'code' => strtoupper((string) ($storeView['code'] ?? '')),
            ];
        }

        $paymentMethods = new PaymentMethodService();

        foreach ($orders as &$order) {
            $orderItems = $db->table('order_items')
                ->where('order_id', (int) $order['id'])
                ->get();

            $statusHistory = $db->table('order_status_history')
                ->where('order_id', (int) $order['id'])
                ->orderBy('created_at', 'DESC')
                ->get();

            $shippingAddress = is_string($order['shipping_address'] ?? '')
                ? (json_decode((string) $order['shipping_address'], true) ?: [])
                : ((array) ($order['shipping_address'] ?? []));

            $countryCode = strtoupper((string) ($shippingAddress['country_code'] ?? ''));
            $storeViewMeta = $storeViews[(int) ($order['store_view_id'] ?? 0)] ?? ['name' => 'Store', 'code' => ''];

            $order['items'] = $orderItems;
            $order['item_count'] = count($orderItems);
            $order['shipping_country'] = $countryCode;
            $order['store_view_name'] = $storeViewMeta['name'];
            $order['store_view_code'] = $storeViewMeta['code'];
            $order['latest_status_comment'] = (string) (($statusHistory[0]['comment'] ?? '') ?: '');
            $order['tracking_available'] = in_array((string) ($order['status'] ?? ''), ['shipped', 'delivered'], true);
            $order['compatibility_hint'] = $this->extractCompatibilityHint($orderItems);
            $order['maintenance_hint'] = $this->extractMaintenanceHint($orderItems);
            $order['reorder_items'] = $this->buildReorderItems($orderItems, (int) $order['id']);
            $order['payment_method_label'] = $paymentMethods->label((string) ($order['payment_method'] ?? ''));
        }
        unset($order);

        $filters = $this->normalizeOrderFilters();
        $orders = $this->filterOrders($orders, $filters);

        $availableCountries = [];
        $allStatuses = [];
        foreach ($orders as $order) {
            $country = (string) ($order['shipping_country'] ?? '');
            if ($country !== '') {
                $availableCountries[$country] = $country;
            }

            $status = (string) ($order['status'] ?? '');
            if ($status !== '') {
                $allStatuses[$status] = ucfirst($status);
            }
        }
        ksort($availableCountries);
        ksort($allStatuses);

        return $this->storefrontView('account.orders', [
            'title' => 'My Orders',
            'orders' => $orders,
            'orderFilters' => $filters,
            'availableCountries' => array_values($availableCountries),
            'availableStatuses' => $allStatuses,
            'filteredOrdersCount' => count($orders),
            'openOrdersCount' => count(array_filter($orders, static fn (array $order): bool => in_array((string) ($order['status'] ?? ''), ['pending', 'processing', 'shipped'], true))),
        ]);
    }

    public function addresses(): Response
    {
        return $this->renderAccountPage('account.addresses', 'My Addresses');
    }

    public function garage(): Response
    {
        return $this->renderAccountPage('account.garage', 'My Garage');
    }

    public function purchaseCredits(): Response
    {
        if ($response = $this->redirectIfGuest()) {
            return $response;
        }

        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/account');
        }

        $customerId = (int) Auth::customerId();
        $amount = round((float) $this->input('amount', 0), 2);

        $walletService = new WalletService();
        $storeViewId = (int) (StoreResolver::storeViewId() ?? 0);

        if (!$walletService->creditsEnabled($storeViewId)) {
            Session::flash('error', 'Credits are currently disabled.');
            return $this->redirect('/account');
        }

        $minAmount = $walletService->creditsMinPurchaseAmount($storeViewId);
        if ($amount < $minAmount) {
            Session::flash('error', 'Minimum credit purchase amount is ' . number_format($minAmount, 2) . '.');
            return $this->redirect('/account');
        }

        $walletService->addCredits(
            $customerId,
            $amount,
            'credit_purchase',
            null,
            'Customer purchased account credits.'
        );

        Session::flash('success', 'Credits added successfully: ' . number_format($amount, 2));
        return $this->redirect('/account');
    }

    public function updateProfile(): Response
    {
        if ($response = $this->redirectIfGuest()) {
            return $response;
        }

        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/account/profile');
        }

        $customer = Customer::find((int) Auth::customerId());
        if (!$customer) {
            Auth::logout();
            Session::flash('error', 'Please log in to continue.');
            return $this->redirect('/login');
        }

        $data = [
            'first_name' => trim((string) $this->input('first_name', '')),
            'last_name' => trim((string) $this->input('last_name', '')),
            'email' => strtolower(trim((string) $this->input('email', ''))),
            'phone' => trim((string) $this->input('phone', '')),
            'date_of_birth' => trim((string) $this->input('date_of_birth', '')),
            'customer_group' => CustomerPortal::normalizeGroup((string) $this->input('customer_group', 'retail')),
        ];

        $validator = Validator::make($data, [
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'email' => 'required|email|max:255|unique:customers,email,' . $customer->getId(),
            'phone' => 'max:50',
        ]);

        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(static fn ($errors) => $errors[0], $validator->errors())));
            return $this->redirect('/account/profile');
        }

        if ($data['date_of_birth'] !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['date_of_birth'])) {
            Session::flash('error', 'Please enter a valid date of birth.');
            return $this->redirect('/account/profile');
        }

        $updateData = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] !== '' ? $data['phone'] : null,
            'date_of_birth' => $data['date_of_birth'] !== '' ? $data['date_of_birth'] : null,
        ];

        if (CustomerPortal::supportsCustomerGroups()) {
            $updateData['customer_group'] = $data['customer_group'];
        }

        $customer->update($updateData);

        Session::set('customer_email', $data['email']);
        Session::flash('success', 'Profile updated successfully.');
        return $this->redirect('/account/profile');
    }

    public function setupTwoFactor(): Response
    {
        if ($response = $this->redirectIfGuest()) {
            return $response;
        }

        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/account/profile');
        }

        $twoFactor = new TwoFactorService();
        if (!$twoFactor->isFeatureEnabled()) {
            Session::flash('error', 'Two-factor authentication is disabled by the administrator.');
            return $this->redirect('/account/profile');
        }

        $db = Database::getInstance();
        $customerId = (int) Auth::customerId();
        $customer = $db->table('customers')->where('id', $customerId)->first();

        if (!$customer) {
            Auth::logout();
            Session::flash('error', 'Please log in to continue.');
            return $this->redirect('/login');
        }

        $secret = $twoFactor->generateSecret();

        $db->table('customers')
            ->where('id', $customerId)
            ->update([
                'two_factor_secret' => $secret,
                'two_factor_enabled' => 0,
                'two_factor_confirmed_at' => null,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        Session::flash('success', 'Two-factor secret generated. Enter a code from your authenticator app to enable it.');
        return $this->redirect('/account/profile');
    }

    public function enableTwoFactor(): Response
    {
        if ($response = $this->redirectIfGuest()) {
            return $response;
        }

        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/account/profile');
        }

        $twoFactor = new TwoFactorService();
        if (!$twoFactor->isFeatureEnabled()) {
            Session::flash('error', 'Two-factor authentication is disabled by the administrator.');
            return $this->redirect('/account/profile');
        }

        $code = preg_replace('/\D+/', '', (string) $this->input('two_factor_code', '')) ?? '';
        if (strlen($code) !== 6) {
            Session::flash('error', 'Enter a valid 6-digit code.');
            return $this->redirect('/account/profile');
        }

        $db = Database::getInstance();
        $customerId = (int) Auth::customerId();
        $customer = $db->table('customers')->where('id', $customerId)->first();

        if (!$customer) {
            Auth::logout();
            Session::flash('error', 'Please log in to continue.');
            return $this->redirect('/login');
        }

        $secret = trim((string) ($customer['two_factor_secret'] ?? ''));
        if ($secret === '') {
            Session::flash('error', 'Generate a 2FA secret first.');
            return $this->redirect('/account/profile');
        }

        $twoFactor = new TwoFactorService();
        if (!$twoFactor->verifyCode($secret, $code)) {
            Session::flash('error', 'The verification code is invalid.');
            return $this->redirect('/account/profile');
        }

        $db->table('customers')
            ->where('id', $customerId)
            ->update([
                'two_factor_enabled' => 1,
                'two_factor_confirmed_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        Session::flash('success', 'Two-factor authentication is now enabled.');
        return $this->redirect('/account/profile');
    }

    public function disableTwoFactor(): Response
    {
        if ($response = $this->redirectIfGuest()) {
            return $response;
        }

        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/account/profile');
        }

        $twoFactor = new TwoFactorService();
        if (!$twoFactor->isFeatureEnabled()) {
            Session::flash('error', 'Two-factor authentication is disabled by the administrator.');
            return $this->redirect('/account/profile');
        }

        $db = Database::getInstance();
        $customerId = (int) Auth::customerId();
        $customer = $db->table('customers')->where('id', $customerId)->first();

        if (!$customer) {
            Auth::logout();
            Session::flash('error', 'Please log in to continue.');
            return $this->redirect('/login');
        }

        $enabled = (int) ($customer['two_factor_enabled'] ?? 0) === 1;
        $secret = trim((string) ($customer['two_factor_secret'] ?? ''));

        if ($enabled) {
            $code = preg_replace('/\D+/', '', (string) $this->input('two_factor_code', '')) ?? '';
            $twoFactor = new TwoFactorService();
            if (strlen($code) !== 6 || !$twoFactor->verifyCode($secret, $code)) {
                Session::flash('error', 'Enter a valid authenticator code to disable 2FA.');
                return $this->redirect('/account/profile');
            }
        }

        $db->table('customers')
            ->where('id', $customerId)
            ->update([
                'two_factor_enabled' => 0,
                'two_factor_secret' => null,
                'two_factor_confirmed_at' => null,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        Session::flash('success', 'Two-factor authentication has been disabled.');
        return $this->redirect('/account/profile');
    }

    public function storeAddress(): Response
    {
        if ($response = $this->redirectIfGuest()) {
            return $response;
        }

        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/account/addresses');
        }

        $customerId = (int) Auth::customerId();
        $data = $this->addressPayload();

        $validator = Validator::make($data, $this->addressRules());
        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(static fn ($errors) => $errors[0], $validator->errors())));
            return $this->redirect('/account/addresses');
        }

        $db = Database::getInstance();
        if ((int) $data['is_default'] === 1) {
            $db->table('addresses')
                ->where('customer_id', $customerId)
                ->where('type', $data['type'])
                ->update([
                    'is_default' => 0,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }

        $db->table('addresses')->insert([
            'customer_id' => $customerId,
            'type' => $data['type'],
            'is_default' => (int) $data['is_default'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'company' => $data['company'] !== '' ? $data['company'] : null,
            'street_1' => $data['street_1'],
            'street_2' => $data['street_2'] !== '' ? $data['street_2'] : null,
            'city' => $data['city'],
            'state' => $data['state'] !== '' ? $data['state'] : null,
            'postcode' => $data['postcode'],
            'country_code' => strtoupper($data['country_code']),
            'phone' => $data['phone'] !== '' ? $data['phone'] : null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Address added successfully.');
        return $this->redirect('/account/addresses');
    }

    public function updateAddress(string $id): Response
    {
        if ($response = $this->redirectIfGuest()) {
            return $response;
        }

        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/account/addresses');
        }

        $customerId = (int) Auth::customerId();
        $db = Database::getInstance();
        $address = $db->table('addresses')
            ->where('id', (int) $id)
            ->where('customer_id', $customerId)
            ->first();

        if (!$address) {
            Session::flash('error', 'Address not found.');
            return $this->redirect('/account/addresses');
        }

        $data = $this->addressPayload();
        $validator = Validator::make($data, $this->addressRules());
        if ($validator->fails()) {
            Session::flash('error', implode(' ', array_map(static fn ($errors) => $errors[0], $validator->errors())));
            return $this->redirect('/account/addresses');
        }

        if ((int) $data['is_default'] === 1) {
            $db->table('addresses')
                ->where('customer_id', $customerId)
                ->where('type', $data['type'])
                ->update([
                    'is_default' => 0,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }

        $db->table('addresses')
            ->where('id', (int) $id)
            ->where('customer_id', $customerId)
            ->update([
                'type' => $data['type'],
                'is_default' => (int) $data['is_default'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'company' => $data['company'] !== '' ? $data['company'] : null,
                'street_1' => $data['street_1'],
                'street_2' => $data['street_2'] !== '' ? $data['street_2'] : null,
                'city' => $data['city'],
                'state' => $data['state'] !== '' ? $data['state'] : null,
                'postcode' => $data['postcode'],
                'country_code' => strtoupper($data['country_code']),
                'phone' => $data['phone'] !== '' ? $data['phone'] : null,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        Session::flash('success', 'Address updated successfully.');
        return $this->redirect('/account/addresses');
    }

    public function deleteAddress(string $id): Response
    {
        if ($response = $this->redirectIfGuest()) {
            return $response;
        }

        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/account/addresses');
        }

        $deleted = Database::getInstance()->table('addresses')
            ->where('id', (int) $id)
            ->where('customer_id', (int) Auth::customerId())
            ->delete();

        Session::flash($deleted > 0 ? 'success' : 'error', $deleted > 0 ? 'Address removed successfully.' : 'Address not found.');
        return $this->redirect('/account/addresses');
    }

    public function defaultAddress(string $id): Response
    {
        if ($response = $this->redirectIfGuest()) {
            return $response;
        }

        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/account/addresses');
        }

        $customerId = (int) Auth::customerId();
        $db = Database::getInstance();
        $address = $db->table('addresses')
            ->where('id', (int) $id)
            ->where('customer_id', $customerId)
            ->first();

        if (!$address) {
            Session::flash('error', 'Address not found.');
            return $this->redirect('/account/addresses');
        }

        $db->table('addresses')
            ->where('customer_id', $customerId)
            ->where('type', $address['type'])
            ->update([
                'is_default' => 0,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $db->table('addresses')
            ->where('id', (int) $id)
            ->where('customer_id', $customerId)
            ->update([
                'is_default' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        Session::flash('success', ucfirst((string) $address['type']) . ' address set as default.');
        return $this->redirect('/account/addresses');
    }

    public function order(string $id): Response
    {
        if ($response = $this->redirectIfGuest()) {
            return $response;
        }

        $db = Database::getInstance();
        $order = $db->table('orders')
            ->where('id', (int) $id)
            ->where('customer_id', (int) Auth::customerId())
            ->first();

        if (!$order) {
            Session::flash('error', 'Order not found.');
            return $this->redirect('/account');
        }

        $items = $db->table('order_items')
            ->where('order_id', $order['id'])
            ->get();

        $statusHistory = $db->table('order_status_history')
            ->where('order_id', $order['id'])
            ->orderBy('created_at', 'DESC')
            ->get();

        $paymentMethods = new PaymentMethodService();
        $order['payment_method_label'] = $paymentMethods->label((string) ($order['payment_method'] ?? ''));
        $order['payment_instruction'] = $paymentMethods->instruction((string) ($order['payment_method'] ?? ''));

        return $this->storefrontView('account.order', [
            'title' => 'Order ' . $order['order_number'],
            'order' => $order,
            'items' => $items,
            'statusHistory' => $statusHistory,
        ]);
    }

    public function invoice(string $id): Response
    {
        if ($response = $this->redirectIfGuest()) {
            return $response;
        }

        $db = Database::getInstance();
        $order = $db->table('orders')
            ->where('id', (int) $id)
            ->where('customer_id', (int) Auth::customerId())
            ->first();

        if (!$order) {
            Session::flash('error', 'Order not found.');
            return $this->redirect('/account');
        }

        $items = $db->table('order_items')
            ->where('order_id', $order['id'])
            ->get();

        $billing = is_string($order['billing_address'] ?? '') ? json_decode((string) $order['billing_address'], true) : ($order['billing_address'] ?? []);
        $shipping = is_string($order['shipping_address'] ?? '') ? json_decode((string) $order['shipping_address'], true) : ($order['shipping_address'] ?? []);

        $viewPath = dirname(__DIR__) . '/Views/account/invoice.php';
        ob_start();
        include $viewPath;
        $html = ob_get_clean() ?: '';

        return Response::html($html)
            ->header('Content-Disposition', 'attachment; filename="invoice-' . preg_replace('/[^A-Za-z0-9\-_]/', '-', (string) $order['order_number']) . '.html"');
    }

    public function storeGarageVehicle(): Response
    {
        if ($response = $this->redirectIfGuest()) {
            return $response;
        }

        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/account/garage');
        }

        $customerId = (int) Auth::customerId();
        $vehicleId = (int) $this->input('vehicle_id', 0);
        $vehicleType = strtolower(trim((string) $this->input('vehicle_type', 'scooter')));

        if (!in_array($vehicleType, ['scooter', 'moped', 'motorcycle'], true)) {
            $vehicleType = 'scooter';
        }

        $db = Database::getInstance();
        $vehicle = $db->table('vehicles')
            ->where('id', $vehicleId)
            ->where('is_active', 1)
            ->first();

        if (!$vehicle) {
            Session::flash('error', 'Selected vehicle is invalid.');
            return $this->redirect('/account/garage');
        }

        $exists = $db->table('customer_vehicles')
            ->where('customer_id', $customerId)
            ->where('vehicle_id', $vehicleId)
            ->where('vehicle_type', $vehicleType)
            ->first();

        if ($exists) {
            Session::flash('error', 'This vehicle is already in your garage.');
            return $this->redirect('/account/garage');
        }

        $hasDefault = $db->table('customer_vehicles')
            ->where('customer_id', $customerId)
            ->where('is_default', 1)
            ->count() > 0;

        $db->table('customer_vehicles')->insert([
            'customer_id' => $customerId,
            'vehicle_id' => $vehicleId,
            'vehicle_type' => $vehicleType,
            'nickname' => null,
            'is_default' => $hasDefault ? 0 : 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Vehicle added to your garage.');
        return $this->redirect('/account/garage');
    }

    public function deleteGarageVehicle(string $id): Response
    {
        if ($response = $this->redirectIfGuest()) {
            return $response;
        }

        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/account/garage');
        }

        $customerId = (int) Auth::customerId();
        $db = Database::getInstance();

        $vehicle = $db->table('customer_vehicles')
            ->where('id', (int) $id)
            ->where('customer_id', $customerId)
            ->first();

        if (!$vehicle) {
            Session::flash('error', 'Garage vehicle not found.');
            return $this->redirect('/account/garage');
        }

        $db->table('customer_vehicles')
            ->where('id', (int) $id)
            ->where('customer_id', $customerId)
            ->delete();

        if (!empty($vehicle['is_default'])) {
            $next = $db->table('customer_vehicles')
                ->where('customer_id', $customerId)
                ->orderBy('id', 'ASC')
                ->first();

            if ($next) {
                $db->table('customer_vehicles')
                    ->where('id', (int) $next['id'])
                    ->where('customer_id', $customerId)
                    ->update([
                        'is_default' => 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }
        }

        Session::flash('success', 'Vehicle removed from your garage.');
        return $this->redirect('/account/garage');
    }

    public function selectGarageVehicle(string $id): Response
    {
        if ($response = $this->redirectIfGuest()) {
            return $response;
        }

        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/account/garage');
        }

        $customerId = (int) Auth::customerId();
        $db = Database::getInstance();

        $vehicle = $db->table('customer_vehicles')
            ->where('id', (int) $id)
            ->where('customer_id', $customerId)
            ->first();

        if (!$vehicle) {
            Session::flash('error', 'Garage vehicle not found.');
            return $this->redirect('/account/garage');
        }

        $db->table('customer_vehicles')
            ->where('customer_id', $customerId)
            ->update([
                'is_default' => 0,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $db->table('customer_vehicles')
            ->where('id', (int) $id)
            ->where('customer_id', $customerId)
            ->update([
                'is_default' => 1,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        Session::flash('success', 'Default garage vehicle updated.');
        return $this->redirect('/account/garage');
    }

    public function tickets(): Response
    {
        if ($response = $this->redirectIfGuest()) {
            return $response;
        }

        $db = Database::getInstance();
        $customerId = (int) Auth::customerId();

        $tickets = $db->table('tickets')
            ->where('customer_id', $customerId)
            ->orderBy('last_activity_at', 'DESC')
            ->get();

        return $this->storefrontView('account.tickets', [
            'title' => 'My Tickets',
            'tickets' => $tickets,
        ]);
    }

    public function ticketCreate(): Response
    {
        if ($response = $this->redirectIfGuest()) {
            return $response;
        }

        $db = Database::getInstance();
        $customerId = (int) Auth::customerId();

        $departments = $db->table('ticket_departments')
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->get();

        $orders = $db->table('orders')
            ->where('customer_id', $customerId)
            ->orderBy('created_at', 'DESC')
            ->get();

        return $this->storefrontView('account.ticket-create', [
            'title' => 'Open a Ticket',
            'departments' => $departments,
            'orders' => $orders,
        ]);
    }

    public function ticketStore(): Response
    {
        if ($response = $this->redirectIfGuest()) {
            return $response;
        }

        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/account/tickets/create');
        }

        $subject = trim((string) $this->input('subject', ''));
        $body = trim((string) $this->input('body', ''));

        if ($subject === '' || $body === '') {
            Session::flash('error', 'Subject and message are required.');
            return $this->redirect('/account/tickets/create');
        }

        $db = Database::getInstance();
        $customerId = (int) Auth::customerId();
        $customer = $db->table('customers')->where('id', $customerId)->first();

        $now = date('Y-m-d H:i:s');
        $ticketId = $db->table('tickets')->insert([
            'ticket_number' => Ticket::generateNumber(),
            'subject' => $subject,
            'status' => 'open',
            'priority' => (string) $this->input('priority', 'normal'),
            'type' => (string) $this->input('type', 'general'),
            'source' => 'web',
            'requester_type' => 'customer',
            'customer_id' => $customerId,
            'guest_email' => null,
            'guest_name' => null,
            'assigned_agent_id' => null,
            'department_id' => (int) $this->input('department_id', 0) ?: null,
            'category_id' => (int) $this->input('category_id', 0) ?: null,
            'brand_id' => null,
            'website_id' => null,
            'store_view_id' => (int) (StoreResolver::storeViewId() ?? 0) ?: null,
            'order_id' => (int) $this->input('order_id', 0) ?: null,
            'sla_policy_id' => null,
            'sla_first_response_due_at' => null,
            'sla_resolution_due_at' => null,
            'sla_first_response_met' => null,
            'sla_resolution_met' => null,
            'first_response_at' => null,
            'resolved_at' => null,
            'closed_at' => null,
            'last_activity_at' => $now,
            'is_escalated' => 0,
            'escalated_at' => null,
            'merged_into_ticket_id' => null,
            'custom_fields' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $db->table('ticket_replies')->insert([
            'ticket_id' => $ticketId,
            'author_type' => 'customer',
            'author_id' => $customerId,
            'author_name' => trim((string) (($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? ''))),
            'author_email' => (string) ($customer['email'] ?? ''),
            'body' => $body,
            'is_internal' => 0,
            'is_resolution' => 0,
            'time_spent_minutes' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Session::flash('success', 'Ticket created successfully.');
        return $this->redirect('/account/tickets/' . $ticketId);
    }

    public function ticketShow(int $id): Response
    {
        if ($response = $this->redirectIfGuest()) {
            return $response;
        }

        $db = Database::getInstance();
        $customerId = (int) Auth::customerId();

        $ticket = $db->table('tickets')
            ->where('id', $id)
            ->where('customer_id', $customerId)
            ->first();

        if (!$ticket) {
            Session::flash('error', 'Ticket not found.');
            return $this->redirect('/account/tickets');
        }

        $replies = $db->table('ticket_replies')
            ->where('ticket_id', $id)
            ->where('is_internal', 0)
            ->orderBy('created_at', 'ASC')
            ->get();

        return $this->storefrontView('account.ticket', [
            'title' => (string) ($ticket['subject'] ?? 'Ticket'),
            'ticket' => $ticket,
            'replies' => $replies,
        ]);
    }

    public function ticketReply(int $id): Response
    {
        if ($response = $this->redirectIfGuest()) {
            return $response;
        }

        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/account/tickets/' . $id);
        }

        $body = trim((string) $this->input('body', ''));
        if ($body === '') {
            Session::flash('error', 'Reply message is required.');
            return $this->redirect('/account/tickets/' . $id);
        }

        $db = Database::getInstance();
        $customerId = (int) Auth::customerId();
        $customer = $db->table('customers')->where('id', $customerId)->first();

        $ticket = $db->table('tickets')
            ->where('id', $id)
            ->where('customer_id', $customerId)
            ->first();

        if (!$ticket) {
            Session::flash('error', 'Ticket not found.');
            return $this->redirect('/account/tickets');
        }

        $now = date('Y-m-d H:i:s');
        $db->table('ticket_replies')->insert([
            'ticket_id' => $id,
            'author_type' => 'customer',
            'author_id' => $customerId,
            'author_name' => trim((string) (($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? ''))),
            'author_email' => (string) ($customer['email'] ?? ''),
            'body' => $body,
            'is_internal' => 0,
            'is_resolution' => 0,
            'time_spent_minutes' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $db->table('tickets')
            ->where('id', $id)
            ->where('customer_id', $customerId)
            ->update([
                'status' => in_array((string) ($ticket['status'] ?? ''), ['closed', 'resolved'], true) ? 'reopened' : (string) ($ticket['status'] ?? 'open'),
                'last_activity_at' => $now,
                'updated_at' => $now,
            ]);

        Session::flash('success', 'Reply added.');
        return $this->redirect('/account/tickets/' . $id);
    }

    public function ticketClose(int $id): Response
    {
        if ($response = $this->redirectIfGuest()) {
            return $response;
        }

        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/account/tickets/' . $id);
        }

        $now = date('Y-m-d H:i:s');
        $updated = Database::getInstance()->table('tickets')
            ->where('id', $id)
            ->where('customer_id', (int) Auth::customerId())
            ->update([
                'status' => 'closed',
                'closed_at' => $now,
                'last_activity_at' => $now,
                'updated_at' => $now,
            ]);

        Session::flash($updated > 0 ? 'success' : 'error', $updated > 0 ? 'Ticket closed.' : 'Ticket not found.');
        return $this->redirect('/account/tickets/' . $id);
    }

    public function ticketReopen(int $id): Response
    {
        if ($response = $this->redirectIfGuest()) {
            return $response;
        }

        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/account/tickets/' . $id);
        }

        $now = date('Y-m-d H:i:s');
        $updated = Database::getInstance()->table('tickets')
            ->where('id', $id)
            ->where('customer_id', (int) Auth::customerId())
            ->update([
                'status' => 'reopened',
                'closed_at' => null,
                'last_activity_at' => $now,
                'updated_at' => $now,
            ]);

        Session::flash($updated > 0 ? 'success' : 'error', $updated > 0 ? 'Ticket reopened.' : 'Ticket not found.');
        return $this->redirect('/account/tickets/' . $id);
    }

    private function renderAccountPage(string $view, string $title): Response
    {
        if ($response = $this->redirectIfGuest()) {
            return $response;
        }

        $customer = Customer::find((int) Auth::customerId());
        if (!$customer) {
            Auth::logout();
            Session::flash('error', 'Please log in to continue.');
            return $this->redirect('/login');
        }

        $db = Database::getInstance();
        $orders = $db->table('orders')
            ->where('customer_id', $customer->getId())
            ->orderBy('created_at', 'DESC')
            ->get();

        foreach ($orders as &$order) {
            $order['item_count'] = $db->table('order_items')
                ->where('order_id', $order['id'])
                ->count();
        }
        unset($order);

        $addresses = $db->table('addresses')
            ->where('customer_id', $customer->getId())
            ->orderBy('type', 'ASC')
            ->orderBy('is_default', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->get();

        $customerData = $customer->toArray();
        $customerGroup = CustomerPortal::normalizeGroup($customerData['customer_group'] ?? 'retail');
        $twoFactorService = new TwoFactorService();
        $twoFactorFeatureEnabled = $twoFactorService->isFeatureEnabled();
        $twoFactorEnabled = $twoFactorFeatureEnabled && (int) ($customerData['two_factor_enabled'] ?? 0) === 1;
        $twoFactorSecret = $twoFactorFeatureEnabled ? trim((string) ($customerData['two_factor_secret'] ?? '')) : '';
        $twoFactorIssuer = (string) Config::get('app.name', 'StructBrew');
        $twoFactorProvisioningUri = '';
        if ($twoFactorFeatureEnabled && $twoFactorSecret !== '') {
            $twoFactorProvisioningUri = $twoFactorService->provisioningUri(
                $twoFactorIssuer,
                (string) ($customerData['email'] ?? ''),
                $twoFactorSecret
            );
        }
        $totalSpent = array_reduce($orders, static fn (float $sum, array $order): float => $sum + (float) ($order['grand_total'] ?? 0), 0.0);
        $activeOrders = count(array_filter($orders, static fn (array $order): bool => in_array($order['status'] ?? '', ['pending', 'processing', 'shipped'], true)));

        $garageData = $this->garageDataForCustomer((int) $customer->getId());

        $walletService = new WalletService();
        $storeViewId = (int) (StoreResolver::storeViewId() ?? 0);

        return $this->storefrontView($view, [
            'title' => $title,
            'customer' => $customerData,
            'customerGroup' => $customerGroup,
            'customerGroupLabel' => CustomerPortal::label($customerGroup),
            'customerGroupDescription' => CustomerPortal::description($customerGroup),
            'customerGroups' => CustomerPortal::groups(),
            'supportsCustomerGroups' => CustomerPortal::supportsCustomerGroups(),
            'portalSections' => CustomerPortal::sections(true),
            'recommendedResources' => CustomerPortal::recommendedBusinessItems($customerGroup),
            'topCategories' => array_slice(Products::categories(), 0, 8, true),
            'orders' => $orders,
            'recentOrders' => array_slice($orders, 0, 5),
            'addresses' => $addresses,
            'totalSpent' => $totalSpent,
            'activeOrders' => $activeOrders,
            'twoFactorFeatureEnabled' => $twoFactorFeatureEnabled,
            'twoFactorEnabled' => $twoFactorEnabled,
            'twoFactorSecret' => $twoFactorSecret,
            'twoFactorProvisioningUri' => $twoFactorProvisioningUri,
            'garageVehicles' => $garageData['garageVehicles'],
            'garageVehicleOptions' => $garageData['garageVehicleOptions'],
            'garageBrands' => $garageData['garageBrands'],
            'creditsEnabled' => $walletService->creditsEnabled($storeViewId),
            'creditsMinPurchaseAmount' => $walletService->creditsMinPurchaseAmount($storeViewId),
        ]);
    }

    private function normalizeOrderFilters(): array
    {
        $status = trim((string) $this->input('status', ''));
        $query = trim((string) $this->input('q', ''));
        $country = strtoupper(trim((string) $this->input('country', '')));
        $dateFrom = trim((string) $this->input('date_from', ''));
        $dateTo = trim((string) $this->input('date_to', ''));

        return [
            'status' => $status,
            'q' => $query,
            'country' => $country,
            'date_from' => preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom) ? $dateFrom : '',
            'date_to' => preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo) ? $dateTo : '',
        ];
    }

    private function filterOrders(array $orders, array $filters): array
    {
        return array_values(array_filter($orders, static function (array $order) use ($filters): bool {
            if (($filters['status'] ?? '') !== '' && (string) ($order['status'] ?? '') !== (string) $filters['status']) {
                return false;
            }

            if (($filters['country'] ?? '') !== '' && strtoupper((string) ($order['shipping_country'] ?? '')) !== strtoupper((string) $filters['country'])) {
                return false;
            }

            $createdAt = (string) ($order['created_at'] ?? '');
            $createdDate = $createdAt !== '' ? date('Y-m-d', strtotime($createdAt)) : '';

            if (($filters['date_from'] ?? '') !== '' && $createdDate !== '' && $createdDate < (string) $filters['date_from']) {
                return false;
            }

            if (($filters['date_to'] ?? '') !== '' && $createdDate !== '' && $createdDate > (string) $filters['date_to']) {
                return false;
            }

            $query = mb_strtolower((string) ($filters['q'] ?? ''));
            if ($query === '') {
                return true;
            }

            $haystack = [
                (string) ($order['order_number'] ?? ''),
                (string) ($order['store_view_name'] ?? ''),
                (string) ($order['shipping_country'] ?? ''),
                (string) ($order['payment_method'] ?? ''),
                (string) ($order['shipping_method'] ?? ''),
                (string) ($order['compatibility_hint'] ?? ''),
            ];

            foreach ((array) ($order['items'] ?? []) as $item) {
                $haystack[] = (string) ($item['name'] ?? '');
                $haystack[] = (string) ($item['sku'] ?? '');
            }

            $fullText = mb_strtolower(implode(' ', $haystack));
            return str_contains($fullText, $query);
        }));
    }

    private function extractCompatibilityHint(array $orderItems): string
    {
        foreach ($orderItems as $item) {
            $options = is_string($item['options'] ?? '')
                ? (json_decode((string) $item['options'], true) ?: [])
                : ((array) ($item['options'] ?? []));

            $brand = trim((string) ($options['brand'] ?? $options['vehicle_brand'] ?? $options['scooter_brand'] ?? ''));
            $model = trim((string) ($options['model'] ?? $options['vehicle_model'] ?? $options['scooter_model'] ?? ''));
            $year = trim((string) ($options['year'] ?? $options['vehicle_year'] ?? $options['scooter_year'] ?? ''));

            $parts = array_values(array_filter([$brand, $model, $year], static fn (string $value): bool => $value !== ''));
            if (!empty($parts)) {
                return implode(' ', $parts);
            }
        }

        return 'Compatibility check recommended';
    }

    private function extractMaintenanceHint(array $orderItems): string
    {
        $maintenanceTerms = [
            'oil' => 'Routine maintenance item',
            'filter' => 'Routine maintenance item',
            'brake' => 'Safety maintenance item',
            'belt' => 'Drive system maintenance item',
            'spark' => 'Engine tune-up item',
            'tire' => 'Wear part',
            'tyre' => 'Wear part',
        ];

        foreach ($orderItems as $item) {
            $name = mb_strtolower((string) ($item['name'] ?? ''));
            foreach ($maintenanceTerms as $needle => $label) {
                if (str_contains($name, $needle)) {
                    return $label;
                }
            }
        }

        return 'Performance or upgrade item';
    }

    private function buildReorderItems(array $orderItems, int $orderId): array
    {
        $result = [];
        foreach ($orderItems as $item) {
            $itemId = (string) ($item['id'] ?? uniqid((string) $orderId . '-', true));
            $qty = max(1, (int) ($item['qty'] ?? 1));
            $price = (float) ($item['price'] ?? 0);
            $name = (string) ($item['name'] ?? 'Product');
            $productId = (string) ($item['product_id'] ?? ('historic-' . $orderId . '-' . $itemId));
            $slug = trim((string) ($item['sku'] ?? '')) !== '' ? ('?sku=' . urlencode((string) $item['sku'])) : '';

            $result[] = [
                'id' => $productId,
                'name' => $name,
                'slug' => $slug,
                'price' => $price,
                'qty' => $qty,
            ];
        }

        return $result;
    }

    private function garageDataForCustomer(int $customerId): array
    {
        $db = Database::getInstance();

        $brands = $db->table('brands')
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->get();

        $brandById = [];
        foreach ($brands as $brand) {
            $brandById[(int) $brand['id']] = (string) $brand['name'];
        }

        $vehicles = $db->table('vehicles')
            ->where('is_active', 1)
            ->orderBy('model', 'ASC')
            ->get();

        $garageVehicleOptions = [];
        foreach ($vehicles as $vehicle) {
            $vehicleId = (int) $vehicle['id'];
            $brandName = $brandById[(int) ($vehicle['brand_id'] ?? 0)] ?? 'Unknown';
            $garageVehicleOptions[] = [
                'id' => $vehicleId,
                'brand_id' => (int) ($vehicle['brand_id'] ?? 0),
                'brand' => $brandName,
                'model' => (string) ($vehicle['model'] ?? ''),
                'label' => trim($brandName . ' ' . (string) ($vehicle['model'] ?? '')),
            ];
        }

        $garageRows = [];
        try {
            $garageRows = $db->table('customer_vehicles')
                ->where('customer_id', $customerId)
                ->orderBy('is_default', 'DESC')
                ->orderBy('id', 'DESC')
                ->get();
        } catch (\Throwable) {
            $garageRows = [];
        }

        $garageVehicles = [];
        foreach ($garageRows as $row) {
            $vehicleMeta = null;
            foreach ($garageVehicleOptions as $option) {
                if ((int) $option['id'] === (int) ($row['vehicle_id'] ?? 0)) {
                    $vehicleMeta = $option;
                    break;
                }
            }
            if ($vehicleMeta === null) {
                continue;
            }

            $garageVehicles[] = [
                'id' => (int) ($row['id'] ?? 0),
                'vehicle_id' => (int) ($row['vehicle_id'] ?? 0),
                'vehicle_type' => (string) ($row['vehicle_type'] ?? 'scooter'),
                'is_default' => (int) ($row['is_default'] ?? 0) === 1,
                'label' => $vehicleMeta['label'],
                'brand' => $vehicleMeta['brand'],
                'model' => $vehicleMeta['model'],
            ];
        }

        return [
            'garageVehicles' => $garageVehicles,
            'garageVehicleOptions' => $garageVehicleOptions,
            'garageBrands' => array_map(static fn (array $brand): array => [
                'id' => (int) $brand['id'],
                'name' => (string) $brand['name'],
            ], $brands),
        ];
    }

    private function addressPayload(): array
    {
        return [
            'type' => trim((string) $this->input('type', 'shipping')),
            'is_default' => $this->input('is_default') ? 1 : 0,
            'first_name' => trim((string) $this->input('first_name', '')),
            'last_name' => trim((string) $this->input('last_name', '')),
            'company' => trim((string) $this->input('company', '')),
            'street_1' => trim((string) $this->input('street_1', '')),
            'street_2' => trim((string) $this->input('street_2', '')),
            'city' => trim((string) $this->input('city', '')),
            'state' => trim((string) $this->input('state', '')),
            'postcode' => trim((string) $this->input('postcode', '')),
            'country_code' => strtoupper(trim((string) $this->input('country_code', ''))),
            'phone' => trim((string) $this->input('phone', '')),
        ];
    }

    private function addressRules(): array
    {
        return [
            'type' => 'required|in:billing,shipping',
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'company' => 'max:255',
            'street_1' => 'required|max:255',
            'street_2' => 'max:255',
            'city' => 'required|max:100',
            'state' => 'max:100',
            'postcode' => 'required|max:20',
            'country_code' => 'required|string|min:2|max:2',
            'phone' => 'max:50',
        ];
    }
}
