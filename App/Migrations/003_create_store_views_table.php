<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE store_views (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                store_id INT UNSIGNED NOT NULL,
                code VARCHAR(50) NOT NULL,
                name VARCHAR(255) NOT NULL,
                locale VARCHAR(20) NOT NULL DEFAULT 'en_US',
                currency_code CHAR(3) NOT NULL DEFAULT 'USD',
                theme VARCHAR(100) NULL,
                is_default TINYINT(1) NOT NULL DEFAULT 0,
                sort_order INT NOT NULL DEFAULT 0,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_store_views_code (code),
                KEY idx_store_views_store_id (store_id),
                CONSTRAINT fk_store_views_store_id FOREIGN KEY (store_id) REFERENCES stores (id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS store_views");
    },
];
