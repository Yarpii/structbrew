<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Database;
use App\Core\Response;
use App\Core\Session;

final class DealerController extends BaseAdminController
{
    // ─── Applications ────────────────────────────────────────

    public function applications(): Response
    {
        $db = Database::getInstance();

        $status = (string) ($this->request?->query('status') ?? '');
        $search = (string) ($this->request?->query('q') ?? '');
        $page   = max(1, (int) ($this->request?->query('page') ?? 1));

        $query = $db->table('dealer_applications')->orderBy('created_at', 'DESC');

        if ($status !== '' && in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $status);
        }
        if ($search !== '') {
            $safe = addslashes($search);
            $query->whereRaw(
                "(email LIKE :srch1 OR contact_name LIKE :srch2 OR company_name LIKE :srch3)",
                [':srch1' => "%{$safe}%", ':srch2' => "%{$safe}%", ':srch3' => "%{$safe}%"]
            );
        }

        $applications = $query->paginate(20, $page);

        $counts = [
            'pending'  => $db->table('dealer_applications')->where('status', 'pending')->count(),
            'approved' => $db->table('dealer_applications')->where('status', 'approved')->count(),
            'rejected' => $db->table('dealer_applications')->where('status', 'rejected')->count(),
        ];

        return $this->adminView('admin/dealers/index', [
            'pageTitle'    => 'Dealer Applications',
            'applications' => $applications,
            'counts'       => $counts,
            'statusFilter' => $status,
            'search'       => $search,
        ]);
    }

    public function showApplication(string $id): Response
    {
        $db          = Database::getInstance();
        $application = $db->table('dealer_applications')->where('id', (int) $id)->first();

        if (!$application) {
            Session::flash('error', 'Application not found.');
            return $this->redirect('/admin/dealers');
        }

        $dealerAccount = $db->table('dealer_accounts')
            ->where('application_id', (int) $id)
            ->first();

        return $this->adminView('admin/dealers/show', [
            'pageTitle'     => 'Dealer Application',
            'application'   => $application,
            'dealerAccount' => $dealerAccount,
        ]);
    }

    public function approveApplication(string $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/dealers/' . $id);
        }

        $db          = Database::getInstance();
        $application = $db->table('dealer_applications')->where('id', (int) $id)->first();

        if (!$application) {
            Session::flash('error', 'Application not found.');
            return $this->redirect('/admin/dealers');
        }

        if ($application['status'] !== 'pending') {
            Session::flash('error', 'Only pending applications can be approved.');
            return $this->redirect('/admin/dealers/' . $id);
        }

        $existing = $db->table('dealer_accounts')
            ->where('application_id', (int) $id)
            ->first();

        if ($existing) {
            Session::flash('error', 'A dealer account already exists for this application.');
            return $this->redirect('/admin/dealers/' . $id);
        }

        $discountRate   = max(0, min(100, (float) $this->input('discount_rate', 0)));
        $creditLimit    = max(0, (float) $this->input('credit_limit', 0));
        $paymentTerms   = (string) $this->input('payment_terms', 'prepaid');
        if (!in_array($paymentTerms, ['prepaid', 'net15', 'net30', 'net60'], true)) {
            $paymentTerms = 'prepaid';
        }

        $accountNumber = $this->generateAccountNumber($db);

        $customer = $db->table('customers')
            ->where('email', strtolower((string) $application['email']))
            ->first();

        $db->table('dealer_applications')
            ->where('id', (int) $id)
            ->update(['status' => 'approved', 'updated_at' => date('Y-m-d H:i:s')]);

        $db->table('dealer_accounts')->insert([
            'application_id' => (int) $id,
            'customer_id'    => $customer ? (int) $customer['id'] : null,
            'company_name'   => (string) $application['company_name'],
            'contact_name'   => (string) $application['contact_name'],
            'email'          => strtolower((string) $application['email']),
            'phone'          => $application['phone'] ?? null,
            'account_number' => $accountNumber,
            'discount_rate'  => $discountRate,
            'credit_limit'   => $creditLimit,
            'payment_terms'  => $paymentTerms,
            'status'         => 'active',
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Application approved and dealer account created.');
        return $this->redirect('/admin/dealers/' . $id);
    }

    public function rejectApplication(string $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/dealers/' . $id);
        }

        $db          = Database::getInstance();
        $application = $db->table('dealer_applications')->where('id', (int) $id)->first();

        if (!$application) {
            Session::flash('error', 'Application not found.');
            return $this->redirect('/admin/dealers');
        }

        $notes = trim((string) $this->input('admin_notes', ''));

        $db->table('dealer_applications')
            ->where('id', (int) $id)
            ->update([
                'status'      => 'rejected',
                'admin_notes' => $notes ?: null,
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);

        Session::flash('success', 'Application rejected.');
        return $this->redirect('/admin/dealers/' . $id);
    }

    // ─── Dealer Accounts ─────────────────────────────────────

    public function accounts(): Response
    {
        $db = Database::getInstance();

        $search = (string) ($this->request?->query('q') ?? '');
        $status = (string) ($this->request?->query('status') ?? '');
        $page   = max(1, (int) ($this->request?->query('page') ?? 1));

        $query = $db->table('dealer_accounts')->orderBy('created_at', 'DESC');

        if ($status !== '' && in_array($status, ['active', 'paused', 'suspended'], true)) {
            $query->where('status', $status);
        }
        if ($search !== '') {
            $safe = addslashes($search);
            $query->whereRaw(
                "(email LIKE :srch1 OR contact_name LIKE :srch2 OR company_name LIKE :srch3 OR account_number LIKE :srch4)",
                [':srch1' => "%{$safe}%", ':srch2' => "%{$safe}%", ':srch3' => "%{$safe}%", ':srch4' => "%{$safe}%"]
            );
        }

        $accounts = $query->paginate(20, $page);

        return $this->adminView('admin/dealers/accounts', [
            'pageTitle'    => 'Dealer Accounts',
            'accounts'     => $accounts,
            'statusFilter' => $status,
            'search'       => $search,
        ]);
    }

    public function showAccount(string $id): Response
    {
        $db      = Database::getInstance();
        $account = $db->table('dealer_accounts')->where('id', (int) $id)->first();

        if (!$account) {
            Session::flash('error', 'Dealer account not found.');
            return $this->redirect('/admin/dealers/accounts');
        }

        $application = null;
        if (!empty($account['application_id'])) {
            $application = $db->table('dealer_applications')
                ->where('id', (int) $account['application_id'])
                ->first();
        }

        return $this->adminView('admin/dealers/account_show', [
            'pageTitle'   => 'Dealer Account',
            'account'     => $account,
            'application' => $application,
        ]);
    }

    public function updateAccount(string $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid security token.');
            return $this->redirect('/admin/dealers/accounts/' . $id);
        }

        $db      = Database::getInstance();
        $account = $db->table('dealer_accounts')->where('id', (int) $id)->first();

        if (!$account) {
            Session::flash('error', 'Dealer account not found.');
            return $this->redirect('/admin/dealers/accounts');
        }

        $discountRate = max(0, min(100, (float) $this->input('discount_rate', $account['discount_rate'])));
        $creditLimit  = max(0, (float) $this->input('credit_limit', $account['credit_limit']));
        $paymentTerms = (string) $this->input('payment_terms', $account['payment_terms']);
        if (!in_array($paymentTerms, ['prepaid', 'net15', 'net30', 'net60'], true)) {
            $paymentTerms = 'prepaid';
        }
        $status = (string) $this->input('status', $account['status']);
        if (!in_array($status, ['active', 'paused', 'suspended'], true)) {
            $status = 'active';
        }
        $notes = trim((string) $this->input('notes', $account['notes'] ?? ''));

        $db->table('dealer_accounts')
            ->where('id', (int) $id)
            ->update([
                'discount_rate'  => $discountRate,
                'credit_limit'   => $creditLimit,
                'payment_terms'  => $paymentTerms,
                'status'         => $status,
                'notes'          => $notes ?: null,
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);

        Session::flash('success', 'Dealer account updated.');
        return $this->redirect('/admin/dealers/accounts/' . $id);
    }

    // ─── Helpers ─────────────────────────────────────────────

    private function generateAccountNumber(mixed $db): string
    {
        do {
            $number = 'DLR-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
            $exists = $db->table('dealer_accounts')->where('account_number', $number)->first();
        } while ($exists);

        return $number;
    }

    private function verifyCsrf(): bool
    {
        return \App\Core\Session::verifyCsrf((string) $this->input('_csrf_token', ''));
    }
}
