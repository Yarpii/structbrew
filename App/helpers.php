<?php

/**
 * Global translation helper — no namespace so __() is always in global scope.
 *
 * Usage in views:
 *   <?= __('add_to_cart') ?>
 *   <?= __('showing_results', ['from' => 1, 'to' => 24, 'total' => 100]) ?>
 *   <?= __('shop.add_to_cart') ?>
 */
function __(string $key, array $replace = []): string
{
    return \App\Core\Translator::get($key, $replace);
}
