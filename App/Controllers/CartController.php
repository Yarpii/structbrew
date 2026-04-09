<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Core\StoreResolver;
use App\Data\Products;
use App\Models\Order;
use App\Services\WalletService;
use Throwable;

final class CartController extends BaseStorefrontController
{
    /**
     * @throws Throwable
     */
    public function index(): Response
    {
        $creditsBalance = 0.0;
        $creditsEnabled = false;

        if (Auth::isLoggedIn()) {
            $customerId = (int) Auth::customerId();
            $customerRow = Database::getInstance()->table('customers')->where('id', $customerId)->first();
            $creditsBalance = (float) ($customerRow['credits_balance'] ?? 0.0);

            $storeViewId = (int) ($this->resolveStoreViewId() ?? 0);
            $creditsEnabled = (new WalletService())->creditsEnabled($storeViewId);
        }

        $paymentMethods = $this->availablePaymentMethods();

        return $this->storefrontView('cart.index', [
            'title' => 'Shopping Cart',
            'ads' => $this->activeAdsByPlacements(['cart_page']),
            'creditsBalance' => $creditsBalance,
            'creditsEnabled' => $creditsEnabled,
            'paymentMethods' => array_values($paymentMethods),
            'defaultPaymentMethod' => $this->defaultPaymentMethod($paymentMethods),
        ]);
    }

    public function checkout(): Response
    {
        if (!$this->verifyCsrf()) {
            return $this->json(['success' => false, 'message' => 'Invalid security token. Please refresh the page.'], 422);
        }

        if (!Auth::isLoggedIn()) {
            return $this->json([
                'success' => false,
                'message' => 'Please log in to complete checkout.',
                'redirect' => '/login',
            ], 401);
        }

        $items = $this->input('items', []);
        if (!is_array($items) || empty($items)) {
            return $this->json(['success' => false, 'message' => 'Your cart is empty.'], 422);
        }

        $lineItems = [];
        $subtotal = 0.0;

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $productId = (int) ($item['id'] ?? $item['product_id'] ?? 0);
            $qty = max(1, (int) ($item['qty'] ?? 1));

            if ($productId <= 0) {
                continue;
            }

            $product = Products::find($productId);
            if (!$product || !(bool) ($product['in_stock'] ?? false)) {
                continue;
            }

            $unitPrice = (float) (($product['sale_price'] ?? null) ?: ($product['price'] ?? 0));
            $rowTotal = $unitPrice * $qty;
            $subtotal += $rowTotal;

            $lineItems[] = [
                'product_id' => $productId,
                'sku' => (string) ($product['sku'] ?? ('SKU-' . $productId)),
                'name' => (string) ($product['name'] ?? 'Product'),
                'qty' => $qty,
                'price' => $unitPrice,
                'row_total' => $rowTotal,
                'options' => [
                    'slug' => (string) ($product['slug'] ?? ''),
                ],
            ];
        }

        if (empty($lineItems)) {
            return $this->json(['success' => false, 'message' => 'No valid products found in cart.'], 422);
        }

        $customerId = (int) Auth::customerId();
        $customer = Auth::customer() ?? [];
        $storeViewId = $this->resolveStoreViewId();
        $currencyCode = (string) StoreResolver::currency();

        $useCredits = (bool) $this->input('use_credits', false);
        $walletService = new WalletService();

        $shippingAmount = $subtotal >= 50 ? 0.0 : 4.99;
        $grandTotal = $subtotal + $shippingAmount;

        $db = Database::getInstance();
        $customerRow = $db->table('customers')->where('id', $customerId)->first();

        if ($useCredits && !$walletService->creditsEnabled($storeViewId)) {
            return $this->json(['success' => false, 'message' => 'Credits are currently disabled.'], 422);
        }

        if ($useCredits) {
            $creditsBalance = (float) ($customerRow['credits_balance'] ?? 0.0);
            if ($creditsBalance + 0.00001 < $grandTotal) {
                return $this->json(['success' => false, 'message' => 'Insufficient credits balance for this order.'], 422);
            }
        }

        $paymentMethods = $this->availablePaymentMethods();
        $selectedPaymentMethod = (string) $this->input('payment_method', '');

