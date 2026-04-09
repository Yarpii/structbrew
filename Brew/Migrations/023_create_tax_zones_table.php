<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE tax_zones (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                country_code CHAR(2) NOT NULL,
                state VARCHAR(100) NULL,
                postcode_pattern VARCHAR(100) NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS tax_zones");
    },
];
