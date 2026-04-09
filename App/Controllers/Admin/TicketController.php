<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Models\Ticket;

final class TicketController extends BaseAdminController
{
    // ─── Index ───────────────────────────────────────────────────────────────

    public function index(): Response
    {
        $db      = Database::getInstance();
        $page    = $this->page();
        $perPage = 25;

        $search     = (string) $this->request->query('search');
        $status     = (string) $this->request->query('status');
        $priority   = (string) $this->request->query('priority');
        $deptId     = (int) $this->request->query('department_id');
        $agentId    = (int) $this->request->query('agent_id');

        $query = $db->table('tickets')
            ->select(
                'tickets.*',
                'ticket_departments.name AS department_name',
                'ticket_departments.color AS department_color',
                'customers.first_name AS customer_first',
                'customers.last_name AS customer_last',
                'admin_users.first_name AS agent_first',
                'admin_users.last_name AS agent_last'
            )
            ->leftJoin('ticket_departments', 'tickets.department_id', '=', 'ticket_departments.id')
            ->leftJoin('customers', 'tickets.customer_id', '=', 'customers.id')
            ->leftJoin('admin_users', 'tickets.assigned_agent_id', '=', 'admin_users.id')
            ->orderBy('tickets.last_activity_at', 'DESC');

        if ($search !== '') {
            $query->whereRaw(
                "(tickets.ticket_number LIKE :s0 OR tickets.subject LIKE :s1 OR customers.email LIKE :s2 OR tickets.guest_email LIKE :s3)",
                [':s0' => "%{$search}%", ':s1' => "%{$search}%", ':s2' => "%{$search}%", ':s3' => "%{$search}%"]
            );
        }
        if ($status !== '')    { $query->where('tickets.status', $status); }
        if ($priority !== '')  { $query->where('tickets.priority', $priority); }
        if ($deptId > 0)       { $query->where('tickets.department_id', $deptId); }
        if ($agentId > 0)      { $query->where('tickets.assigned_agent_id', $agentId); }

        $tickets = $query->paginate($perPage, $page);

        $departments = $db->table('ticket_departments')->where('is_active', 1)->orderBy('sort_order')->get();
        $agents      = $db->table('admin_users')->where('is_active', 1)->orderBy('first_name')->get();

        $statuses  = ['open','in_progress','waiting_customer','waiting_third_party','on_hold','resolved','closed','reopened'];
        $priorities = ['low','normal','high','critical','urgent'];

        return $this->adminView('admin/tickets/index', [
            'title'       => 'Support Tickets',
            'tickets'     => $tickets,
            'departments' => $departments,
            'agents'      => $agents,
            'statuses'    => $statuses,
            'priorities'  => $priorities,
            'search'      => $search,
            'status'      => $status,
            'priority'    => $priority,
            'deptId'      => $deptId,
            'agentId'     => $agentId,
        ]);
    }

    // ─── Create ───────────────────────────────────────────────────────────────

    public function create(): Response
    {
        $db          = Database::getInstance();
        $departments = $db->table('ticket_departments')->where('is_active', 1)->orderBy('sort_order')->get();
        $categories  = $db->table('ticket_categories')->where('is_active', 1)->orderBy('name')->get();
        $agents      = $db->table('admin_users')->where('is_active', 1)->orderBy('first_name')->get();
        $customers   = $db->table('customers')->orderBy('email')->get();

        return $this->adminView('admin/tickets/create', [
            'title'       => 'Create Ticket',
            'departments' => $departments,
            'categories'  => $categories,
            'agents'      => $agents,
            'customers'   => $customers,
        ]);
    }

    public function store(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect('/admin/tickets/create');
        }

        $db      = Database::getInstance();
        $admin   = Auth::admin();