        if ($useCredits) {
            $selectedPaymentMethod = 'account_credits';
        } else {
            if ($selectedPaymentMethod === '') {
                $selectedPaymentMethod = $this->defaultPaymentMethod($paymentMethods);
            }

            if (!isset($paymentMethods[$selectedPaymentMethod])) {
                return $this->json(['success' => false, 'message' => 'Selected payment method is not available.'], 422);
            }
        }

        $billingAddress = $this->resolveAddress($db, $customerId, 'billing', $customer);
        $shippingAddress = $this->resolveAddress($db, $customerId, 'shipping', $customer);

        $db->beginTransaction();

        try {
            $orderId = $db->table('orders')->insert([
                'order_number' => Order::generateOrderNumber(),
                'customer_id' => $customerId,
                'store_view_id' => $storeViewId,
                'status' => 'pending',
                'currency_code' => $currencyCode,
                'subtotal' => $subtotal,
                'tax_amount' => 0,
                'shipping_amount' => $shippingAmount,
                'discount_amount' => 0,
                'grand_total' => $grandTotal,
                'coupon_code' => null,
                'shipping_method' => 'standard',
                'payment_method' => $selectedPaymentMethod,
                'billing_address' => json_encode($billingAddress, JSON_UNESCAPED_UNICODE),
                'shipping_address' => json_encode($shippingAddress, JSON_UNESCAPED_UNICODE),
                'customer_email' => (string) ($customer['email'] ?? ''),
                'customer_note' => null,
                'ip_address' => (string) ($this->request->ip() ?? ''),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            foreach ($lineItems as $lineItem) {
                $db->table('order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $lineItem['product_id'],
                    'sku' => $lineItem['sku'],
                    'name' => $lineItem['name'],
                    'qty' => $lineItem['qty'],
                    'price' => $lineItem['price'],
                    'row_total' => $lineItem['row_total'],
                    'tax_amount' => 0,
                    'discount_amount' => 0,
                    'options' => json_encode($lineItem['options'], JSON_UNESCAPED_UNICODE),
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }

            $paymentComment = $selectedPaymentMethod === 'account_credits'
                ? 'Order created from cart checkout and paid using account credits.'
                : 'Order created from cart checkout with payment method: ' . ($paymentMethods[$selectedPaymentMethod]['label'] ?? $selectedPaymentMethod) . '.';

            $db->table('order_status_history')->insert([
                'order_id' => $orderId,
                'status' => 'pending',
                'comment' => $paymentComment,
                'is_customer_notified' => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            if ($useCredits) {
                $spent = $walletService->spendCredits(
                    $customerId,
                    $grandTotal,
                    'order_payment',
                    'order:' . $orderId,
                    'Credits used for order #' . $orderId . '.'
                );

                if (!$spent) {
                    throw new \RuntimeException('Failed to apply account credits.');
                }
            }

            $walletService->awardOrderPoints($customerId, (int) $orderId, $grandTotal, $storeViewId);
            $walletService->awardBirthdayPointsIfDue($customerId, (string) ($customerRow['date_of_birth'] ?? ''), $storeViewId);

            $db->commit();

            return $this->json([
                'success' => true,
                'message' => $useCredits ? 'Order placed using account credits.' : 'Order placed successfully.',
                'redirect' => '/account/orders/' . $orderId,
            ]);
        } catch (Throwable $e) {
            $db->rollback();
            return $this->json([
                'success' => false,
                'message' => 'Checkout failed. Please try again.',
            ], 500);
        }
    }

    private function resolveStoreViewId(): int
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

        return (int) ($fallbackStoreView['id'] ?? 1);
    }

    private function resolveAddress(Database $db, int $customerId, string $type, array $customer): array
    {
        $address = $db->table('addresses')
            ->where('customer_id', $customerId)
            ->where('type', $type)
            ->where('is_default', 1)
            ->first();

        if (!$address) {
            $address = $db->table('addresses')
                ->where('customer_id', $customerId)
                ->where('type', $type)
                ->orderBy('id', 'DESC')
                ->first();
        }

        if ($address) {
            return [
                'first_name' => (string) ($address['first_name'] ?? ''),
                'last_name' => (string) ($address['last_name'] ?? ''),
                'company' => (string) ($address['company'] ?? ''),
                'street_1' => (string) ($address['street_1'] ?? ''),
                'street_2' => (string) ($address['street_2'] ?? ''),
                'city' => (string) ($address['city'] ?? ''),
                'state' => (string) ($address['state'] ?? ''),
                'postcode' => (string) ($address['postcode'] ?? ''),
                'country_code' => (string) ($address['country_code'] ?? ''),
                'phone' => (string) ($address['phone'] ?? ''),
            ];
        }

        return [
            'first_name' => (string) ($customer['first_name'] ?? 'Customer'),
            'last_name' => (string) ($customer['last_name'] ?? ''),
            'company' => '',
            'street_1' => 'Address not set',
            'street_2' => '',
            'city' => 'N/A',
            'state' => '',
            'postcode' => '0000',
            'country_code' => 'US',
            'phone' => (string) ($customer['phone'] ?? ''),
        ];
    }

    private function activeAdsByPlacements(array $placements): array
    {
        if (empty($placements)) {
            return [];
        }

        try {
            $now = date('Y-m-d H:i:s');
            $rows = Database::getInstance()->table('marketing_ads')
                ->where('is_active', 1)
                ->whereRaw('(starts_at IS NULL OR starts_at <= :now0)', [':now0' => $now])
                ->whereRaw('(ends_at IS NULL OR ends_at >= :now1)', [':now1' => $now])
                ->orderBy('sort_order', 'ASC')
                ->orderBy('id', 'DESC')
                ->get();

            $allowed = array_fill_keys($placements, true);
            $mapped = [];

            foreach ($rows as $row) {
                $placement = (string) ($row['placement'] ?? '');
                if ($placement === '' || !isset($allowed[$placement]) || isset($mapped[$placement])) {
                    continue;
                }
                $mapped[$placement] = $row;
            }

            return $mapped;
        } catch (Throwable) {
            return [];
        }
    }

    private function availablePaymentMethods(): array
    {
        $methods = [
            'manual_checkout' => ['code' => 'manual_checkout', 'label' => 'Manual / Invoice'],
            'ideal' => ['code' => 'ideal', 'label' => 'iDEAL'],
            'paypal' => ['code' => 'paypal', 'label' => 'PayPal'],
            'bank_transfer' => ['code' => 'bank_transfer', 'label' => 'Bank Transfer'],
            'cash_on_delivery' => ['code' => 'cash_on_delivery', 'label' => 'Cash on Delivery'],
        ];

        $defaultFlags = [
            'checkout/payment_method_manual_checkout' => '1',
            'checkout/payment_method_ideal' => '0',
            'checkout/payment_method_paypal' => '0',
            'checkout/payment_method_bank_transfer' => '0',
            'checkout/payment_method_cash_on_delivery' => '0',
        ];

        $flags = $defaultFlags;

        try {
            $db = Database::getInstance();
            if ($db->tableExists('configurations')) {
                $rows = $db->table('configurations')
                    ->where('scope', 'global')
                    ->where('scope_id', 0)
                    ->get();

                foreach ($rows as $row) {
                    $path = (string) ($row['path'] ?? '');
                    if (array_key_exists($path, $flags)) {
                        $flags[$path] = (string) ($row['value'] ?? '');
                    }
                }
            }
        } catch (Throwable) {
            // Keep defaults when configurations are unavailable
        }

        $enabled = [];
        foreach ($methods as $code => $meta) {
            $path = 'checkout/payment_method_' . $code;
            $value = strtolower(trim((string) ($flags[$path] ?? '0')));
            if (in_array($value, ['1', 'true', 'yes', 'on'], true)) {
                $enabled[$code] = $meta;
            }
        }

        if (empty($enabled)) {
            $enabled['manual_checkout'] = $methods['manual_checkout'];
        }

        return $enabled;
    }

    private function defaultPaymentMethod(array $paymentMethods): string
    {
        try {
            $db = Database::getInstance();
            if ($db->tableExists('configurations')) {
                $row = $db->table('configurations')
                    ->where('path', 'checkout/default_payment_method')
                    ->where('scope', 'global')
                    ->where('scope_id', 0)
                    ->first();

                $configured = trim((string) ($row['value'] ?? ''));
                if ($configured !== '' && isset($paymentMethods[$configured])) {
                    return $configured;
                }
            }
        } catch (Throwable) {
            // Ignore and use fallback
        }

        return (string) (array_key_first($paymentMethods) ?? 'manual_checkout');
    }
}
