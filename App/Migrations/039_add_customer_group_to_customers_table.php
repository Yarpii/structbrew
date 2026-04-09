<?php

return [
    'up' => function ($db) {
        $db->statement("ALTER TABLE customers ADD COLUMN customer_group VARCHAR(50) NOT NULL DEFAULT 'retail' AFTER gender");
    },
    'down' => function ($db) {
        $db->statement("ALTER TABLE customers DROP COLUMN customer_group");
    },
];
