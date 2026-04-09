<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

final class PaymentMethodService
{
    private const LABELS = [
        'account_credits' => 'Account Credits',
        'manual_checkout' => 'Manual / Invoice',
        'ideal' => 'iDEAL',
        'paypal' => 'PayPal',
        'bank_transfer' => 'Bank Transfer',
        'cash_on_delivery' => 'Cash on Delivery',
    ];

    private const DEFAULT_INSTRUCTIONS = [
        'account_credits' => 'This order was paid with your account credits balance.',
        'manual_checkout' => 'You will receive a manual payment confirmation or invoice update from our team.',
        'ideal' => 'Pay securely with iDEAL. If marked as pending, your payment confirmation is still processing.',
        'paypal' => 'Pay through PayPal. Pending status usually means the PayPal confirmation is still being processed.',
        'bank_transfer' => "Please transfer the total amount to the provided bank account and include your order number as payment reference.",
        'cash_on_delivery' => 'Please pay the courier upon delivery using the available payment options.',
    ];

    public function label(string $code): string
    {
        $normalized = trim($code);
        if ($normalized === '') {
            return 'Unknown';
        }

        return self::LABELS[$normalized] ?? ucwords(str_replace('_', ' ', $normalized));
    }

    public function instruction(string $code): string
    {
        $normalized = trim($code);
        if ($normalized === '') {
            return '';
        }

        $configured = $this->configuredInstruction($normalized);
        if ($configured !== null && trim($configured) !== '') {
            return trim($configured);
        }

        return self::DEFAULT_INSTRUCTIONS[$normalized] ?? '';
    }

    private function configuredInstruction(string $code): ?string
    {
        try {
            $db = Database::getInstance();
            if (!$db->tableExists('configurations')) {
                return null;
            }

            $path = 'checkout/payment_instruction_' . $code;
            $row = $db->table('configurations')
                ->where('path', $path)
                ->where('scope', 'global')
                ->where('scope_id', 0)
                ->first();

            if (!$row) {
                return null;
            }

            return (string) ($row['value'] ?? '');
        } catch (\Throwable) {
            return null;
        }
    }
}
