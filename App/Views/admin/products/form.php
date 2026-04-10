<?php $__dir = __DIR__ . '/partials/'; ?>
<form method="POST" action="<?= $formAction ?? '/admin/products' ?>" enctype="multipart/form-data">
    <input type="hidden" name="_csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">

    <?php include $__dir . '_action_bar.php'; ?>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 p-5">

        <div class="xl:col-span-2 space-y-4">
            <?php include $__dir . '_general.php'; ?>
            <?php include $__dir . '_content_pricing.php'; ?>
            <?php include $__dir . '_attributes.php'; ?>
            <?php include $__dir . '_images.php'; ?>
        </div>

        <div class="space-y-4">
            <?php include $__dir . '_sidebar.php'; ?>
        </div>

    </div>
</form>