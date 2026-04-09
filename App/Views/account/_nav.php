<?php
$activeAccountTab = $activeAccountTab ?? 'overview';
$tabs = [
    'overview'  => ['label' => 'Overview',   'href' => '/account'],
    'profile'   => ['label' => 'Profile',    'href' => '/account/profile'],
    'orders'    => ['label' => 'Orders',     'href' => '/account/orders'],
    'addresses' => ['label' => 'Addresses',  'href' => '/account/addresses'],
    'garage'    => ['label' => 'Garage',     'href' => '/account/garage'],
    'support'   => ['label' => 'Support',    'href' => '/account/tickets'],
];
?>
<div class="rounded-lg border border-[var(--color-border)] bg-[var(--color-surface)] p-2">
    <div class="flex flex-wrap gap-2">
        <?php foreach ($tabs as $key => $tab): ?>
            <a href="<?= htmlspecialchars((string) $tab['href']) ?>" class="inline-flex h-10 items-center justify-center rounded-md px-4 text-sm font-semibold transition <?= $activeAccountTab === $key ? 'border border-[var(--color-border)] bg-[var(--color-bg)] text-[var(--color-text)]' : 'border border-transparent text-[var(--color-muted)] hover:border-[var(--color-border)] hover:text-[var(--color-text)]' ?>">
                <?= htmlspecialchars((string) $tab['label']) ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>
