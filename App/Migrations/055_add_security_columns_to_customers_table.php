<?php

return [
    'up' => function ($db) {
        $db->statement("ALTER TABLE customers ADD COLUMN two_factor_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER email_verified_at");
        $db->statement("ALTER TABLE customers ADD COLUMN two_factor_secret VARCHAR(64) NULL AFTER two_factor_enabled");
        $db->statement("ALTER TABLE customers ADD COLUMN two_factor_confirmed_at TIMESTAMP NULL AFTER two_factor_secret");
    },
    'down' => function ($db) {
        $db->statement("ALTER TABLE customers DROP COLUMN two_factor_confirmed_at");
        $db->statement("ALTER TABLE customers DROP COLUMN two_factor_secret");
        $db->statement("ALTER TABLE customers DROP COLUMN two_factor_enabled");
    },
];
