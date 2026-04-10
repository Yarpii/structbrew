<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Response;
use App\Core\Session;

final class ConfigController extends BaseAdminController
{
    /**
     * Configuration group definitions for the admin panel.
     */
    private const CONFIG_GROUPS = [
        'general' => [
            'label' => 'General',
            'fields' => [
                'general/store_name'        => ['label' => 'Store Name', 'type' => 'text'],
                'general/store_email'       => ['label' => 'Store Email', 'type' => 'email'],
                'general/store_phone'       => ['label' => 'Store Phone', 'type' => 'text'],
                'general/default_locale'    => ['label' => 'Default Locale', 'type' => 'text'],
                'general/default_currency'  => ['label' => 'Default Currency', 'type' => 'text'],
                'general/timezone'          => ['label' => 'Timezone', 'type' => 'text'],
            ],
        ],
        'catalog' => [
            'label' => 'Catalog',
            'fields' => [
                'catalog/products_per_page'      => ['label' => 'Products Per Page', 'type' => 'number'],
                'catalog/default_sort_order'     => ['label' => 'Default Sort Order', 'type' => 'text'],
                'catalog/show_out_of_stock'      => ['label' => 'Show Out of Stock', 'type' => 'boolean'],
                'catalog/low_stock_notification' => ['label' => 'Low Stock Email Notification', 'type' => 'boolean'],
            ],
        ],
        'checkout' => [
            'label' => 'Checkout',
            'fields' => [
                'checkout/guest_checkout'           => ['label' => 'Allow Guest Checkout', 'type' => 'boolean'],
                'checkout/min_order_amount'         => ['label' => 'Minimum Order Amount', 'type' => 'number'],
                'checkout/terms_and_conditions'     => ['label' => 'Require Terms & Conditions', 'type' => 'boolean'],
                'checkout/default_payment_method'   => ['label' => 'Default Payment Method (code)', 'type' => 'text'],
                'checkout/payment_method_manual_checkout' => ['label' => 'Enable Manual Checkout', 'type' => 'boolean'],
                'checkout/payment_method_ideal' => ['label' => 'Enable iDEAL', 'type' => 'boolean'],
                'checkout/payment_method_paypal' => ['label' => 'Enable PayPal', 'type' => 'boolean'],
                'checkout/payment_method_bank_transfer' => ['label' => 'Enable Bank Transfer', 'type' => 'boolean'],
                'checkout/payment_method_cash_on_delivery' => ['label' => 'Enable Cash on Delivery', 'type' => 'boolean'],
                'checkout/payment_instruction_manual_checkout' => ['label' => 'Manual Checkout Instructions', 'type' => 'textarea'],
                'checkout/payment_instruction_ideal' => ['label' => 'iDEAL Instructions', 'type' => 'textarea'],
                'checkout/payment_instruction_paypal' => ['label' => 'PayPal Instructions', 'type' => 'textarea'],
                'checkout/payment_instruction_bank_transfer' => ['label' => 'Bank Transfer Instructions', 'type' => 'textarea'],
                'checkout/payment_instruction_cash_on_delivery' => ['label' => 'Cash on Delivery Instructions', 'type' => 'textarea'],
            ],
        ],
        'shipping' => [
            'label' => 'Shipping',
            'fields' => [
                'shipping/origin_country'   => ['label' => 'Origin Country', 'type' => 'text'],
                'shipping/origin_postcode'  => ['label' => 'Origin Postcode', 'type' => 'text'],
                'shipping/free_shipping_threshold' => ['label' => 'Free Shipping Threshold', 'type' => 'number'],
            ],
        ],
        'tax' => [
            'label' => 'Tax',
            'fields' => [
                'tax/prices_include_tax'    => ['label' => 'Prices Include Tax', 'type' => 'boolean'],
                'tax/display_tax_in_cart'   => ['label' => 'Display Tax in Cart', 'type' => 'boolean'],
                'tax/default_tax_class'     => ['label' => 'Default Tax Class', 'type' => 'text'],
            ],
        ],
        'email' => [
            'label' => 'Email',
            'fields' => [
                'email/smtp_host'       => ['label' => 'SMTP Host', 'type' => 'text'],
                'email/smtp_port'       => ['label' => 'SMTP Port', 'type' => 'number'],
                'email/smtp_user'       => ['label' => 'SMTP Username', 'type' => 'text'],
                'email/smtp_encryption' => ['label' => 'SMTP Encryption', 'type' => 'text'],
                'email/from_name'       => ['label' => 'From Name', 'type' => 'text'],
                'email/from_email'      => ['label' => 'From Email', 'type' => 'email'],
            ],
        ],
        'seo' => [
            'label' => 'SEO',
            'fields' => [
                'seo/meta_title_suffix'       => ['label' => 'Meta Title Suffix', 'type' => 'text'],
                'seo/default_meta_description' => ['label' => 'Default Meta Description', 'type' => 'textarea'],
                'seo/enable_sitemap'          => ['label' => 'Enable Sitemap', 'type' => 'boolean'],
                'seo/robots_txt'              => ['label' => 'Robots.txt Content', 'type' => 'textarea'],
            ],
        ],
        'loyalty' => [
            'label' => 'Loyalty Program',
            'fields' => [
                'loyalty/enabled' => ['label' => 'Enable Loyalty Program', 'type' => 'boolean'],
                'loyalty/signup_points' => ['label' => 'Signup Bonus Points', 'type' => 'number'],
                'loyalty/order_points_fixed' => ['label' => 'Fixed Points Per Order', 'type' => 'number'],
                'loyalty/order_points_per_currency' => ['label' => 'Points Per Currency Unit', 'type' => 'number'],
                'loyalty/birthday_points' => ['label' => 'Birthday Bonus Points', 'type' => 'number'],
            ],
        ],
        'credits' => [
            'label' => 'Store Credits',
            'fields' => [
                'credits/enabled' => ['label' => 'Enable Store Credits', 'type' => 'boolean'],
                'credits/min_purchase_amount' => ['label' => 'Minimum Credit Purchase Amount', 'type' => 'number'],
            ],
        ],
        'security' => [
            'label' => 'Security',
            'fields' => [
                'security/customer_two_factor_enabled' => ['label' => 'Allow Customer 2FA', 'type' => 'boolean'],
                'security/turnstile_enabled' => ['label' => 'Enable Cloudflare Turnstile', 'type' => 'boolean'],
                'security/turnstile_site_key' => ['label' => 'Turnstile Site Key', 'type' => 'text'],
                'security/turnstile_secret_key' => ['label' => 'Turnstile Secret Key', 'type' => 'password'],
            ],
        ],
    ];

