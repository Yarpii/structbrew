<?php ?>
<div class="space-y-6">
    <div class="bg-white rounded-md border border-gray-200 p-5">
        <div class="flex items-start justify-between gap-4 mb-4">
            <div>
                <h2 class="text-sm font-semibold text-gray-900">SMTP (Outgoing) — One-time Setup</h2>
                <p class="text-xs text-gray-500 mt-1">Used globally by all department mailboxes.</p>
            </div>
        </div>

        <form method="POST" action="/admin/tickets/mailboxes/smtp" class="grid grid-cols-1 md:grid-cols-6 gap-3">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">SMTP Host *</label>
                <input type="text" name="smtp_host" value="<?= htmlspecialchars($smtpSettings['host'] ?? '') ?>" required class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Port *</label>
                <input type="number" name="smtp_port" value="<?= htmlspecialchars($smtpSettings['port'] ?? '587') ?>" required class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Encryption</label>
                <select name="smtp_encryption" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm">
                    <option value="tls" <?= (($smtpSettings['encryption'] ?? 'tls') === 'tls') ? 'selected' : '' ?>>TLS</option>
                    <option value="ssl" <?= (($smtpSettings['encryption'] ?? '') === 'ssl') ? 'selected' : '' ?>>SSL</option>
                    <option value="none" <?= (($smtpSettings['encryption'] ?? '') === 'none') ? 'selected' : '' ?>>None</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">SMTP Username *</label>
                <input type="text" name="smtp_username" value="<?= htmlspecialchars($smtpSettings['username'] ?? '') ?>" required class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">From Name</label>
                <input type="text" name="from_name" value="<?= htmlspecialchars($smtpSettings['from_name'] ?? '') ?>" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm">
            </div>
            <div class="md:col-span-3">
                <label class="block text-xs font-medium text-gray-600 mb-1">SMTP Password</label>
                <input type="password" name="smtp_password" placeholder="Leave blank to keep current password" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm">
            </div>
            <div class="md:col-span-3 flex items-end">
                <button type="submit" class="w-full py-2 bg-gray-900 hover:bg-black text-white text-sm font-medium rounded-md">Save Global SMTP</button>
            </div>
        </form>
    </div>

    <div class="flex items-center justify-between">
        <p class="text-sm text-gray-500">Create mailbox accounts and map each mailbox to a domain + department.</p>
        <button onclick="document.getElementById('createMailboxModal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Mailbox
        </button>
    </div>

    <?php if (!empty($flashSuccess)): ?>
    <div class="px-4 py-3 rounded-md bg-green-50 border border-green-200 text-green-700 text-sm"><?= htmlspecialchars($flashSuccess) ?></div>
    <?php endif; ?>
    <?php if (!empty($flashError)): ?>
    <div class="px-4 py-3 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm"><?= htmlspecialchars($flashError) ?></div>
    <?php endif; ?>

    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Mailbox</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Domain</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Department</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Outgoing</th>
                    <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Incoming</th>
                    <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Active</th>
                    <th class="text-right px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($mailboxes as $mailbox): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800"><?= htmlspecialchars($mailbox['name']) ?></div>
                        <div class="text-xs text-gray-500"><?= htmlspecialchars($mailbox['email']) ?></div>
                    </td>
                    <td class="px-4 py-3 text-gray-600 text-xs"><?= htmlspecialchars($mailbox['domain_name'] ?? 'Not linked') ?></td>
                    <td class="px-4 py-3 text-gray-600 text-xs"><?= htmlspecialchars($mailbox['department_name'] ?? 'Not linked') ?></td>
                    <td class="px-4 py-3 text-gray-600 text-xs">Global SMTP</td>
                    <td class="px-4 py-3 text-gray-600 text-xs">
                        <?php if (!empty($mailbox['incoming_host'])): ?>
                            <?= htmlspecialchars(($mailbox['incoming_host'] ?? '') . ':' . (string)($mailbox['incoming_port'] ?? '')) ?><br>
                            <span class="text-gray-400"><?= htmlspecialchars($mailbox['incoming_encryption'] ?? 'none') ?></span>
                        <?php else: ?>
                            <span class="text-gray-400">Not configured</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium <?= (int)$mailbox['is_active'] ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' ?>">
                            <?= (int)$mailbox['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="openEditMailbox(<?= htmlspecialchars(json_encode($mailbox), ENT_QUOTES) ?>)"
                                    class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-md transition-colors">Edit</button>
                            <form method="POST" action="/admin/tickets/mailboxes/<?= (int)$mailbox['id'] ?>/delete" onsubmit="return confirm('Delete this mailbox?')">
                                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
                                <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-medium rounded-md transition-colors">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($mailboxes)): ?>
                <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400">No mailboxes yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="createMailboxModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 overflow-y-auto p-6">
    <div class="bg-white rounded-md shadow-xl w-full max-w-3xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900">Add Mailbox</h3>
            <button onclick="document.getElementById('createMailboxModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form method="POST" action="/admin/tickets/mailboxes" class="space-y-4">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Mailbox Name *</label>
                    <input type="text" name="name" required class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Email *</label>
                    <input type="email" name="email" required class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">From Name</label>
                    <input type="text" name="from_name" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Domain</label>
                    <select name="domain_id" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm">
                        <option value="">Not linked</option>
                        <?php foreach ($domains as $domain): ?>
                        <option value="<?= (int)$domain['id'] ?>"><?= htmlspecialchars($domain['domain']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Department Link</label>
                    <select name="department_id" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm">
                        <option value="">Not linked</option>
                        <?php foreach ($departments as $dept): ?>
                        <option value="<?= (int)$dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <h4 class="text-sm font-semibold text-gray-800 mb-3">Incoming (Receive)</h4>
                <div class="grid grid-cols-4 gap-3">
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Incoming Host</label>
                        <input type="text" name="incoming_host" placeholder="imap.example.com" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Port</label>
                        <input type="number" name="incoming_port" value="993" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Encryption</label>
                        <select name="incoming_encryption" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm">
                            <option value="ssl">SSL</option>
                            <option value="tls">TLS</option>
                            <option value="none">None</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Incoming Username</label>
                        <input type="text" name="incoming_username" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Incoming Password</label>
                        <input type="password" name="incoming_password" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm">
                    </div>
                </div>
            </div>

            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300">
                Active
            </label>

            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('createMailboxModal').classList.add('hidden')"
                        class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md">Cancel</button>
                <button type="submit" class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md">Create</button>
            </div>
        </form>
    </div>
</div>

<div id="editMailboxModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 overflow-y-auto p-6">
    <div class="bg-white rounded-md shadow-xl w-full max-w-3xl p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-semibold text-gray-900">Edit Mailbox</h3>
            <button onclick="document.getElementById('editMailboxModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="editMailboxForm" method="POST" class="space-y-4">
            <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Mailbox Name *</label>
                    <input type="text" name="name" id="editMailboxName" required class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Email *</label>
                    <input type="email" name="email" id="editMailboxEmail" required class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">From Name</label>
                    <input type="text" name="from_name" id="editMailboxFromName" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Domain</label>
                    <select name="domain_id" id="editMailboxDomain" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm">
                        <option value="">Not linked</option>
                        <?php foreach ($domains as $domain): ?>
                        <option value="<?= (int)$domain['id'] ?>"><?= htmlspecialchars($domain['domain']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1">Department Link</label>
                    <select name="department_id" id="editMailboxDepartment" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm">
                        <option value="">Not linked</option>
                        <?php foreach ($departments as $dept): ?>
                        <option value="<?= (int)$dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <h4 class="text-sm font-semibold text-gray-800 mb-3">Incoming (Receive)</h4>
                <div class="grid grid-cols-4 gap-3">
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Incoming Host</label>
                        <input type="text" name="incoming_host" id="editIncomingHost" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Port</label>
                        <input type="number" name="incoming_port" id="editIncomingPort" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Encryption</label>
                        <select name="incoming_encryption" id="editIncomingEncryption" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm">
                            <option value="ssl">SSL</option>
                            <option value="tls">TLS</option>
                            <option value="none">None</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Incoming Username</label>
                        <input type="text" name="incoming_username" id="editIncomingUsername" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Incoming Password</label>
                        <input type="password" name="incoming_password" placeholder="Leave blank to keep current password" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Active</label>
                <select name="is_active" id="editMailboxActive" class="w-full px-3 py-2 rounded-md border border-gray-300 text-sm max-w-[180px]">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="button" onclick="document.getElementById('editMailboxModal').classList.add('hidden')"
                        class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md">Cancel</button>
                <button type="submit" class="flex-1 py-2 bg-gray-900 hover:bg-black text-white text-sm font-medium rounded-md">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditMailbox(mailbox) {
    document.getElementById('editMailboxName').value = mailbox.name || '';
    document.getElementById('editMailboxEmail').value = mailbox.email || '';
    document.getElementById('editMailboxFromName').value = mailbox.from_name || '';
    document.getElementById('editMailboxDepartment').value = mailbox.department_id || '';
    document.getElementById('editMailboxDomain').value = mailbox.domain_id || '';
    document.getElementById('editIncomingHost').value = mailbox.incoming_host || '';
    document.getElementById('editIncomingPort').value = mailbox.incoming_port || '';
    document.getElementById('editIncomingEncryption').value = mailbox.incoming_encryption || 'ssl';
    document.getElementById('editIncomingUsername').value = mailbox.incoming_username || '';
    document.getElementById('editMailboxActive').value = mailbox.is_active || 0;
    document.getElementById('editMailboxForm').action = '/admin/tickets/mailboxes/' + mailbox.id;
    document.getElementById('editMailboxModal').classList.remove('hidden');
}
</script>
