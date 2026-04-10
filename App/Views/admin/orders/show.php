<div class="mb-6">
    <a href="/admin/orders" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back to Orders
    </a>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <!-- Order Details -->
    <div class="xl:col-span-2 space-y-6">
        <!-- Order Items -->
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Order Items</h3>
            </div>
            <div class="divide-y divide-gray-100">
                <?php foreach ($order['items'] ?? [] as $item): ?>
                <div class="px-6 py-4 flex items-center gap-4">
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex-shrink-0"></div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($item['name']) ?></p>
                        <p class="text-xs text-gray-500">SKU: <?= htmlspecialchars($item['sku']) ?></p>
                    </div>
                    <div class="text-sm text-gray-500">x<?= (int) ($item['qty'] ?? 0) ?></div>
                    <div class="text-sm font-medium text-right w-24">
                        <?= htmlspecialchars($order['currency_code'] ?? '') ?> <?= number_format((float)$item['row_total'], 2) ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="px-6 py-4 bg-gray-50 space-y-2 border-t border-gray-200">
                <div class="flex justify-between text-sm"><span class="text-gray-500">Subtotal</span><span><?= htmlspecialchars($order['currency_code'] ?? '') ?> <?= number_format((float)$order['subtotal'], 2) ?></span></div>
                <div class="flex justify-between text-sm"><span class="text-gray-500">Shipping</span><span><?= htmlspecialchars($order['currency_code'] ?? '') ?> <?= number_format((float)$order['shipping_amount'], 2) ?></span></div>
                <div class="flex justify-between text-sm"><span class="text-gray-500">Tax</span><span><?= htmlspecialchars($order['currency_code'] ?? '') ?> <?= number_format((float)$order['tax_amount'], 2) ?></span></div>
                <?php if ((float)($order['discount_amount'] ?? 0) > 0): ?>
                <div class="flex justify-between text-sm"><span class="text-gray-500">Discount</span><span class="text-red-600">-<?= htmlspecialchars($order['currency_code'] ?? '') ?> <?= number_format((float)$order['discount_amount'], 2) ?></span></div>
                <?php endif; ?>
                <div class="flex justify-between text-base font-semibold pt-2 border-t border-gray-200">
                    <span>Grand Total</span>
                    <span><?= htmlspecialchars($order['currency_code'] ?? '') ?> <?= number_format((float)$order['grand_total'], 2) ?></span>
                </div>
            </div>
        </div>

        <!-- Addresses -->
        <div class="grid grid-cols-2 gap-6">
            <?php
            $billing = is_string($order['billing_address'] ?? '') ? json_decode($order['billing_address'], true) : ($order['billing_address'] ?? []);
            $shipping = is_string($order['shipping_address'] ?? '') ? json_decode($order['shipping_address'], true) : ($order['shipping_address'] ?? []);
            ?>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-3">Billing Address</h3>
                <div class="text-sm text-gray-600 space-y-1">
                    <p class="font-medium text-gray-900"><?= htmlspecialchars(($billing['first_name'] ?? '') . ' ' . ($billing['last_name'] ?? '')) ?></p>
                    <?php if (!empty($billing['company'])): ?><p><?= htmlspecialchars($billing['company']) ?></p><?php endif; ?>
                    <p><?= htmlspecialchars($billing['street_1'] ?? '') ?></p>
                    <?php if (!empty($billing['street_2'])): ?><p><?= htmlspecialchars($billing['street_2']) ?></p><?php endif; ?>
                    <p><?= htmlspecialchars(($billing['postcode'] ?? '') . ' ' . ($billing['city'] ?? '')) ?></p>
                    <p><?= htmlspecialchars($billing['country_code'] ?? '') ?></p>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-800 mb-3">Shipping Address</h3>
                <div class="text-sm text-gray-600 space-y-1">
                    <p class="font-medium text-gray-900"><?= htmlspecialchars(($shipping['first_name'] ?? '') . ' ' . ($shipping['last_name'] ?? '')) ?></p>
                    <?php if (!empty($shipping['company'])): ?><p><?= htmlspecialchars($shipping['company']) ?></p><?php endif; ?>
                    <p><?= htmlspecialchars($shipping['street_1'] ?? '') ?></p>
                    <?php if (!empty($shipping['street_2'])): ?><p><?= htmlspecialchars($shipping['street_2']) ?></p><?php endif; ?>
                    <p><?= htmlspecialchars(($shipping['postcode'] ?? '') . ' ' . ($shipping['city'] ?? '')) ?></p>
                    <p><?= htmlspecialchars($shipping['country_code'] ?? '') ?></p>
                </div>
            </div>
        </div>

        <!-- Status History -->
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-800">Status History</h3>
            </div>
            <div class="p-6 space-y-4">
                <?php foreach ($statusHistory ?? [] as $history): ?>
                <div class="flex gap-4">
                    <div class="w-2 h-2 rounded-full bg-blue-500 mt-2 flex-shrink-0"></div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">
                            Status changed to <span class="font-semibold"><?= htmlspecialchars(ucfirst((string) ($history['status'] ?? 'unknown'))) ?></span>
                        </p>
                        <?php if (!empty($history['comment'])): ?>
                        <p class="text-sm text-gray-500 mt-0.5"><?= htmlspecialchars($history['comment']) ?></p>
                        <?php endif; ?>
                        <p class="text-xs text-gray-400 mt-1"><?= htmlspecialchars((string) ($history['created_at'] ?? '')) ?> by <?= htmlspecialchars($history['created_by'] ?? 'system') ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Order Info -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Status</span>
                    <span class="inline-block px-2.5 py-1 text-xs font-medium rounded-full
                        <?= match($order['status']) {
                            'pending' => 'bg-yellow-100 text-yellow-700',
                            'processing' => 'bg-blue-100 text-blue-700',
                            'shipped' => 'bg-purple-100 text-purple-700',
                            'delivered' => 'bg-green-100 text-green-700',
                            'cancelled' => 'bg-red-100 text-red-700',
                            default => 'bg-gray-100 text-gray-700',
                        } ?>">
                        <?= htmlspecialchars(ucfirst((string) ($order['status'] ?? 'unknown'))) ?>
                    </span>
                </div>
                <div class="flex justify-between"><span class="text-sm text-gray-500">Order Date</span><span class="text-sm text-gray-900"><?= htmlspecialchars((string) ($order['created_at'] ?? '')) ?></span></div>
                <div class="flex justify-between"><span class="text-sm text-gray-500">Payment</span><span class="text-sm text-gray-900"><?= htmlspecialchars($order['payment_method_label'] ?? $order['payment_method'] ?? '—') ?></span></div>
                <div class="flex justify-between"><span class="text-sm text-gray-500">Shipping</span><span class="text-sm text-gray-900"><?= htmlspecialchars($order['shipping_method'] ?? '—') ?></span></div>
                <?php if (!empty($order['coupon_code'])): ?>
                <div class="flex justify-between"><span class="text-sm text-gray-500">Coupon</span><span class="text-sm text-gray-900 font-mono"><?= htmlspecialchars($order['coupon_code']) ?></span></div>
                <?php endif; ?>
                <div class="flex justify-between"><span class="text-sm text-gray-500">IP</span><span class="text-sm text-gray-400"><?= htmlspecialchars($order['ip_address'] ?? '') ?></span></div>
            </div>
        </div>

        <?php if (!empty($order['payment_instruction'])): ?>
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-800 mb-3">Payment Instructions</h3>
            <p class="text-sm text-gray-600 whitespace-pre-line"><?= htmlspecialchars((string) $order['payment_instruction']) ?></p>
        </div>
        <?php endif; ?>

        <!-- Customer Info -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-800 mb-3">Customer</h3>
            <p class="text-sm text-gray-900"><?= htmlspecialchars($order['customer_email']) ?></p>
            <?php if (!empty($order['customer_id'])): ?>
            <a href="/admin/customers/<?= (int) $order['customer_id'] ?>" class="text-xs text-blue-600 hover:text-blue-700 mt-1 inline-block">View customer profile</a>
            <?php else: ?>
            <p class="text-xs text-gray-400 mt-1">Guest order</p>
            <?php endif; ?>
        </div>

        <!-- Update Status -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-800 mb-3">Update Status</h3>
            <form method="POST" action="/admin/orders/<?= (int) $order['id'] ?>/status" class="space-y-3">
                <input type="hidden" name="_csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
                <select name="status" class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm">
                    <?php foreach (['pending','processing','shipped','delivered','cancelled','refunded'] as $s): ?>
                    <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                    <?php endforeach; ?>
                </select>
                <textarea name="comment" rows="2" placeholder="Add a comment (optional)"
                          class="w-full px-3 py-2 rounded-lg border border-gray-300 text-sm"></textarea>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="notify_customer" value="1" class="rounded border-gray-300 text-blue-600">
                    <span class="text-sm text-gray-600">Notify customer</span>
                </label>
                <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Update Status
                </button>
            </form>
        </div>
    </div>
</div>
