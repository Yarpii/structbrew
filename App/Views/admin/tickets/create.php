<?php
$types     = ['general','order_support','product_inquiry','technical','billing','shipping','returns','partnership','advertising'];
$priorities = ['low','normal','high','critical','urgent'];
$sources   = ['web','email','phone','api','chat','admin'];
?>

<div class="flex items-center gap-3 mb-6">
    <a href="/admin/tickets" class="text-gray-400 hover:text-gray-600">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <p class="text-sm text-gray-500">Create a new support ticket manually</p>
</div>

<?php if (!empty($flashError)): ?>
<div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm"><?= htmlspecialchars($flashError) ?></div>
<?php endif; ?>

<div class="max-w-3xl">
    <form method="POST" action="/admin/tickets" class="space-y-5">
        <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">

        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Ticket Details</h2>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Subject <span class="text-red-500">*</span></label>
                <input type="text" name="subject" placeholder="Brief description of the issue" required
                       class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Initial Message</label>
                <textarea name="body" rows="5" placeholder="Describe the issue in detail..."
                          class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm focus:ring-2 focus:ring-blue-500 resize-y"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Priority</label>
                    <select name="priority" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <?php foreach ($priorities as $p): ?>
                        <option value="<?= $p ?>" <?= $p === 'normal' ? 'selected' : '' ?>><?= ucfirst($p) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Type</label>
                    <select name="type" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <?php foreach ($types as $tp): ?>
                        <option value="<?= $tp ?>"><?= ucwords(str_replace('_', ' ', $tp)) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Source</label>
                    <select name="source" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <?php foreach ($sources as $src): ?>
                        <option value="<?= $src ?>" <?= $src === 'admin' ? 'selected' : '' ?>><?= ucfirst($src) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Department</label>
                    <select name="department_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <option value="">None</option>
                        <?php foreach ($departments as $d): ?>
                        <option value="<?= (int) $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Category</label>
                    <select name="category_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                        <option value="">None</option>
                        <?php foreach ($categories as $c): ?>
                        <option value="<?= (int) $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Assign to Agent</label>
                <select name="assigned_agent_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                    <option value="">Unassigned</option>
                    <?php foreach ($agents as $a): ?>
                    <option value="<?= (int) $a['id'] ?>"><?= htmlspecialchars(trim($a['first_name'] . ' ' . $a['last_name'])) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6 space-y-4" x-data="{ reqType: 'customer' }">
            <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Requester</h2>

            <div class="flex gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="requester_type" value="customer" x-model="reqType" class="accent-blue-600"> <span class="text-sm">Existing Customer</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="requester_type" value="guest" x-model="reqType" class="accent-blue-600"> <span class="text-sm">Guest / Manual</span>
                </label>
            </div>

            <div x-show="reqType === 'customer'">
                <label class="block text-xs font-medium text-gray-600 mb-1">Customer</label>
                <select name="customer_id" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                    <option value="">Select customer...</option>
                    <?php foreach ($customers as $cust): ?>
                    <option value="<?= (int) $cust['id'] ?>"><?= htmlspecialchars($cust['email']) ?> — <?= htmlspecialchars(($cust['first_name'] ?? '') . ' ' . ($cust['last_name'] ?? '')) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div x-show="reqType === 'guest'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Guest Name</label>
                    <input type="text" name="guest_name" placeholder="Full name"
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Guest Email</label>
                    <input type="email" name="guest_email" placeholder="email@example.com"
                           class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-colors">
                Create Ticket
            </button>
            <a href="/admin/tickets" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>