        $data = [
            'ticket_number'    => Ticket::generateNumber(),
            'subject'          => trim((string) $this->input('subject')),
            'status'           => 'open',
            'priority'         => $this->input('priority', 'normal'),
            'type'             => $this->input('type', 'general'),
            'source'           => 'admin',
            'requester_type'   => $this->input('requester_type', 'customer'),
            'customer_id'      => ($this->input('customer_id') !== '' && $this->input('customer_id') !== null) ? (int) $this->input('customer_id') : null,
            'guest_email'      => trim((string) $this->input('guest_email')),
            'guest_name'       => trim((string) $this->input('guest_name')),
            'assigned_agent_id'=> ($this->input('assigned_agent_id') !== '' && $this->input('assigned_agent_id') !== null) ? (int) $this->input('assigned_agent_id') : null,
            'department_id'    => ($this->input('department_id') !== '' && $this->input('department_id') !== null) ? (int) $this->input('department_id') : null,
            'category_id'      => ($this->input('category_id') !== '' && $this->input('category_id') !== null) ? (int) $this->input('category_id') : null,
            'last_activity_at' => date('Y-m-d H:i:s'),
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ];

        if (empty($data['subject'])) {
            Session::flash('error', 'Subject is required.');
            return $this->redirect('/admin/tickets/create');
        }

        Ticket::applySla($data);

        $ticketId = $db->table('tickets')->insertGetId($data);

        // Initial message
        $body = trim((string) $this->input('body'));
        if ($body !== '') {
            $db->table('ticket_replies')->insert([
                'ticket_id'    => $ticketId,
                'author_type'  => 'admin',
                'author_id'    => $admin['id'] ?? null,
                'author_name'  => $admin['name'] ?? trim(($admin['first_name'] ?? '') . ' ' . ($admin['last_name'] ?? '')) ?: 'Admin',
                'author_email' => $admin['email'] ?? '',
                'body'         => $body,
                'is_internal'  => 0,
                'created_at'   => date('Y-m-d H:i:s'),
            ]);
        }

