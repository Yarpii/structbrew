<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;

final class PartnerController extends BaseAdminController
{
    // ─── Applications ────────────────────────────────────────

    public function applications(): Response
    {
        $db = Database::getInstance();

        $status = (string) ($this->request?->query('status') ?? '');
        $search = (string) ($this->request?->query('q') ?? '');
        $page   = max(1, (int) ($this->request?->query('page') ?? 1));

        $query = $db->table('partner_applications')->orderBy('created_at', 'DESC');

        if ($status !== '' && in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $status);
        }
        if ($search !== '') {
            $safe = addslashes($search);
            $query->whereRaw(
                "(email LIKE :srch1 OR first_name LIKE :srch2 OR last_name LIKE :srch3 OR company LIKE :srch4)",
                [':srch1' => "%{$safe}%", ':srch2' => "%{$safe}%", ':srch3' => "%{$safe}%", ':srch4' => "%{$safe}%"]
            );
        }

        $applications = $query->paginate(20, $page);

        $counts = [
            'pending'  => $db->table('partner_applications')->where('status', 'pending')->count(),
            'approved' => $db->table('partner_applications')->where('status', 'approved')->count(),
            'rejected' => $db->table('partner_applications')->where('status', 'rejected')->count(),
        ];

        return $this->adminView('admin/partners/index', [
            'pageTitle'    => 'Partner Applications',
            'applications' => $applications,
            'counts'       => $counts,
            'statusFilter' => $status,
            'search'       => $search,
        ]);
    }

    public function showApplication(string $id): Response
    {
        $db = Database::getInstance();
        $application = $db->table('partner_applications')->where('id', (int) $id)->first();

        if (!$application) {
            Session::flash('error', 'Application not found.');
            return $this->redirect('/admin/partners');
        }

        $partnerAccount = $db->table('partner_accounts')
            ->where('application_id', (int) $id)
            ->first();

        return $this->adminView('admin/partners/show', [
            'pageTitle'      => 'Partner Application',
            'application'    => $application,
            'partnerAccount' => $partnerAccount,
        ]);
    }

    public function approveApplication(string $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/partners/' . $id);
        }

        $db = Database::getInstance();
        $application = $db->table('partner_applications')->where('id', (int) $id)->first();

        if (!$application) {
            Session::flash('error', 'Application not found.');
            return $this->redirect('/admin/partners');
        }

        if ($application['status'] !== 'pending') {
            Session::flash('error', 'Only pending applications can be approved.');
            return $this->redirect('/admin/partners/' . $id);
        }

        // Check if partner account already exists
        $existing = $db->table('partner_accounts')
            ->where('application_id', (int) $id)
            ->first();

        if ($existing) {
            Session::flash('error', 'A partner account already exists for this application.');
            return $this->redirect('/admin/partners/' . $id);
        }

        $commissionRate = max(0, min(100, (float) $this->input('commission_rate', 10)));
        $referralCode   = $this->generateReferralCode($db);

        // Find linked customer account if any
        $customer = $db->table('customers')
            ->where('email', strtolower((string) $application['email']))
            ->first();

        $db->table('partner_applications')
            ->where('id', (int) $id)
            ->update(['status' => 'approved', 'updated_at' => date('Y-m-d H:i:s')]);

        $db->table('partner_accounts')->insert([
            'application_id'         => (int) $id,
            'customer_id'            => $customer ? (int) $customer['id'] : null,
            'first_name'             => (string) $application['first_name'],
            'last_name'              => (string) $application['last_name'],
            'email'                  => strtolower((string) $application['email']),
            'company'                => $application['company'] ?? null,
            'referral_code'          => $referralCode,
            'commission_rate'        => $commissionRate,
            'total_clicks'           => 0,
            'total_conversions'      => 0,
            'total_commission_earned'=> 0.00,
            'balance'                => 0.00,
            'status'                 => 'active',
            'created_at'             => date('Y-m-d H:i:s'),
            'updated_at'             => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Application approved and partner account created.');
        return $this->redirect('/admin/partners/' . $id);
    }

    public function rejectApplication(string $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/partners/' . $id);
        }

        $db = Database::getInstance();
        $application = $db->table('partner_applications')->where('id', (int) $id)->first();

        if (!$application) {
            Session::flash('error', 'Application not found.');
            return $this->redirect('/admin/partners');
        }

        $notes = trim((string) $this->input('admin_notes', ''));

        $db->table('partner_applications')
            ->where('id', (int) $id)
            ->update([
                'status'      => 'rejected',
                'admin_notes' => $notes ?: null,
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);

        Session::flash('success', 'Application rejected.');
        return $this->redirect('/admin/partners/' . $id);
    }

    // ─── Partner Accounts ────────────────────────────────────

    public function accounts(): Response
    {
        $db = Database::getInstance();

        $search = (string) ($this->request?->query('q') ?? '');
        $status = (string) ($this->request?->query('status') ?? '');
        $page   = max(1, (int) ($this->request?->query('page') ?? 1));

        $query = $db->table('partner_accounts')->orderBy('created_at', 'DESC');

        if ($status !== '' && in_array($status, ['active', 'paused', 'suspended'], true)) {
            $query->where('status', $status);
        }
        if ($search !== '') {
            $safe = addslashes($search);
            $query->whereRaw(
                "(email LIKE :srch1 OR first_name LIKE :srch2 OR last_name LIKE :srch3 OR company LIKE :srch4 OR referral_code LIKE :srch5)",
                [':srch1' => "%{$safe}%", ':srch2' => "%{$safe}%", ':srch3' => "%{$safe}%", ':srch4' => "%{$safe}%", ':srch5' => "%{$safe}%"]
            );
        }

        $accounts = $query->paginate(20, $page);

        return $this->adminView('admin/partners/accounts', [
            'pageTitle'    => 'Partner Accounts',
            'accounts'     => $accounts,
            'statusFilter' => $status,
            'search'       => $search,
        ]);
    }

    public function showAccount(string $id): Response
    {
        $db = Database::getInstance();
        $account = $db->table('partner_accounts')->where('id', (int) $id)->first();

        if (!$account) {
            Session::flash('error', 'Partner account not found.');
            return $this->redirect('/admin/partners/accounts');
        }

        $referrals = $db->table('partner_referrals')
            ->where('partner_account_id', (int) $id)
            ->orderBy('created_at', 'DESC')
            ->get();

        $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
            . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');

        return $this->adminView('admin/partners/account_show', [
            'pageTitle' => 'Partner Account',
            'account'   => $account,
            'referrals' => $referrals,
            'baseUrl'   => $baseUrl,
        ]);
    }

    public function updateAccount(string $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/partners/accounts/' . $id);
        }

        $db = Database::getInstance();
        $account = $db->table('partner_accounts')->where('id', (int) $id)->first();

        if (!$account) {
            Session::flash('error', 'Partner account not found.');
            return $this->redirect('/admin/partners/accounts');
        }

        $commissionRate = max(0, min(100, (float) $this->input('commission_rate', $account['commission_rate'])));
        $status         = (string) $this->input('status', $account['status']);
        if (!in_array($status, ['active', 'paused', 'suspended'], true)) {
            $status = 'active';
        }

        $db->table('partner_accounts')
            ->where('id', (int) $id)
            ->update([
                'commission_rate' => $commissionRate,
                'status'          => $status,
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);

        Session::flash('success', 'Partner account updated.');
        return $this->redirect('/admin/partners/accounts/' . $id);
    }

    public function addReferral(string $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/partners/accounts/' . $id);
        }

        $db = Database::getInstance();
        $account = $db->table('partner_accounts')->where('id', (int) $id)->first();

        if (!$account) {
            Session::flash('error', 'Partner account not found.');
            return $this->redirect('/admin/partners/accounts');
        }

        $orderTotal       = max(0, (float) $this->input('order_total', 0));
        $commissionAmount = max(0, (float) $this->input('commission_amount', round($orderTotal * ((float) $account['commission_rate'] / 100), 2)));
        $note             = trim((string) $this->input('note', ''));
        $orderId          = (int) $this->input('order_id', 0) ?: null;

        $db->table('partner_referrals')->insert([
            'partner_account_id' => (int) $id,
            'order_id'           => $orderId,
            'referral_code'      => (string) $account['referral_code'],
            'order_total'        => $orderTotal,
            'commission_amount'  => $commissionAmount,
            'status'             => 'pending',
            'note'               => $note ?: null,
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s'),
        ]);

        // Update account totals
        $db->table('partner_accounts')
            ->where('id', (int) $id)
            ->update([
                'total_conversions'       => (int) $account['total_conversions'] + 1,
                'total_commission_earned' => round((float) $account['total_commission_earned'] + $commissionAmount, 2),
                'balance'                 => round((float) $account['balance'] + $commissionAmount, 2),
                'updated_at'              => date('Y-m-d H:i:s'),
            ]);

        Session::flash('success', 'Referral conversion added.');
        return $this->redirect('/admin/partners/accounts/' . $id);
    }

    public function updateReferralStatus(string $id, string $refId): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/partners/accounts/' . $id);
        }

        $db      = Database::getInstance();
        $account = $db->table('partner_accounts')->where('id', (int) $id)->first();
        $ref     = $db->table('partner_referrals')->where('id', (int) $refId)->where('partner_account_id', (int) $id)->first();

        if (!$account || !$ref) {
            Session::flash('error', 'Record not found.');
            return $this->redirect('/admin/partners/accounts/' . $id);
        }

        $newStatus = (string) $this->input('status', $ref['status']);
        if (!in_array($newStatus, ['pending', 'approved', 'paid', 'rejected'], true)) {
            $newStatus = 'pending';
        }

        $db->table('partner_referrals')
            ->where('id', (int) $refId)
            ->update(['status' => $newStatus, 'updated_at' => date('Y-m-d H:i:s')]);

        // If marking as paid, deduct from balance
        if ($newStatus === 'paid' && $ref['status'] !== 'paid') {
            $db->table('partner_accounts')
                ->where('id', (int) $id)
                ->update([
                    'balance'    => max(0, round((float) $account['balance'] - (float) $ref['commission_amount'], 2)),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }

        // If un-paying, add back to balance
        if ($ref['status'] === 'paid' && $newStatus !== 'paid') {
            $db->table('partner_accounts')
                ->where('id', (int) $id)
                ->update([
                    'balance'    => round((float) $account['balance'] + (float) $ref['commission_amount'], 2),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }

        Session::flash('success', 'Referral status updated.');
        return $this->redirect('/admin/partners/accounts/' . $id);
    }

    // ─── Helpers ─────────────────────────────────────────────

    private function generateReferralCode(mixed $db): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        do {
            $code = '';
            for ($i = 0; $i < 8; $i++) {
                $code .= $chars[random_int(0, strlen($chars) - 1)];
            }
            $exists = $db->table('partner_accounts')->where('referral_code', $code)->first();
        } while ($exists);

        return $code;
    }
}
