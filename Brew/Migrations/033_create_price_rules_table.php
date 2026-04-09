<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE price_rules (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                description TEXT NULL,
                type ENUM('percentage', 'fixed') NOT NULL,
                value DECIMAL(10,2) NOT NULL,
                min_order_total DECIMAL(10,2) NULL,
                starts_at DATETIME NULL,
                expires_at DATETIME NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                usage_limit INT NULL,
                times_used INT NOT NULL DEFAULT 0,
                store_view_ids JSON NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS price_rules");
    },
];