        $this->logActivity('create', 'ticket', $ticketId, null, $data);
        Session::flash('success', 'Ticket created successfully.');
        return $this->redirect("/admin/tickets/{$ticketId}");
    }

    // ─── Show ─────────────────────────────────────────────────────────────────

    public function show(int $id): Response
    {
        $db     = Database::getInstance();
        $ticket = $db->table('tickets')
            ->select(
                'tickets.*',
                'ticket_departments.name AS department_name',
                'ticket_departments.color AS department_color',
                'ticket_categories.name AS category_name',
                'customers.first_name AS customer_first',
                'customers.last_name AS customer_last',
                'customers.email AS customer_email_addr',
                'admin_users.first_name AS agent_first',
                'admin_users.last_name AS agent_last'
            )
            ->leftJoin('ticket_departments', 'tickets.department_id', '=', 'ticket_departments.id')
            ->leftJoin('ticket_categories', 'tickets.category_id', '=', 'ticket_categories.id')
            ->leftJoin('customers', 'tickets.customer_id', '=', 'customers.id')
            ->leftJoin('admin_users', 'tickets.assigned_agent_id', '=', 'admin_users.id')
            ->where('tickets.id', $id)
            ->first();

        if (!$ticket) {
            Session::flash('error', 'Ticket not found.');
            return $this->redirect('/admin/tickets');
        }

        $replies = $db->table('ticket_replies')
            ->where('ticket_id', $id)
            ->orderBy('created_at', 'ASC')
            ->get();

        $departments   = $db->table('ticket_departments')->where('is_active', 1)->orderBy('sort_order')->get();
        $categories    = $db->table('ticket_categories')->where('is_active', 1)->orderBy('name')->get();
        $agents        = $db->table('admin_users')->where('is_active', 1)->orderBy('first_name')->get();
        $cannedAll     = $db->table('ticket_canned_responses')->where('is_active', 1)->orderBy('name')->get();
        $tags          = $db->table('ticket_tags')
            ->join('ticket_ticket_tags', 'ticket_tags.id', '=', 'ticket_ticket_tags.tag_id')
            ->where('ticket_ticket_tags.ticket_id', $id)
            ->select('ticket_tags.*')
            ->get();

        $slaBreached = false;
        if (!in_array($ticket['status'], ['resolved', 'closed'], true) && !empty($ticket['sla_resolution_due_at'])) {
            $slaBreached = strtotime($ticket['sla_resolution_due_at']) < time();
        }

        return $this->adminView('admin/tickets/show', [
            'title'       => 'Ticket #' . $ticket['ticket_number'],
            'ticket'      => $ticket,
            'replies'     => $replies,
            'departments' => $departments,
            'categories'  => $categories,
            'agents'      => $agents,
            'cannedAll'   => $cannedAll,
            'tags'        => $tags,
            'slaBreached' => $slaBreached,
        ]);
    }

    // ─── Update (status, priority, assignment) ────────────────────────────────

    public function update(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect("/admin/tickets/{$id}");
        }

        $db = Database::getInstance();
        $ticket = $db->table('tickets')->where('id', $id)->first();
        if (!$ticket) {
            Session::flash('error', 'Ticket not found.');
            return $this->redirect('/admin/tickets');
        }

        $old = $ticket;
        $data = [
            'subject'           => trim((string) $this->input('subject', $ticket['subject'])),
            'status'            => $this->input('status', $ticket['status']),
            'priority'          => $this->input('priority', $ticket['priority']),
            'type'              => $this->input('type', $ticket['type']),
            'assigned_agent_id' => ($this->input('assigned_agent_id') !== '' && $this->input('assigned_agent_id') !== null) ? (int) $this->input('assigned_agent_id') : null,
            'department_id'     => ($this->input('department_id') !== '' && $this->input('department_id') !== null) ? (int) $this->input('department_id') : null,
            'category_id'       => ($this->input('category_id') !== '' && $this->input('category_id') !== null) ? (int) $this->input('category_id') : null,
            'last_activity_at'  => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ];

        // Auto-set resolved/closed timestamps
        if ($data['status'] === 'resolved' && empty($ticket['resolved_at'])) {
            $data['resolved_at'] = date('Y-m-d H:i:s');
        }
        if ($data['status'] === 'closed' && empty($ticket['closed_at'])) {
            $data['closed_at'] = date('Y-m-d H:i:s');
        }

        $db->table('tickets')->where('id', $id)->update($data);
        $this->logActivity('update', 'ticket', $id, $old, $data);
        Session::flash('success', 'Ticket updated.');
        return $this->redirect("/admin/tickets/{$id}");
    }

    // ─── Reply ────────────────────────────────────────────────────────────────

    public function reply(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect("/admin/tickets/{$id}");
        }

        $db    = Database::getInstance();
        $admin = Auth::admin();

        $ticket = $db->table('tickets')->where('id', $id)->first();
        if (!$ticket) {
            Session::flash('error', 'Ticket not found.');
            return $this->redirect('/admin/tickets');
        }

        $body = trim((string) $this->input('body'));
        if (empty($body)) {
            Session::flash('error', 'Reply body is required.');
            return $this->redirect("/admin/tickets/{$id}");
        }

        $isInternal = (int) $this->input('is_internal', 0);

        $db->table('ticket_replies')->insert([
            'ticket_id'    => $id,
            'author_type'  => 'admin',
            'author_id'    => $admin['id'] ?? null,
            'author_name'  => $admin['name'] ?? trim(($admin['first_name'] ?? '') . ' ' . ($admin['last_name'] ?? '')) ?: 'Admin',
            'author_email' => $admin['email'] ?? '',
            'body'         => $body,
            'is_internal'  => $isInternal,
            'created_at'   => date('Y-m-d H:i:s'),
        ]);

        $updateData = ['last_activity_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')];

        // Set first_response_at if not yet set
        if (empty($ticket['first_response_at']) && !$isInternal) {
            $updateData['first_response_at']  = date('Y-m-d H:i:s');
            $updateData['sla_first_response_met'] = (!empty($ticket['sla_first_response_due_at']) && strtotime($ticket['sla_first_response_due_at']) >= time()) ? 1 : 0;
        }

        // If status is waiting_customer, move to in_progress
        if ($ticket['status'] === 'waiting_customer' && !$isInternal) {
            $updateData['status'] = 'in_progress';
        }

        $db->table('tickets')->where('id', $id)->update($updateData);

        Session::flash('success', 'Reply sent.');
        return $this->redirect("/admin/tickets/{$id}#replies");
    }

    // ─── Escalate ─────────────────────────────────────────────────────────────

    public function escalate(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect("/admin/tickets/{$id}");
        }

        $db = Database::getInstance();
        $db->table('tickets')->where('id', $id)->update([
            'is_escalated'    => 1,
            'escalated_at'    => date('Y-m-d H:i:s'),
            'priority'        => 'critical',
            'last_activity_at' => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);

        $this->logActivity('escalate', 'ticket', $id);
        Session::flash('success', 'Ticket escalated to critical priority.');
        return $this->redirect("/admin/tickets/{$id}");
    }

    // ─── Merge ────────────────────────────────────────────────────────────────

    public function merge(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect("/admin/tickets/{$id}");
        }

        $db       = Database::getInstance();
        $targetId = (int) $this->input('merge_into_id');

        if ($targetId === $id || $targetId === 0) {
            Session::flash('error', 'Invalid target ticket for merge.');
            return $this->redirect("/admin/tickets/{$id}");
        }

        $target = $db->table('tickets')->where('id', $targetId)->first();
        if (!$target) {
            Session::flash('error', 'Target ticket not found.');
            return $this->redirect("/admin/tickets/{$id}");
        }

        // Move replies to target
        $db->table('ticket_replies')->where('ticket_id', $id)->update(['ticket_id' => $targetId]);
        $db->table('ticket_attachments')->where('ticket_id', $id)->update(['ticket_id' => $targetId]);

        // Close & mark merged
        $db->table('tickets')->where('id', $id)->update([
            'status'              => 'closed',
            'merged_into_ticket_id'=> $targetId,
            'closed_at'           => date('Y-m-d H:i:s'),
            'updated_at'          => date('Y-m-d H:i:s'),
        ]);

        $db->table('tickets')->where('id', $targetId)->update([
            'last_activity_at' => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);

        $this->logActivity('merge', 'ticket', $id, null, ['merged_into' => $targetId]);
        Session::flash('success', "Ticket #{$id} merged into #{$targetId}.");
        return $this->redirect("/admin/tickets/{$targetId}");
    }

    // ─── Export ───────────────────────────────────────────────────────────────

    public function export(): Response
    {
        $db     = Database::getInstance();
        $status = (string) $this->request->query('status');

        $query = $db->table('tickets')
            ->select('tickets.*', 'ticket_departments.name AS department_name', 'customers.email AS customer_email_addr', 'admin_users.first_name AS agent_first', 'admin_users.last_name AS agent_last')
            ->leftJoin('ticket_departments', 'tickets.department_id', '=', 'ticket_departments.id')
            ->leftJoin('customers', 'tickets.customer_id', '=', 'customers.id')
            ->leftJoin('admin_users', 'tickets.assigned_agent_id', '=', 'admin_users.id')
            ->orderBy('tickets.created_at', 'DESC');

        if ($status !== '') {
            $query->where('tickets.status', $status);
        }

        $rows = $query->get();

        $filename = 'tickets_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");

        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Number', 'Subject', 'Status', 'Priority', 'Type', 'Department', 'Agent', 'Customer', 'Created', 'Last Activity']);

        foreach ($rows as $row) {
            $customer  = !empty($row['customer_email_addr']) ? $row['customer_email_addr'] : ($row['guest_email'] ?? '');
            $agentName = trim(($row['agent_first'] ?? '') . ' ' . ($row['agent_last'] ?? '')) ?: 'Unassigned';
            fputcsv($out, [
                $row['id'],
                $row['ticket_number'],
                $row['subject'],
                $row['status'],
                $row['priority'],
                $row['type'],
                $row['department_name'] ?? '',
                $agentName,
                $customer,
                $row['created_at'],
                $row['last_activity_at'],
            ]);
        }

        fclose($out);
        exit;
    }

    // ─── Departments ─────────────────────────────────────────────────────────

    public function departments(): Response
    {
        $db   = Database::getInstance();
        $deps = $db->table('ticket_departments')->orderBy('sort_order')->get();

        return $this->adminView('admin/tickets/departments', [
            'title'       => 'Ticket Departments',
            'departments' => $deps,
        ]);
    }

    public function storeDepartment(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect('/admin/tickets/departments');
        }

        $db   = Database::getInstance();
        $name = trim((string) $this->input('name'));
        $code = trim((string) $this->input('code'));

        if ($name === '' || $code === '') {
            Session::flash('error', 'Name and code are required.');
            return $this->redirect('/admin/tickets/departments');
        }

        $db->table('ticket_departments')->insert([
            'name'       => $name,
            'code'       => strtolower($code),
            'description'=> trim((string) $this->input('description')),
            'color'      => $this->input('color', '#3b82f6'),
            'is_active'  => 1,
            'sort_order' => (int) $this->input('sort_order', 0),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Department created.');
        return $this->redirect('/admin/tickets/departments');
    }

    public function updateDepartment(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect('/admin/tickets/departments');
        }

        $db = Database::getInstance();
        $db->table('ticket_departments')->where('id', $id)->update([
            'name'       => trim((string) $this->input('name')),
            'description'=> trim((string) $this->input('description')),
            'color'      => $this->input('color', '#3b82f6'),
            'is_active'  => (int) $this->input('is_active', 1),
            'sort_order' => (int) $this->input('sort_order', 0),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Department updated.');
        return $this->redirect('/admin/tickets/departments');
    }

    public function deleteDepartment(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect('/admin/tickets/departments');
        }

        Database::getInstance()->table('ticket_departments')->where('id', $id)->delete();
        Session::flash('success', 'Department deleted.');
        return $this->redirect('/admin/tickets/departments');
    }

    // ─── Mailboxes ───────────────────────────────────────────────────────────

    public function mailboxes(): Response
    {
        $db = Database::getInstance();

        $mailboxes = $db->table('ticket_mailboxes')
            ->select('ticket_mailboxes.*', 'ticket_departments.name AS department_name', 'store_domains.domain AS domain_name')
            ->leftJoin('ticket_departments', 'ticket_mailboxes.department_id', '=', 'ticket_departments.id')
            ->leftJoin('store_domains', 'ticket_mailboxes.domain_id', '=', 'store_domains.id')
            ->orderBy('ticket_mailboxes.name')
            ->get();

        $departments = $db->table('ticket_departments')->orderBy('sort_order')->get();
        $domains = $db->table('store_domains')->where('is_active', 1)->orderBy('domain')->get();

        return $this->adminView('admin/tickets/mailboxes', [
            'title' => 'Ticket Mailboxes',
            'mailboxes' => $mailboxes,
            'departments' => $departments,
            'domains' => $domains,
            'smtpSettings' => [
                'host' => $this->getGlobalConfigValue('support_mail/smtp_host'),
                'port' => $this->getGlobalConfigValue('support_mail/smtp_port', '587'),
                'encryption' => $this->getGlobalConfigValue('support_mail/smtp_encryption', 'tls'),
                'username' => $this->getGlobalConfigValue('support_mail/smtp_username'),
                'from_name' => $this->getGlobalConfigValue('support_mail/from_name'),
            ],
        ]);
    }

    public function saveMailboxSmtp(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect('/admin/tickets/mailboxes');
        }

        $host = trim((string) $this->input('smtp_host'));
        $port = trim((string) $this->input('smtp_port'));
        $enc = trim((string) $this->input('smtp_encryption', 'tls'));
        $username = trim((string) $this->input('smtp_username'));
        $password = trim((string) $this->input('smtp_password'));
        $fromName = trim((string) $this->input('from_name'));

        if ($host === '' || $port === '' || $username === '') {
            Session::flash('error', 'SMTP host, port, and username are required.');
            return $this->redirect('/admin/tickets/mailboxes');
        }

        $allowedEnc = ['none', 'ssl', 'tls'];
        if (!in_array($enc, $allowedEnc, true)) {
            $enc = 'tls';
        }

        $this->upsertGlobalConfigValue('support_mail/smtp_host', $host);
        $this->upsertGlobalConfigValue('support_mail/smtp_port', (string) ((int) $port));
        $this->upsertGlobalConfigValue('support_mail/smtp_encryption', $enc);
        $this->upsertGlobalConfigValue('support_mail/smtp_username', $username);
        $this->upsertGlobalConfigValue('support_mail/from_name', $fromName);
        if ($password !== '') {
            $this->upsertGlobalConfigValue('support_mail/smtp_password', $password);
        }

        Session::flash('success', 'Global outgoing SMTP settings saved.');
        return $this->redirect('/admin/tickets/mailboxes');
    }

    public function storeMailbox(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect('/admin/tickets/mailboxes');
        }

        $db = Database::getInstance();

        $name = trim((string) $this->input('name'));
        $email = strtolower(trim((string) $this->input('email')));

        if ($name === '' || $email === '') {
            Session::flash('error', 'Mailbox name and email are required.');
            return $this->redirect('/admin/tickets/mailboxes');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Please provide a valid email address.');
            return $this->redirect('/admin/tickets/mailboxes');
        }

        $exists = $db->table('ticket_mailboxes')->where('email', $email)->first();
        if ($exists) {
            Session::flash('error', 'This mailbox email already exists.');
            return $this->redirect('/admin/tickets/mailboxes');
        }

        $departmentInput = $this->input('department_id');
        $departmentId = ($departmentInput !== '' && $departmentInput !== null) ? (int) $departmentInput : null;

        $domainInput = $this->input('domain_id');
        $domainId = ($domainInput !== '' && $domainInput !== null) ? (int) $domainInput : null;

        $db->table('ticket_mailboxes')->insert([
            'department_id' => $departmentId,
            'domain_id' => $domainId,
            'use_global_smtp' => 1,
            'name' => $name,
            'email' => $email,
            'from_name' => trim((string) $this->input('from_name')),
            'smtp_host' => null,
            'smtp_port' => null,
            'smtp_encryption' => 'tls',
            'smtp_username' => null,
            'smtp_password' => null,
            'incoming_host' => trim((string) $this->input('incoming_host')),
            'incoming_port' => ($this->input('incoming_port') !== '' && $this->input('incoming_port') !== null) ? (int) $this->input('incoming_port') : null,
            'incoming_encryption' => $this->input('incoming_encryption', 'ssl'),
            'incoming_username' => trim((string) $this->input('incoming_username')),
            'incoming_password' => trim((string) $this->input('incoming_password')),
            'is_active' => (int) $this->input('is_active', 1),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Mailbox created.');
        return $this->redirect('/admin/tickets/mailboxes');
    }

    public function updateMailbox(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect('/admin/tickets/mailboxes');
        }

        $db = Database::getInstance();
        $mailbox = $db->table('ticket_mailboxes')->where('id', $id)->first();

        if (!$mailbox) {
            Session::flash('error', 'Mailbox not found.');
            return $this->redirect('/admin/tickets/mailboxes');
        }

        $name = trim((string) $this->input('name'));
        $email = strtolower(trim((string) $this->input('email')));

        if ($name === '' || $email === '') {
            Session::flash('error', 'Mailbox name and email are required.');
            return $this->redirect('/admin/tickets/mailboxes');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Please provide a valid email address.');
            return $this->redirect('/admin/tickets/mailboxes');
        }

        $exists = $db->table('ticket_mailboxes')
            ->where('email', $email)
            ->whereRaw('id <> :id', [':id' => $id])
            ->first();

        if ($exists) {
            Session::flash('error', 'This mailbox email already exists.');
            return $this->redirect('/admin/tickets/mailboxes');
        }

        $departmentInput = $this->input('department_id');
        $departmentId = ($departmentInput !== '' && $departmentInput !== null) ? (int) $departmentInput : null;

        $domainInput = $this->input('domain_id');
        $domainId = ($domainInput !== '' && $domainInput !== null) ? (int) $domainInput : null;

        $data = [
            'department_id' => $departmentId,
            'domain_id' => $domainId,
            'use_global_smtp' => 1,
            'name' => $name,
            'email' => $email,
            'from_name' => trim((string) $this->input('from_name')),
            'incoming_host' => trim((string) $this->input('incoming_host')),
            'incoming_port' => ($this->input('incoming_port') !== '' && $this->input('incoming_port') !== null) ? (int) $this->input('incoming_port') : null,
            'incoming_encryption' => $this->input('incoming_encryption', 'ssl'),
            'incoming_username' => trim((string) $this->input('incoming_username')),
            'is_active' => (int) $this->input('is_active', 1),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $incomingPassword = trim((string) $this->input('incoming_password'));
        if ($incomingPassword !== '') {
            $data['incoming_password'] = $incomingPassword;
        }

        $db->table('ticket_mailboxes')->where('id', $id)->update($data);

        Session::flash('success', 'Mailbox updated.');
        return $this->redirect('/admin/tickets/mailboxes');
    }

    public function deleteMailbox(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect('/admin/tickets/mailboxes');
        }

        Database::getInstance()->table('ticket_mailboxes')->where('id', $id)->delete();
        Session::flash('success', 'Mailbox deleted.');
        return $this->redirect('/admin/tickets/mailboxes');
    }

    private function getGlobalConfigValue(string $path, string $default = ''): string
    {
        $row = Database::getInstance()->table('configurations')
            ->where('path', $path)
            ->where('scope', 'global')
            ->where('scope_id', 0)
            ->first();

        return (string) ($row['value'] ?? $default);
    }

    private function upsertGlobalConfigValue(string $path, string $value): void
    {
        $db = Database::getInstance();
        $existing = $db->table('configurations')
            ->where('path', $path)
            ->where('scope', 'global')
            ->where('scope_id', 0)
            ->first();

        $now = date('Y-m-d H:i:s');

        if ($existing) {
            $db->table('configurations')->where('id', $existing['id'])->update([
                'value' => $value,
                'updated_at' => $now,
            ]);
            return;
        }

        $db->table('configurations')->insert([
            'path' => $path,
            'value' => $value,
            'scope' => 'global',
            'scope_id' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    // ─── Categories ───────────────────────────────────────────────────────────

    public function categories(): Response
    {
        $db         = Database::getInstance();
        $categories = $db->table('ticket_categories')
            ->select('ticket_categories.*', 'ticket_departments.name AS department_name')
            ->leftJoin('ticket_departments', 'ticket_categories.department_id', '=', 'ticket_departments.id')
            ->orderBy('ticket_categories.name')
            ->get();
        $departments = $db->table('ticket_departments')->where('is_active', 1)->orderBy('sort_order')->get();

        return $this->adminView('admin/tickets/categories', [
            'title'       => 'Ticket Categories',
            'categories'  => $categories,
            'departments' => $departments,
        ]);
    }

    public function storeCategory(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect('/admin/tickets/categories');
        }

        $db   = Database::getInstance();
        $name = trim((string) $this->input('name'));

        if ($name === '') {
            Session::flash('error', 'Name is required.');
            return $this->redirect('/admin/tickets/categories');
        }

        $db->table('ticket_categories')->insert([
            'name'          => $name,
            'description'   => trim((string) $this->input('description')),
            'department_id' => ($this->input('department_id') !== '' && $this->input('department_id') !== null) ? (int) $this->input('department_id') : null,
            'is_active'     => 1,
            'sort_order'    => (int) $this->input('sort_order', 0),
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Category created.');
        return $this->redirect('/admin/tickets/categories');
    }

    public function updateCategory(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect('/admin/tickets/categories');
        }

        $db = Database::getInstance();
        $db->table('ticket_categories')->where('id', $id)->update([
            'name'          => trim((string) $this->input('name')),
            'description'   => trim((string) $this->input('description')),
            'department_id' => ($this->input('department_id') !== '' && $this->input('department_id') !== null) ? (int) $this->input('department_id') : null,
            'is_active'     => (int) $this->input('is_active', 1),
            'sort_order'    => (int) $this->input('sort_order', 0),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Category updated.');
        return $this->redirect('/admin/tickets/categories');
    }

    public function deleteCategory(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect('/admin/tickets/categories');
        }

        Database::getInstance()->table('ticket_categories')->where('id', $id)->delete();
        Session::flash('success', 'Category deleted.');
        return $this->redirect('/admin/tickets/categories');
    }

    // ─── SLA Policies ─────────────────────────────────────────────────────────

    public function sla(): Response
    {
        $db       = Database::getInstance();
        $policies = $db->table('ticket_sla_policies')->orderBy('name')->get();

        return $this->adminView('admin/tickets/sla', [
            'title'    => 'SLA Policies',
            'policies' => $policies,
        ]);
    }

    public function storeSla(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect('/admin/tickets/sla');
        }

        $db   = Database::getInstance();
        $name = trim((string) $this->input('name'));

        if ($name === '') {
            Session::flash('error', 'Name is required.');
            return $this->redirect('/admin/tickets/sla');
        }

        $db->table('ticket_sla_policies')->insert([
            'name'                    => $name,
            'description'             => trim((string) $this->input('description')),
            'applies_to_priority'     => $this->input('applies_to_priority', 'normal'),
            'first_response_hours'    => (int) $this->input('first_response_hours', 8),
            'resolution_hours'        => (int) $this->input('resolution_hours', 48),
            'business_hours_only'     => (int) $this->input('business_hours_only', 0),
            'is_active'               => 1,
            'created_at'              => date('Y-m-d H:i:s'),
            'updated_at'              => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'SLA policy created.');
        return $this->redirect('/admin/tickets/sla');
    }

    public function updateSla(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect('/admin/tickets/sla');
        }

        $db = Database::getInstance();
        $db->table('ticket_sla_policies')->where('id', $id)->update([
            'name'                 => trim((string) $this->input('name')),
            'description'          => trim((string) $this->input('description')),
            'applies_to_priority'  => $this->input('applies_to_priority', 'normal'),
            'first_response_hours' => (int) $this->input('first_response_hours', 8),
            'resolution_hours'     => (int) $this->input('resolution_hours', 48),
            'business_hours_only'  => (int) $this->input('business_hours_only', 0),
            'is_active'            => (int) $this->input('is_active', 1),
            'updated_at'           => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'SLA policy updated.');
        return $this->redirect('/admin/tickets/sla');
    }

    public function deleteSla(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect('/admin/tickets/sla');
        }

        Database::getInstance()->table('ticket_sla_policies')->where('id', $id)->delete();
        Session::flash('success', 'SLA policy deleted.');
        return $this->redirect('/admin/tickets/sla');
    }

    // ─── Canned Responses ─────────────────────────────────────────────────────

    public function canned(): Response
    {
        $db      = Database::getInstance();
        $canned  = $db->table('ticket_canned_responses')
            ->select('ticket_canned_responses.*', 'ticket_departments.name AS department_name')
            ->leftJoin('ticket_departments', 'ticket_canned_responses.department_id', '=', 'ticket_departments.id')
            ->orderBy('ticket_canned_responses.sort_order')
            ->get();
        $departments = $db->table('ticket_departments')->where('is_active', 1)->orderBy('sort_order')->get();

        return $this->adminView('admin/tickets/canned', [
            'title'       => 'Canned Responses',
            'canned'      => $canned,
            'departments' => $departments,
        ]);
    }

    public function storeCanned(): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect('/admin/tickets/canned');
        }

        $db   = Database::getInstance();
        $name = trim((string) $this->input('name'));
        $body = trim((string) $this->input('body'));

        if ($name === '' || $body === '') {
            Session::flash('error', 'Name and body are required.');
            return $this->redirect('/admin/tickets/canned');
        }

        $db->table('ticket_canned_responses')->insert([
            'department_id' => ($this->input('department_id') !== '' && $this->input('department_id') !== null) ? (int) $this->input('department_id') : null,
            'name'          => $name,
            'subject'       => trim((string) $this->input('subject')),
            'body'          => $body,
            'is_active'     => 1,
            'sort_order'    => (int) $this->input('sort_order', 0),
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Canned response created.');
        return $this->redirect('/admin/tickets/canned');
    }

    public function updateCanned(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect('/admin/tickets/canned');
        }

        $db = Database::getInstance();
        $db->table('ticket_canned_responses')->where('id', $id)->update([
            'department_id' => ($this->input('department_id') !== '' && $this->input('department_id') !== null) ? (int) $this->input('department_id') : null,
            'name'          => trim((string) $this->input('name')),
            'subject'       => trim((string) $this->input('subject')),
            'body'          => trim((string) $this->input('body')),
            'is_active'     => (int) $this->input('is_active', 1),
            'sort_order'    => (int) $this->input('sort_order', 0),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        Session::flash('success', 'Canned response updated.');
        return $this->redirect('/admin/tickets/canned');
    }

    public function deleteCanned(int $id): Response
    {
        if (!$this->verifyCsrf()) {
            Session::flash('error', 'Invalid CSRF token.');
            return $this->redirect('/admin/tickets/canned');
        }

        Database::getInstance()->table('ticket_canned_responses')->where('id', $id)->delete();
        Session::flash('success', 'Canned response deleted.');
        return $this->redirect('/admin/tickets/canned');
    }
}
