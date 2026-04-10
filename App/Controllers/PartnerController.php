<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;

final class PartnerController extends BaseStorefrontController
{
    /**
     * Public partner program landing page.
     */
    public function landing(): Response
    {
        return $this->storefrontView('pages.partner-program', [
            'title' => 'Partner Program',
        ]);
    }

    /**
     * Handle partner application form submission.
     */
    public function apply(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token. Please try again.');
            return $this->redirect('/partner-program');
        }

        $v = Validator::make([
            'first_name' => $this->input('first_name', ''),
            'last_name'  => $this->input('last_name', ''),
            'email'      => $this->input('email', ''),
            'company'    => $this->input('company', ''),
            'website'    => $this->input('website', ''),
            'country'    => $this->input('country', ''),
            'message'    => $this->input('message', ''),
        ], [
            'first_name' => 'required|max:100',
            'last_name'  => 'required|max:100',
            'email'      => 'required|email',
            'company'    => 'max:191',
            'website'    => 'max:255',
            'message'    => 'max:3000',
        ]);

        if ($v->fails()) {
            $allErrors = array_merge(...array_values($v->errors()));
            Session::flash('error', implode(' ', $allErrors));
            return $this->redirect('/partner-program');
        }

        $db = Database::getInstance();

        // Prevent duplicate pending/approved applications from same email
        $existing = $db->table('partner_applications')
            ->where('email', strtolower(trim((string) $this->input('email', ''))))
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            Session::flash('error', 'An application for this email address already exists or has been approved.');
            return $this->redirect('/partner-program');
        }

        $db->table('partner_applications')->insert([
            'first_name' => trim((string) $this->input('first_name', '')),
            'last_name'  => trim((string) $this->input('last_name', '')),
            'email'      => strtolower(trim((string) $this->input('email', ''))),
            'company'    => trim((string) $this->input('company', '')) ?: null,
            'website'    => trim((string) $this->input('website', '')) ?: null,
            'country'    => trim((string) $this->input('country', '')) ?: null,
            'message'    => trim((string) $this->input('message', '')) ?: null,
            'status'     => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Your application has been submitted! We will review it and get back to you within 2–3 business days.');
        return $this->redirect('/partner-program');
    }

    /**
     * Authenticated partner dashboard.
     */
    public function dashboard(): Response
    {
        if ($response = $this->redirectIfGuest()) {
            return $response;
        }

        $db = Database::getInstance();
        $customerId = (int) Auth::customerId();
        $customer = Auth::customer();

        $partnerAccount = $db->table('partner_accounts')
            ->where('customer_id', $customerId)
            ->where('status', '!=', 'suspended')
            ->first();

        // If no linked account, check by email
        if (!$partnerAccount && !empty($customer['email'])) {
            $partnerAccount = $db->table('partner_accounts')
                ->where('email', strtolower((string) $customer['email']))
                ->where('status', '!=', 'suspended')
                ->first();

            // Link it to the customer
            if ($partnerAccount) {
                $db->table('partner_accounts')
                    ->where('id', $partnerAccount['id'])
                    ->update(['customer_id' => $customerId, 'updated_at' => date('Y-m-d H:i:s')]);
            }
        }

        $application = null;
        if (!$partnerAccount && !empty($customer['email'])) {
            $application = $db->table('partner_applications')
                ->where('email', strtolower((string) $customer['email']))
                ->orderBy('created_at', 'DESC')
                ->first();
        }

        $referrals = [];
        if ($partnerAccount) {
            $referrals = $db->table('partner_referrals')
                ->where('partner_account_id', $partnerAccount['id'])
                ->orderBy('created_at', 'DESC')
                ->get();
        }

        $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
            . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');

        return $this->storefrontView('account.partner', [
            'title'          => 'Partner Dashboard',
            'partnerAccount' => $partnerAccount,
            'application'    => $application,
            'referrals'      => $referrals,
            'baseUrl'        => $baseUrl,
            'customer'       => $customer,
        ]);
    }

    /**
     * Track a referral click, set cookie, and redirect to the store.
     */
    public function trackClick(string $code): Response
    {
        $code = preg_replace('/[^A-Za-z0-9]/', '', $code);

        if ($code !== '') {
            $db = Database::getInstance();
            $account = $db->table('partner_accounts')
                ->where('referral_code', $code)
                ->where('status', 'active')
                ->first();

            if ($account) {
                $db->table('partner_accounts')
                    ->where('id', $account['id'])
                    ->update([
                        'total_clicks' => (int) $account['total_clicks'] + 1,
                        'updated_at'   => date('Y-m-d H:i:s'),
                    ]);

                // Store referral code in cookie for 30 days
                setcookie('partner_ref', $code, [
                    'expires'  => time() + (30 * 24 * 3600),
                    'path'     => '/',
                    'secure'   => isset($_SERVER['HTTPS']),
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]);
            }
        }

        $redirect = $this->request?->query('to') ?? '/';
        if (!str_starts_with((string) $redirect, '/') || str_starts_with((string) $redirect, '//')) {
            $redirect = '/';
        }

        return Response::redirect((string) $redirect);
    }
}
