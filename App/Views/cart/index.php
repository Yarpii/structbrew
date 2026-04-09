<!-- Breadcrumb -->
<div class="bg-[var(--color-bg)] border-b border-[var(--color-border)]">
    <div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-3">
        <nav class="flex items-center gap-1.5 text-sm text-[var(--color-muted)]">
            <a href="/" class="hover:text-[var(--color-accent)] transition-colors">Home</a>
            <span>/</span>
            <span class="text-[var(--color-text)] font-medium">Shopping Cart</span>
        </nav>
    </div>
</div>

<div class="mx-auto w-[92%] sm:w-[90%] md:w-[88%] lg:w-[85%] py-8"
     x-data="{
        checkoutLoading: false,
        checkoutError: '',
        useCredits: false,
        selectedPaymentMethod: <?= json_encode((string) ($defaultPaymentMethod ?? 'manual_checkout')) ?>,
        paymentMethods: <?= json_encode(array_values($paymentMethods ?? [])) ?>,
        creditsEnabled: <?= !empty($creditsEnabled) ? 'true' : 'false' ?>,
        creditsBalance: <?= json_encode((float) ($creditsBalance ?? 0.0)) ?>,
        async checkout() {
            this.checkoutError = '';
            if ($store.cart.items.length === 0) {
                this.checkoutError = 'Your cart is empty.';
                return;
            }

            if (!this.useCredits && !this.selectedPaymentMethod) {
                this.checkoutError = 'Please select a payment method.';
                return;
            }

            const orderTotal = $store.cart.total + ($store.cart.total >= 50 ? 0 : 4.99);
            if (this.useCredits && orderTotal > this.creditsBalance + 0.00001) {
                this.checkoutError = 'Insufficient credits balance for this order.';
                return;
            }

            this.checkoutLoading = true;
            try {
                const response = await fetch('/cart/checkout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        _csrf_token: '<?= htmlspecialchars((string) ($csrfToken ?? \App\Core\Session::csrfToken())) ?>',
                        items: $store.cart.items,
                        use_credits: this.useCredits,
                        payment_method: this.selectedPaymentMethod
                    })
                });

                const data = await response.json();
                if (!response.ok || !data.success) {
                    this.checkoutError = data.message || 'Checkout failed. Please try again.';
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                    return;
                }

                $store.cart.clear();
                window.location.href = data.redirect || '/account/orders';
            } catch (e) {
                this.checkoutError = 'Could not reach checkout service. Please try again.';
            } finally {
                this.checkoutLoading = false;
            }
        }
     }">
    <h1 class="text-2xl font-bold text-[var(--color-text)] mb-6">Shopping Cart</h1>

    <?php if (!empty($ads['cart_page'])): ?>
        <div class="mb-6">
            <?php $ad = $ads['cart_page']; include dirname(__DIR__) . '/partials/_managed-ad.php'; ?>
        </div>
    <?php endif; ?>

    <div x-show="checkoutError" x-cloak class="mb-4 rounded-md border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-700" x-text="checkoutError"></div>

    <!-- Empty cart -->
    <template x-if="$store.cart.items.length === 0">
        <div class="text-center py-16 rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)]" style="box-shadow: var(--shadow-sm)">
            <svg class="mx-auto mb-4 text-[var(--color-muted)]" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            <h2 class="text-xl font-bold text-[var(--color-text)]">Your cart is empty</h2>
            <p class="mt-2 text-sm text-[var(--color-muted)]">Looks like you haven't added any products yet.</p>
            <a href="/shop" class="mt-5 inline-flex h-11 items-center gap-2 rounded-[var(--radius-button)] bg-[var(--color-accent)] px-6 text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)]">
                Browse Products
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </a>
        </div>
    </template>

    <!-- Cart with items -->
    <template x-if="$store.cart.items.length > 0">
        <div class="grid lg:grid-cols-[1fr_340px] gap-6">
            <!-- Cart Items -->
            <div class="rounded-[var(--radius-card)] border border-[var(--color-border)] bg-[var(--color-surface)] overflow-hidden" style="box-shadow: var(--shadow-sm)">
                <div class="p-4 border-b border-[var(--color-border)] flex items-center justify-between">
                    <span class="text-sm font-semibold text-[var(--color-text)]" x-text="$store.cart.count + ' item' + ($store.cart.count !== 1 ? 's' : '')"></span>
                    <button @click="if(confirm('Clear all items?')) $store.cart.clear()" class="text-xs text-[var(--color-muted)] hover:text-[var(--color-accent)] transition-colors">Clear all</button>
                </div>
                <template x-for="item in $store.cart.items" :key="item.id">
                    <div class="flex items-center gap-4 p-4 border-b border-[var(--color-border)] last:border-b-0">
                        <!-- Product image placeholder -->
                        <a :href="'/shop/' + item.slug" class="shrink-0 w-20 h-20 rounded-lg bg-[var(--color-bg)] border border-[var(--color-border)] flex items-center justify-center">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="var(--color-accent)" stroke-width="1.5" opacity=".5"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                        </a>
                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <a :href="'/shop/' + item.slug" class="text-sm font-semibold text-[var(--color-text)] hover:text-[var(--color-accent)] transition-colors truncate block" x-text="item.name"></a>
                            <div class="mt-1 flex items-baseline gap-1.5">
                                <template x-if="item.sale_price">
                                    <span class="text-sm font-bold text-[var(--color-accent)]" x-text="'$' + item.sale_price.toFixed(2)"></span>
                                </template>
                                <template x-if="item.sale_price">
                                    <span class="text-xs text-[var(--color-muted)] line-through" x-text="'$' + item.price.toFixed(2)"></span>
                                </template>
                                <template x-if="!item.sale_price">
                                    <span class="text-sm font-bold text-[var,--color-text)]" x-text="'$' + item.price.toFixed(2)"></span>
                                </template>
                            </div>
                        </div>
                        <!-- Qty -->
                        <div class="flex items-center rounded-[var(--radius-button)] border border-[var(--color-border)] bg-[var(--color-bg)]">
                            <button @click="$store.cart.update(item.id, item.qty - 1)" class="h-9 w-9 flex items-center justify-center text-[var(--color-text)] hover:text-[var(--color-accent)]">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            </button>
                            <span class="w-8 text-center text-sm font-semibold text-[var,--color-text)]" x-text="item.qty"></span>
                            <button @click="$store.cart.update(item.id, item.qty + 1)" class="h-9 w-9 flex items-center justify-center text-[var(--color-text)] hover:text-[var,--color-accent)]">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            </button>
                        </div>
                        <!-- Subtotal + Remove -->
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold text-[var,--color-text)]" x-text="'$' + ((item.sale_price || item.price) * item.qty).toFixed(2)"></p>
                            <button @click="$store.cart.remove(item.id)" class="mt-1 text-xs text-[var(--color-muted)] hover:text-[var,--color-accent] transition-colors">Remove</button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Order Summary -->
            <div class="lg:sticky lg:top-24 self-start">
                <div class="rounded-md border border-[var(--color-border)] bg-[var(--color-surface)] p-5" style="box-shadow: var(--shadow-sm)">
                    <h2 class="text-lg font-bold text-[var,--color-text)] mb-4">Order Summary</h2>
                    <div class="space-y-2.5 text-sm">
                        <div class="flex justify-between">
                            <span class="text-[var(--color-muted)]">Subtotal</span>
                            <span class="font-semibold text-[var,--color-text)]" x-text="'$' + $store.cart.total.toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-[var(--color-muted)]">Shipping</span>
                            <span class="font-semibold text-emerald-600" x-text="$store.cart.total >= 50 ? 'Free' : '$4.99'"></span>
                        </div>
                        <div class="border-t border-[var(--color-border)] pt-2.5 flex justify-between">
                            <span class="font-bold text-[var,--color-text)]">Total</span>
                            <span class="text-lg font-extrabold text-[var,--color-text)]" x-text="'$' + ($store.cart.total + ($store.cart.total >= 50 ? 0 : 4.99)).toFixed(2)"></span>
                        </div>
                    </div>

                    <?php if (!empty($isLoggedIn) && !empty($creditsEnabled)): ?>
                        <div class="mt-4 rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] p-3">
                            <label class="flex items-start gap-2 text-sm text-[var,--color-text)]">
                                <input type="checkbox"
                                       class="mt-0.5 h-4 w-4 rounded border-[var(--color-border)]"
                                       x-model="useCredits"
                                       :disabled="($store.cart.total + ($store.cart.total >= 50 ? 0 : 4.99)) > creditsBalance + 0.00001">
                                <span>
                                    Pay with account credits
                                    <span class="block text-xs text-[var(--color-muted)]">Balance: $<?= number_format((float) ($creditsBalance ?? 0), 2) ?></span>
                                </span>
                            </label>
                            <p x-show="($store.cart.total + ($store.cart.total >= 50 ? 0 : 4.99)) > creditsBalance + 0.00001" x-cloak class="mt-2 text-xs text-amber-600">
                                Your credits are not enough for this order.
                            </p>
                        </div>
                    <?php endif; ?>

                    <div class="mt-4 rounded-md border border-[var(--color-border)] bg-[var(--color-bg)] p-3" x-show="!useCredits" x-cloak>
                        <p class="text-sm font-semibold text-[var,--color-text)] mb-2">Payment Method</p>
                        <template x-if="paymentMethods.length === 0">
                            <p class="text-xs text-[var(--color-muted)]">No payment methods are enabled.</p>
                        </template>
                        <div class="space-y-2" x-show="paymentMethods.length > 0">
                            <template x-for="method in paymentMethods" :key="method.code">
                                <label class="flex items-center gap-2 text-sm text-[var,--color-text)]">
                                    <input type="radio" name="payment_method" :value="method.code" x-model="selectedPaymentMethod"
                                           class="h-4 w-4 border-[var(--color-border)]">
                                    <span x-text="method.label"></span>
                                </label>
                            </template>
                        </div>
                    </div>

                    <template x-if="$store.cart.total < 50">
                        <p class="mt-3 text-xs text-[var(--color-muted)] bg-[var(--color-bg)] rounded-md p-2.5 border border-[var(--color-border)]">
                            Add <span class="font-semibold text-[var(--color-accent)]" x-text="'$' + (50 - $store.cart.total).toFixed(2)"></span> more for free shipping!
                        </p>
                    </template>

                    <button class="mt-4 w-full h-11 rounded-md bg-[var(--color-accent)] text-sm font-semibold text-white transition hover:bg-[var(--color-accent-hover)] disabled:opacity-70 disabled:cursor-not-allowed"
                            :disabled="checkoutLoading"
                            @click="checkout">
                        <span x-show="!checkoutLoading">Proceed to Checkout</span>
                        <span x-show="checkoutLoading" x-cloak>Processing...</span>
                    </button>
                    <a href="/shop" class="mt-2 block text-center text-sm text-[var(--color-accent)] hover:underline">Continue Shopping</a>
                </div>
            </div>
        </div>
    </template>
</div>
