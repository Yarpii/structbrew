<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

final class WalletService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function loyaltyEnabled(int $storeViewId = 0): bool
    {
        return $this->configBool('loyalty/enabled', true, $storeViewId);
    }

    public function creditsEnabled(int $storeViewId = 0): bool
    {
        return $this->configBool('credits/enabled', true, $storeViewId);
    }

    public function creditsMinPurchaseAmount(int $storeViewId = 0): float
    {
        $min = $this->configFloat('credits/min_purchase_amount', 5.0, $storeViewId);
        return max(0.01, round($min, 2));
    }

    public function awardSignupPoints(int $customerId, int $storeViewId = 0): int
    {
        if (!$this->loyaltyEnabled($storeViewId)) {
            return $this->currentLoyaltyPoints($customerId);
        }

        $points = $this->configInt('loyalty/signup_points', 25, $storeViewId);
        if ($points <= 0) {
            return $this->currentLoyaltyPoints($customerId);
        }

        return $this->adjustLoyaltyPoints(
            $customerId,
            $points,
            'signup_bonus',
            'signup',
            'Signup loyalty bonus awarded.'
        );
    }

    public function awardOrderPoints(int $customerId, int $orderId, float $grandTotal, int $storeViewId = 0): int
    {
        if (!$this->loyaltyEnabled($storeViewId)) {
            return $this->currentLoyaltyPoints($customerId);
        }

        $fixedPoints = $this->configInt('loyalty/order_points_fixed', 0, $storeViewId);
        $perCurrency = $this->configFloat('loyalty/order_points_per_currency', 1.0, $storeViewId);

        $pointsFromAmount = (int) floor(max(0.0, $grandTotal) * max(0.0, $perCurrency));
        $totalPoints = max(0, $fixedPoints) + $pointsFromAmount;

        if ($totalPoints <= 0) {
            return $this->currentLoyaltyPoints($customerId);
        }

        return $this->adjustLoyaltyPoints(
            $customerId,
            $totalPoints,
            'order_reward',
            'order:' . $orderId,
            'Loyalty points awarded for order #' . $orderId . '.',
            ['order_id' => $orderId, 'grand_total' => $grandTotal]
        );
    }

    public function awardBirthdayPointsIfDue(int $customerId, ?string $dateOfBirth, int $storeViewId = 0): int
    {
        if (!$this->loyaltyEnabled($storeViewId)) {
            return $this->currentLoyaltyPoints($customerId);
        }

        $birthdayPoints = $this->configInt('loyalty/birthday_points', 100, $storeViewId);
        if ($birthdayPoints <= 0 || !$this->isBirthdayToday($dateOfBirth)) {
            return $this->currentLoyaltyPoints($customerId);
        }

        return $this->adjustLoyaltyPoints(
            $customerId,
            $birthdayPoints,
            'birthday_bonus',
            date('Y') . ':birthday',
            'Birthday loyalty bonus awarded.'
        );
    }

    public function addCredits(int $customerId, float $amount, string $eventType = 'credit_purchase', ?string $reference = null, string $description = 'Credits purchased.'): float
    {
        $normalized = round($amount, 2);
        if ($normalized <= 0) {
            return $this->currentCreditsBalance($customerId);
        }

        return $this->adjustCredits(
            $customerId,
            $normalized,
            'credit',
            $eventType,
            $reference,
            $description
        );
    }

    public function spendCredits(int $customerId, float $amount, string $eventType = 'credit_spend', ?string $reference = null, string $description = 'Credits spent.'): bool
    {
        $normalized = round($amount, 2);
        if ($normalized <= 0) {
            return true;
        }

        $customer = $this->db->table('customers')->where('id', $customerId)->first();
        if (!$customer) {
            return false;
        }

        $currentBalance = (float) ($customer['credits_balance'] ?? 0.0);
        if ($currentBalance + 0.00001 < $normalized) {
            return false;
        }

        $this->adjustCredits(
            $customerId,
            $normalized,
            'debit',
            $eventType,
            $reference,
            $description
        );

        return true;
    }

    public function currentLoyaltyPoints(int $customerId): int
    {
        $customer = $this->db->table('customers')->where('id', $customerId)->first();
        return (int) ($customer['loyalty_points'] ?? 0);
    }

    public function currentCreditsBalance(int $customerId): float
    {
        $customer = $this->db->table('customers')->where('id', $customerId)->first();
        return (float) ($customer['credits_balance'] ?? 0.0);
    }

    private function adjustLoyaltyPoints(
        int $customerId,
        int $points,
        string $eventType,
        ?string $reference,
        string $description,
        array $metadata = []
    ): int {
        if ($points === 0) {
            return $this->currentLoyaltyPoints($customerId);
        }

        if ($reference !== null && $this->loyaltyReferenceExists($customerId, $eventType, $reference)) {
            return $this->currentLoyaltyPoints($customerId);
        }

        $customer = $this->db->table('customers')->where('id', $customerId)->first();
        if (!$customer) {
            return 0;
        }

        $current = (int) ($customer['loyalty_points'] ?? 0);
        $newBalance = max(0, $current + $points);
        $effectivePoints = $newBalance - $current;

        if ($effectivePoints === 0) {
            return $newBalance;
        }

        $now = date('Y-m-d H:i:s');

        $this->db->table('customers')->where('id', $customerId)->update([
            'loyalty_points' => $newBalance,
            'updated_at' => $now,
        ]);

        $this->db->table('loyalty_point_transactions')->insert([
            'customer_id' => $customerId,
            'points' => $effectivePoints,
            'balance_after' => $newBalance,
            'event_type' => $eventType,
            'reference' => $reference,
            'description' => $description,
            'metadata' => empty($metadata) ? null : json_encode($metadata, JSON_UNESCAPED_UNICODE),
            'created_at' => $now,
        ]);

        return $newBalance;
    }

    private function adjustCredits(
        int $customerId,
        float $amount,
        string $direction,
        string $eventType,
        ?string $reference,
        string $description,
        array $metadata = []
    ): float {
        if ($amount <= 0) {
            return $this->currentCreditsBalance($customerId);
        }

        if ($reference !== null && $this->creditReferenceExists($customerId, $eventType, $reference, $direction)) {
            return $this->currentCreditsBalance($customerId);
        }

        $customer = $this->db->table('customers')->where('id', $customerId)->first();
        if (!$customer) {
            return 0.0;
        }

        $current = (float) ($customer['credits_balance'] ?? 0.0);
        $newBalance = $direction === 'debit'
            ? max(0.0, round($current - $amount, 2))
            : round($current + $amount, 2);

        $effectiveAmount = $direction === 'debit'
            ? round($current - $newBalance, 2)
            : round($newBalance - $current, 2);

        if ($effectiveAmount <= 0) {
            return $newBalance;
        }

        $now = date('Y-m-d H:i:s');

        $this->db->table('customers')->where('id', $customerId)->update([
            'credits_balance' => $newBalance,
            'updated_at' => $now,
        ]);

        $this->db->table('credit_transactions')->insert([
            'customer_id' => $customerId,
            'amount' => $effectiveAmount,
            'balance_after' => $newBalance,
            'direction' => $direction,
            'event_type' => $eventType,
            'reference' => $reference,
            'description' => $description,
            'metadata' => empty($metadata) ? null : json_encode($metadata, JSON_UNESCAPED_UNICODE),
            'is_withdrawable' => 0,
            'created_at' => $now,
        ]);

        return $newBalance;
    }

    private function loyaltyReferenceExists(int $customerId, string $eventType, string $reference): bool
    {
        return $this->db->table('loyalty_point_transactions')
            ->where('customer_id', $customerId)
            ->where('event_type', $eventType)
            ->where('reference', $reference)
            ->exists();
    }

    private function creditReferenceExists(int $customerId, string $eventType, string $reference, string $direction): bool
    {
        return $this->db->table('credit_transactions')
            ->where('customer_id', $customerId)
            ->where('event_type', $eventType)
            ->where('reference', $reference)
            ->where('direction', $direction)
            ->exists();
    }

    private function isBirthdayToday(?string $dateOfBirth): bool
    {
        if ($dateOfBirth === null || $dateOfBirth === '') {
            return false;
        }

        $dobTs = strtotime($dateOfBirth);
        if ($dobTs === false) {
            return false;
        }

        return date('m-d', $dobTs) === date('m-d');
    }

    private function configBool(string $path, bool $default, int $storeViewId = 0): bool
    {
        $value = $this->configValue($path, $default ? '1' : '0', $storeViewId);
        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
    }

    private function configInt(string $path, int $default, int $storeViewId = 0): int
    {
        $value = $this->configValue($path, (string) $default, $storeViewId);
        return is_numeric($value) ? (int) $value : $default;
    }

    private function configFloat(string $path, float $default, int $storeViewId = 0): float
    {
        $value = $this->configValue($path, (string) $default, $storeViewId);
        return is_numeric($value) ? (float) $value : $default;
    }

    private function configValue(string $path, string $default, int $storeViewId = 0): string
    {
        if ($storeViewId > 0) {
            $storeView = $this->db->table('configurations')
                ->where('path', $path)
                ->where('scope', 'store_view')
                ->where('scope_id', $storeViewId)
                ->first();

            if ($storeView && array_key_exists('value', $storeView)) {
                return (string) ($storeView['value'] ?? '');
            }
        }

        $global = $this->db->table('configurations')
            ->where('path', $path)
            ->where('scope', 'global')
            ->where('scope_id', 0)
            ->first();

        if ($global && array_key_exists('value', $global)) {
            return (string) ($global['value'] ?? '');
        }

        return $default;
    }
}
