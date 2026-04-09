<?php
declare(strict_types=1);

namespace Brew\Controllers;

use Brew\Core\Controller;
use Brew\Core\Response;
use Throwable;

final class PagesController extends Controller
{
    /**
     * @throws Throwable
     */
    public function show(string $slug): Response
    {
        $viewFile = __DIR__ . '/../Views/pages/' . $slug . '.php';

        if (!is_file($viewFile)) {
            return $this->text('404 Not Found', 404);
        }

        return $this->view('pages.' . $slug, [
            'title' => self::TITLES[$slug] ?? ucwords(str_replace('-', ' ', $slug)),
        ]);
    }

    private const TITLES = [
        // Business
        'wholesale-partnerships'         => 'Wholesale Partnerships',
        'dealer-onboarding'              => 'Dealer Onboarding',
        'b2b-contact'                    => 'B2B Contact & Intake',
        'advertise'                      => 'Advertise with Us',
        'priority-support'               => 'Priority Support',
        'bulk-ordering'                  => 'Bulk Ordering Guide',
        // Account & Orders
        'payment-methods'                => 'Payment Methods',
        'returns-warranty'               => 'Returns & Warranty',
        'order-issues'                   => 'Order Issues Help',
        'vat-invoices'                   => 'VAT & Invoices',
        // Support
        'faq'                            => 'FAQ Hub',
        'installation-guides'            => 'Installation Guides',
        'compatibility'                  => 'Compatibility Help',
        'availability-restock'           => 'Availability & Restock',
        // International
        'shipping-by-country'            => 'Shipping by Country',
        'incoterms'                      => 'Incoterms: DAP & DDP',
        'customs-duties'                 => 'Customs & Duties FAQ',
        'international-returns'          => 'International Returns',
        'customs-checklist'              => 'Customs Checklist',
        'shipping-restrictions'          => 'Shipping Restrictions',
        'exchange-policy-international'  => 'International Exchange Policy',
        // Policies
        'terms-and-conditions'           => 'Terms & Conditions',
        'privacy-policy'                 => 'Privacy Policy',
        'pre-order-policy'               => 'Pre-Order Policy',
        'cookie-policy'                  => 'Cookie Policy',
        'returns-decision-tree'          => 'Returns Decision Tree',
        'warranty-claim'                 => 'Warranty Claim Checklist',
        'warranty-exclusions'            => 'Warranty Exclusions',
    ];
}