    /**
     * Show grouped configurations with current values.
     */
    public function index(): Response
    {
        $db = Database::getInstance();

        $scope   = (string) ($this->request->query('scope') ?: 'global');
        $scopeId = (int) ($this->request->query('scope_id') ?: 0);

        // Build configuration data with current values
        $groups = self::CONFIG_GROUPS;

        foreach ($groups as $groupKey => &$group) {
            foreach ($group['fields'] as $path => &$field) {
                // Get scoped value (try specific scope, fallback to global)
                $config = $db->table('configurations')
                    ->where('path', $path)
                    ->where('scope', $scope)
                    ->where('scope_id', $scopeId)
                    ->first();

                if ($config) {
                    $field['value'] = $config['value'];
                    $field['config_id'] = $config['id'];
                } else {
                    // Fallback to global
                    $global = $db->table('configurations')
                        ->where('path', $path)
                        ->where('scope', 'global')
                        ->where('scope_id', 0)
                        ->first();
                    $field['value'] = $global['value'] ?? '';
                    $field['config_id'] = $global['id'] ?? null;
                    $field['inherited'] = ($scope !== 'global');
                }
            }
            unset($field);
        }
        unset($group);

        // Scope selector data
        $websites   = $db->table('websites')->orderBy('name', 'ASC')->get();
        $stores     = $db->table('stores')->orderBy('name', 'ASC')->get();
        $storeViewsList = $db->table('store_views')->orderBy('name', 'ASC')->get();

        return $this->adminView('admin/config/index', [
            'title'          => 'Configuration',
            'groups'         => $groups,
            'scope'          => $scope,
            'scopeId'        => $scopeId,
            'websites'       => $websites,
            'stores'         => $stores,
            'storeViewsList' => $storeViewsList,
        ]);
    }

    /**
     * Save configuration values with scope support.
     */
    public function save(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/config');
        }

        $db = Database::getInstance();

        $scope   = (string) $this->input('scope', 'global');
        $scopeId = (int) $this->input('scope_id', '0');
        $configs = $this->input('config', []);

        if (!is_array($configs)) {
            Session::flash('error', 'No configuration data provided.');
            return $this->redirect('/admin/config');
        }

        $validScopes = ['global', 'website', 'store', 'store_view'];
        if (!in_array($scope, $validScopes)) {
            Session::flash('error', 'Invalid scope.');
            return $this->redirect('/admin/config');
        }

        $db->beginTransaction();

        try {
            $now = date('Y-m-d H:i:s');

            foreach ($configs as $path => $value) {
                // Validate that path is in our defined config groups
                $validPath = false;
                foreach (self::CONFIG_GROUPS as $group) {
                    if (array_key_exists($path, $group['fields'])) {
                        $validPath = true;
                        break;
                    }
                }
                if (!$validPath) {
                    continue;
                }

                $existing = $db->table('configurations')
                    ->where('path', $path)
                    ->where('scope', $scope)
                    ->where('scope_id', $scopeId)
                    ->first();

                if ($existing) {
                    $db->table('configurations')
                        ->where('id', $existing['id'])
                        ->update([
                            'value'      => (string) $value,
                            'updated_at' => $now,
                        ]);
                } else {
                    $db->table('configurations')->insert([
                        'path'       => $path,
                        'value'      => (string) $value,
                        'scope'      => $scope,
                        'scope_id'   => $scopeId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }

            $db->commit();

            $this->logActivity('update', 'configuration', 0, null, [
                'scope'    => $scope,
                'scope_id' => $scopeId,
                'paths'    => array_keys($configs),
            ]);

            Session::flash('success', 'Configuration saved successfully.');

        } catch (\Throwable $e) {
            $db->rollback();
            error_log("StructBrew config save failed: " . $e->getMessage());
            Session::flash('error', 'Failed to save configuration. Please try again.');
        }

        $qs = '?scope=' . urlencode($scope) . '&scope_id=' . $scopeId;
        return $this->redirect('/admin/config' . $qs);
    }
}
