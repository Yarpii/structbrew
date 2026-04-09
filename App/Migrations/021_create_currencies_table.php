<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE currencies (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                code CHAR(3) NOT NULL,
                name VARCHAR(100) NOT NULL,
                symbol VARCHAR(10) NOT NULL,
                decimal_places INT NOT NULL DEFAULT 2,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_currencies_code (code)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS currencies");
    },
];
