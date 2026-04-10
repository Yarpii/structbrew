<?php
/**
 * Landing page — assembled from partials.
 *
 * Available data: $featured, $trending, $newArrivals, $onSale, $categories,
 *                 $engineParts, $exhaustParts, $brakesParts, $performanceParts, $wheelsParts, $title
 */
$dir = __DIR__ . '/partials';
?>

<?php include $dir . '/_hero.php'; ?>
<?php include $dir . '/_categories.php'; ?>

<?php /* ── Row 1 ── */ include $dir . '/_featured.php'; ?>
<?php /* ── Row 2 ── */ include $dir . '/_on-sale.php'; ?>

<?php include $dir . '/_ad-find-setup.php'; ?>

<?php /* ── Row 3 ── */ include $dir . '/_trending.php'; ?>

<?php include $dir . '/_ad-deals.php'; ?>

<?php /* ── Row 4 ── */ include $dir . '/_new-arrivals.php'; ?>
<?php /* ── Row 5 ── */
$rowTitle   = 'Engine Components';
$rowLink    = '/shop?category=engine-components';
$rowBg      = 'surface';
$rowBadge   = '';
$rowProducts = $engineParts;
include $dir . '/_product-row.php';
?>
<?php /* ── Row 6 ── */
$rowTitle   = 'Exhaust Systems';
$rowLink    = '/shop?category=exhaust-systems';
$rowBg      = 'bg';
$rowBadge   = '';
$rowProducts = $exhaustParts;
include $dir . '/_product-row.php';
?>

<?php include $dir . '/_ad-newsletter.php'; ?>

<?php /* ── Row 7 ── */
$rowTitle   = 'Braking Systems';
$rowLink    = '/shop?category=braking-systems';
$rowBg      = 'surface';
$rowBadge   = '';
$rowProducts = $brakesParts;
include $dir . '/_product-row.php';
?>
<?php /* ── Row 8 ── */
$rowTitle   = 'Performance & Tuning';
$rowLink    = '/shop?category=performance-tuning';
$rowBg      = 'bg';
$rowBadge   = 'Popular';
$rowProducts = $performanceParts;
include $dir . '/_product-row.php';
?>

<?php include $dir . '/_how-it-works.php'; ?>

<?php /* ── Row 9 ── */
$rowTitle   = 'Wheels, Tires & Hubs';
$rowLink    = '/shop?category=wheels-tires-hubs';
$rowBg      = 'surface';
$rowBadge   = '';
$rowProducts = $wheelsParts;
include $dir . '/_product-row.php';
?>

<?php include $dir . '/_testimonials.php'; ?>
<?php include $dir . '/_brand-marquee.php'; ?>
<?php include $dir . '/_bottom-cta.php'; ?>
