<?php
declare(strict_types=1);

namespace App\Data;

use App\Core\Database;
use Throwable;

final class CustomerPortal
{
    public static function groups(): array
    {
        return [
            'retail' => [
                'label' => 'Normal Client',
                'description' => 'Standard retail customer account for regular shopping and order tracking.',
            ],
            'partner' => [
                'label' => 'Partner',
                'description' => 'Strategic partner account for collaboration, support, and commercial resources.',
            ],
            'reseller' => [
                'label' => 'Reseller',
                'description' => 'Reseller account focused on product resale, B2B ordering, and support.',
            ],
            'wholesale' => [
                'label' => 'Wholesale',
                'description' => 'Wholesale buyer account for bulk orders, B2B contact, and onboarding.',
            ],
            'dealer' => [
                'label' => 'Dealer',
                'description' => 'Dealer account with onboarding, business support, and trade-focused resources.',
            ],
            'advertiser' => [
                'label' => 'Advertiser',
                'description' => 'Advertising partner account for campaigns, contact intake, and visibility options.',
            ],
        ];
    }

    public static function normalizeGroup(?string $group): string
    {
        $group = strtolower(trim((string) $group));
        return array_key_exists($group, self::groups()) ? $group : 'retail';
    }

    public static function label(?string $group): string
    {
        $group = self::normalizeGroup($group);
        return self::groups()[$group]['label'];
    }

    public static function description(?string $group): string
    {
        $group = self::normalizeGroup($group);
        return self::groups()[$group]['description'];
    }

    public static function supportsCustomerGroups(): bool
    {
        static $supported = null;
        if ($supported !== null) {
            return $supported;
        }

        try {
            $column = Database::getInstance()->raw("SHOW COLUMNS FROM customers LIKE 'customer_group'")->fetch();
            $supported = $column !== false && $column !== null;
        } catch (Throwable) {
            $supported = false;
        }

        return $supported;
    }

    public static function recommendedBusinessItems(?string $group): array
    {
        return match (self::normalizeGroup($group)) {
            'partner' => [
                ['title' => 'Priority Support', 'href' => '/priority-support'],
                ['title' => 'B2B Contact & Intake', 'href' => '/b2b-contact'],
                ['title' => 'Bulk Ordering Guide', 'href' => '/bulk-ordering'],
            ],
            'reseller' => [
                ['title' => 'Wholesale', 'href' => '/wholesale-partnerships'],
                ['title' => 'Bulk Ordering Guide', 'href' => '/bulk-ordering'],
                ['title' => 'VAT & Invoices', 'href' => '/vat-invoices'],
            ],
            'wholesale' => [
                ['title' => 'Wholesale', 'href' => '/wholesale-partnerships'],
                ['title' => 'Dealer Onboarding', 'href' => '/dealer-onboarding'],
                ['title' => 'B2B Contact & Intake', 'href' => '/b2b-contact'],
            ],
            'dealer' => [
                ['title' => 'Dealer Onboarding', 'href' => '/dealer-onboarding'],
                ['title' => 'Priority Support', 'href' => '/priority-support'],
                ['title' => 'Compatibility Help', 'href' => '/compatibility'],
            ],
            'advertiser' => [
                ['title' => 'Advertise with Us', 'href' => '/advertise'],
                ['title' => 'B2B Contact & Intake', 'href' => '/b2b-contact'],
                ['title' => 'Priority Support', 'href' => '/priority-support'],
            ],
            default => [
                ['title' => 'FAQ Hub', 'href' => '/faq'],
                ['title' => 'Payment Methods', 'href' => '/payment-methods'],
                ['title' => 'Returns & Warranty', 'href' => '/returns-warranty'],
            ],
        };
    }

    public static function sections(bool $loggedIn = true): array
    {
        return [
            [
                'title' => 'For Business',
                'items' => [
                    ['title' => 'Wholesale', 'href' => '/wholesale-partnerships'],
                    ['title' => 'Dealer Onboarding', 'href' => '/dealer-onboarding'],
                    ['title' => 'B2B Contact & Intake', 'href' => '/b2b-contact'],
                    ['title' => 'Advertise with Us', 'href' => '/advertise'],
                    ['title' => 'Priority Support', 'href' => '/priority-support'],
                    ['title' => 'Bulk Ordering Guide', 'href' => '/bulk-ordering'],
                ],
            ],
            [
                'title' => 'Account & Orders',
                'items' => array_values(array_filter([
                    $loggedIn ? ['title' => 'My Account', 'href' => '/account'] : ['title' => 'Create Account', 'href' => '/register'],
                    ['title' => 'Shopping Cart', 'href' => '/cart'],
                    ['title' => 'Payment Methods', 'href' => '/payment-methods'],
                    ['title' => 'Returns & Warranty', 'href' => '/returns-warranty'],
                    ['title' => 'Order Issues Help', 'href' => '/order-issues'],
                    ['title' => 'VAT & Invoices', 'href' => '/vat-invoices'],
                ])),
            ],
            [
                'title' => 'Support',
                'items' => [
                    ['title' => 'Contact Us', 'href' => '/contact'],
                    ['title' => 'FAQ Hub', 'href' => '/faq'],
                    ['title' => 'Installation Guides', 'href' => '/installation-guides'],
                    ['title' => 'Compatibility Help', 'href' => '/compatibility'],
                    ['title' => 'Availability & Restock', 'href' => '/availability-restock'],
                ],
            ],
            [
                'title' => 'International',
                'items' => [
                    ['title' => 'Shipping by Country', 'href' => '/shipping-by-country'],
                    ['title' => 'Incoterms: DAP & DDP', 'href' => '/incoterms'],
                    ['title' => 'Customs & Duties FAQ', 'href' => '/customs-duties'],
                    ['title' => 'International Returns', 'href' => '/international-returns'],
                    ['title' => 'Customs Checklist', 'href' => '/customs-checklist'],
                    ['title' => 'Shipping Restrictions', 'href' => '/shipping-restrictions'],
                    ['title' => 'International Exchange Policy', 'href' => '/exchange-policy-international'],
                ],
            ],
            [
                'title' => 'Policies',
                'items' => [
                    ['title' => 'Terms & Conditions', 'href' => '/terms-and-conditions'],
                    ['title' => 'Privacy Policy', 'href' => '/privacy-policy'],
                    ['title' => 'Pre-Order Policy', 'href' => '/pre-order-policy'],
                    ['title' => 'Cookie Policy', 'href' => '/cookie-policy'],
                    ['title' => 'Returns Decision Tree', 'href' => '/returns-decision-tree'],
                    ['title' => 'Warranty Claim Checklist', 'href' => '/warranty-claim'],
                    ['title' => 'Warranty Exclusions', 'href' => '/warranty-exclusions'],
                ],
            ],
        ];
    }
}
