<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE stores (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                website_id INT UNSIGNED NOT NULL,
                code VARCHAR(50) NOT NULL,
                name VARCHAR(255) NOT NULL,
                is_default TINYINT(1) NOT NULL DEFAULT 0,
                sort_order INT NOT NULL DEFAULT 0,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_stores_code (code),
                KEY idx_stores_website_id (website_id),
                CONSTRAINT fk_stores_website_id FOREIGN KEY (website_id) REFERENCES websites (id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS stores");
    },
];
