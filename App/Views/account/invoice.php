<?php
$formatMoney = static fn (float $amount): string => \App\Core\StoreResolver::formatPrice($amount);
$formatDateTime = static fn (string $value): string => date('d M Y H:i', strtotime($value));
$addressLines = static function (array $address): array {
    $lines = [];
    $name = trim((string) (($address['first_name'] ?? '') . ' ' . ($address['last_name'] ?? '')));
    if ($name !== '') {
        $lines[] = $name;
    }
    if (!empty($address['company'])) {
        $lines[] = (string) $address['company'];
    }
    if (!empty($address['street_1'])) {
        $lines[] = (string) $address['street_1'];
    }
    if (!empty($address['street_2'])) {
        $lines[] = (string) $address['street_2'];
    }
    $cityLine = trim((string) (($address['postcode'] ?? '') . ' ' . ($address['city'] ?? '')));
    if ($cityLine !== '') {
        $lines[] = $cityLine;
    }
    if (!empty($address['state'])) {
        $lines[] = (string) $address['state'];
    }
    if (!empty($address['country_code'])) {
        $lines[] = (string) $address['country_code'];
    }
    return $lines;
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?= htmlspecialchars((string) $order['order_number']) ?></title>
    <style>
        body { font-family: Inter, Arial, sans-serif; color: #1a1a2e; background: #f8f9fb; margin: 0; padding: 32px; }
        .sheet { max-width: 960px; margin: 0 auto; background: #fff; border: 1px solid #dfe2e8; border-radius: 16px; overflow: hidden; }
        .header { padding: 32px; background: linear-gradient(135deg, #1a1a2e, #2a2a44); color: #fff; }
        .header h1 { margin: 0 0 8px; font-size: 28px; }
        .header p { margin: 4px 0; opacity: .85; }
        .content { padding: 32px; }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; margin-bottom: 24px; }
        .card { border: 1px solid #dfe2e8; border-radius: 14px; padding: 20px; background: #fff; }
        .card h2 { margin: 0 0 12px; font-size: 16px; }
        .muted { color: #5f6578; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 14px 12px; border-bottom: 1px solid #dfe2e8; text-align: left; font-size: 14px; }
        th { background: #f8f9fb; color: #5f6578; }
        .text-right { text-align: right; }
        .totals { width: 320px; margin-left: auto; margin-top: 20px; }
        .totals-row { display: flex; justify-content: space-between; padding: 8px 0; color: #5f6578; }
        .totals-row.total { color: #1a1a2e; font-size: 18px; font-weight: 700; border-top: 1px solid #dfe2e8; margin-top: 6px; padding-top: 14px; }
        @media print { body { background: #fff; padding: 0; } .sheet { border: 0; border-radius: 0; } }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="header">
            <h1>Invoice</h1>
            <p>Order #<?= htmlspecialchars((string) $order['order_number']) ?></p>
            <p>Created <?= htmlspecialchars($formatDateTime((string) $order['created_at'])) ?></p>
        </div>
        <div class="content">
            <div class="grid">
                <div class="card">
                    <h2>Billing address</h2>
                    <?php foreach ($addressLines($billing) as $line): ?>
                        <div class="muted"><?= htmlspecialchars($line) ?></div>
                    <?php endforeach; ?>
                </div>
                <div class="card">
                    <h2>Shipping address</h2>
                    <?php foreach ($addressLines($shipping) as $line): ?>
                        <div class="muted"><?= htmlspecialchars($line) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="card">
                <h2>Order items</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Line total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars((string) $item['name']) ?></td>
                                <td><?= htmlspecialchars((string) ($item['sku'] ?? '—')) ?></td>
                                <td class="text-right"><?= (int) ($item['qty'] ?? 0) ?></td>
                                <td class="text-right"><?= htmlspecialchars($formatMoney((float) ($item['row_total'] ?? 0))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="totals">
                    <div class="totals-row"><span>Subtotal</span><span><?= htmlspecialchars($formatMoney((float) $order['subtotal'])) ?></span></div>
                    <div class="totals-row"><span>Shipping</span><span><?= htmlspecialchars($formatMoney((float) $order['shipping_amount'])) ?></span></div>
                    <div class="totals-row"><span>Tax</span><span><?= htmlspecialchars($formatMoney((float) $order['tax_amount'])) ?></span></div>
                    <?php if ((float) ($order['discount_amount'] ?? 0) > 0): ?>
                        <div class="totals-row"><span>Discount</span><span>-<?= htmlspecialchars($formatMoney((float) $order['discount_amount'])) ?></span></div>
                    <?php endif; ?>
                    <div class="totals-row total"><span>Total</span><span><?= htmlspecialchars($formatMoney((float) $order['grand_total'])) ?></span></div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
