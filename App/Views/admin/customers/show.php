<div class="mb-6">
    <a href="/admin/customers" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Customers
    </a>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="xl:col-span-2 space-y-6">
        <!-- Orders -->
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="font-semibold text-gray-800">Orders</h3>
                <span class="text-sm text-gray-500"><?= $customer['order_count'] ?? 0 ?> total</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Order</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="text-center px-6 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3">
                                    <a href="/admin/orders/<?= $order['id'] ?>" class="font-medium text-blue-600 hover:text-blue-700">
                                        #<?= htmlspecialchars($order['order_number']) ?>
                                    </a>
                                </td>
                                <td class="px-6 py-3 text-gray-500 text-xs"><?= $order['created_at'] ?? '' ?></td>
                                <td class="px-6 py-3 text-center">
                                    <span class="inline-block px-2.5 py-1 text-xs font-medium rounded-full
                                        <?= match($order['status'] ?? '') {
                                            'pending' => 'bg-yellow-100 text-yellow-700',
                                            'processing' => 'bg-blue-100 text-blue-700',
                                            'shipped' => 'bg-purple-100 text-purple-700',
                                            'delivered' => 'bg-green-100 text-green-700',
                                            'cancelled' => 'bg-red-100 text-red-700',
                                            'refunded' => 'bg-gray-100 text-gray-700',
                                            default => 'bg-gray-100 text-gray-700',
                                        } ?>">
                                        <?= ucfirst($order['status'] ?? 'unknown') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right font-medium text-gray-900">
                                    <?= htmlspecialchars($order['currency_code'] ?? '') ?> <?= number_format((float)($order['grand_total'] ?? 0), 2) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">No orders yet</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Addresses -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Billing Addresses -->
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800">Billing Addresses</h3>
                </div>
                <div class="p-6 space-y-4">
                    <?php if (!empty($billingAddresses)): ?>
                        <?php foreach ($billingAddresses as $addr): ?>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <?php if (!empty($addr['is_default'])): ?>
                            <span class="inline-block px-1.5 py-0.5 bg-blue-100 text-blue-700 text-[10px] rounded font-medium mb-2">Default</span>
                            <?php endif; ?>
                            <div class="text-sm text-gray-600 space-y-0.5">
                                <p class="font-medium text-gray-900"><?= htmlspecialchars(($addr['first_name'] ?? '') . ' ' . ($addr['last_name'] ?? '')) ?></p>
                                <?php if (!empty($addr['company'])): ?><p><?= htmlspecialchars($addr['company']) ?></p><?php endif; ?>
                                <p><?= htmlspecialchars($addr['street_1'] ?? '') ?></p>
                                <?php if (!empty($addr['street_2'])): ?><p><?= htmlspecialchars($addr['street_2']) ?></p><?php endif; ?>
                                <p><?= htmlspecialchars(($addr['postcode'] ?? '') . ' ' . ($addr['city'] ?? '')) ?></p>
                                <p><?= htmlspecialchars($addr['country_code'] ?? '') ?></p>
                                <?php if (!empty($addr['phone'])): ?><p class="text-gray-400"><?= htmlspecialchars($addr['phone']) ?></p><?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-sm text-gray-400">No billing addresses</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Shipping Addresses -->
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800">Shipping Addresses</h3>
                </div>
                <div class="p-6 space-y-4">
                    <?php if (!empty($shippingAddresses)): ?>
                        <?php foreach ($shippingAddresses as $addr): ?>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <?php if (!empty($addr['is_default'])): ?>
                            <span class="inline-block px-1.5 py-0.5 bg-blue-100 text-blue-700 text-[10px] rounded font-medium mb-2">Default</span>
                            <?php endif; ?>
                            <div class="text-sm text-gray-600 space-y-0.5">
                                <p class="font-medium text-gray-900"><?= htmlspecialchars(($addr['first_name'] ?? '') . ' ' . ($addr['last_name'] ?? '')) ?></p>
                                <?php if (!empty($addr['company'])): ?><p><?= htmlspecialchars($addr['company']) ?></p><?php endif; ?>
                                <p><?= htmlspecialchars($addr['street_1'] ?? '') ?></p>
                                <?php if (!empty($addr['street_2'])): ?><p><?= htmlspecialchars($addr['street_2']) ?></p><?php endif; ?>
                                <p><?= htmlspecialchars(($addr['postcode'] ?? '') . ' ' . ($addr['city'] ?? '')) ?></p>
                                <p><?= htmlspecialchars($addr['country_code'] ?? '') ?></p>
                                <?php if (!empty($addr['phone'])): ?><p class="text-gray-400"><?= htmlspecialchars($addr['phone']) ?></p><?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-sm text-gray-400">No shipping addresses</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Customer Profile -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center text-lg font-semibold text-blue-600">
                    <?= strtoupper(substr($customer['first_name'] ?? '', 0, 1) . substr($customer['last_name'] ?? '', 0, 1)) ?>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900"><?= htmlspecialchars(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? '')) ?></h3>
                    <p class="text-sm text-gray-500"><?= htmlspecialchars($customer['email'] ?? '') ?></p>
                </div>
            </div>
            <div class="space-y-3 pt-4 border-t border-gray-200">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Status</span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full <?= ($customer['is_active'] ?? 1) ? 'bg-green-500' : 'bg-gray-300' ?>"></span>
                        <span class="text-sm text-gray-900"><?= ($customer['is_active'] ?? 1) ? 'Active' : 'Inactive' ?></span>
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Group</span>
                    <span class="text-sm text-gray-900"><?= htmlspecialchars((string) ($customer['customer_group_label'] ?? 'Normal Client')) ?></span>
                </div>
                <?php if (!empty($customer['phone'])): ?>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Phone</span>
                    <span class="text-sm text-gray-900"><?= htmlspecialchars($customer['phone']) ?></span>
                </div>
                <?php endif; ?>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Store</span>
                    <span class="text-sm text-gray-900"><?= htmlspecialchars($customer['store_name'] ?? '—') ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Registered</span>
                    <span class="text-sm text-gray-900"><?= $customer['created_at'] ?? '—' ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Total Orders</span>
                    <span class="text-sm font-medium text-gray-900"><?= $customer['order_count'] ?? 0 ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Total Spent</span>
                    <span class="text-sm font-medium text-gray-900"><?= $customer['total_spent'] ?? '0.00' ?></span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-800 mb-4">Actions</h3>
            <div class="space-y-3">
                <a href="/admin/customers/<?= $customer['id'] ?>/edit"
                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Customer
                </a>
                <?php if ($customer['is_active'] ?? 1): ?>
                <form method="POST" action="/admin/customers/<?= $customer['id'] ?>/deactivate" onsubmit="return confirm('Deactivate this customer account?')">
                    <input type="hidden" name="_csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        Deactivate Account
                    </button>
                </form>
                <?php else: ?>
                <form method="POST" action="/admin/customers/<?= $customer['id'] ?>/activate">
                    <input type="hidden" name="_csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-green-50 hover:bg-green-100 text-green-600 text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Activate Account
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
