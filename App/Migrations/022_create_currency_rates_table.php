<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE currency_rates (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                base_currency CHAR(3) NOT NULL,
                target_currency CHAR(3) NOT NULL,
                rate DECIMAL(12,6) NOT NULL,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_currency_rates_base_target (base_currency, target_currency)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS currency_rates");
    },
];
