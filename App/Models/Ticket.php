<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Core\Database;

class Ticket extends Model
{
    protected static string $table = 'tickets';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'ticket_number',
        'subject',
        'status',
        'priority',
        'type',
        'source',
        'requester_type',
        'customer_id',
        'guest_email',
        'guest_name',
        'assigned_agent_id',
        'department_id',
        'category_id',
        'brand_id',
        'website_id',
        'store_view_id',
        'order_id',
        'sla_policy_id',
        'sla_first_response_due_at',
        'sla_resolution_due_at',
        'sla_first_response_met',
        'sla_resolution_met',
        'first_response_at',
        'resolved_at',
        'closed_at',
        'last_activity_at',
        'is_escalated',
        'escalated_at',
        'merged_into_ticket_id',
        'custom_fields',
    ];
    protected static array $casts = [
        'custom_fields' => 'json',
    ];

    /**
     * Generate a unique ticket number: TKT-YYYYMMDD-NNNNN
     */
    public static function generateNumber(): string
    {
        $db = Database::getInstance();
        $date = date('Ymd');
        $prefix = "TKT-{$date}-";

        $last = $db->table('tickets')
            ->whereRaw("ticket_number LIKE :prefix", [':prefix' => $prefix . '%'])
            ->orderBy('id', 'DESC')
            ->first();

        $seq = 1;
        if ($last) {
            $parts = explode('-', $last['ticket_number']);
            $seq = (int) end($parts) + 1;
        }

        return $prefix . str_pad((string) $seq, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Apply SLA deadlines based on priority.
     */
    public static function applySla(array &$data): void
    {
        $db = Database::getInstance();
        $sla = $db->table('ticket_sla_policies')
            ->where('applies_to_priority', $data['priority'] ?? 'normal')
            ->where('is_active', 1)
            ->first();

        if ($sla) {
            $data['sla_policy_id']             = $sla['id'];
            $data['sla_first_response_due_at'] = date('Y-m-d H:i:s', time() + (int) $sla['first_response_hours'] * 3600);
            $data['sla_resolution_due_at']     = date('Y-m-d H:i:s', time() + (int) $sla['resolution_hours'] * 3600);
        }
    }

    /**
     * Replies visible to the requester (non-internal).
     */
    public function publicReplies(): array
    {
        $db = Database::getInstance();
        return $db->table('ticket_replies')
            ->where('ticket_id', $this->getId())
            ->where('is_internal', 0)
            ->orderBy('created_at', 'ASC')
            ->get();
    }

    /**
     * All replies (public + internal) — admin use.
     */
    public function allReplies(): array
    {
        $db = Database::getInstance();
        return $db->table('ticket_replies')
            ->where('ticket_id', $this->getId())
            ->orderBy('created_at', 'ASC')
            ->get();
    }

    /**
     * Tags attached to this ticket.
     */
    public function tags(): array
    {
        $db = Database::getInstance();
        return $db->table('ticket_tags')
            ->join('ticket_ticket_tags', 'ticket_tags.id', '=', 'ticket_ticket_tags.tag_id')
            ->where('ticket_ticket_tags.ticket_id', $this->getId())
            ->select('ticket_tags.*')
            ->get();
    }

    /**
     * Watchers on this ticket.
     */
    public function watchers(): array
    {
        $db = Database::getInstance();
        return $db->table('ticket_watchers')
            ->where('ticket_id', $this->getId())
            ->get();
    }

    /**
     * Attachments on this ticket (not tied to a specific reply).
     */
    public function attachments(): array
    {
        $db = Database::getInstance();
        return $db->table('ticket_attachments')
            ->where('ticket_id', $this->getId())
            ->whereRaw('reply_id IS NULL', [])
            ->get();
    }

    /**
     * Check whether SLA is breached.
     */
    public function isSlaBreached(): bool
    {
        if (in_array($this->get('status'), ['resolved', 'closed'], true)) {
            return false;
        }
        $due = $this->get('sla_resolution_due_at');
        return $due && strtotime($due) < time();
    }
}
