<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE store_domains (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                store_view_id INT UNSIGNED NOT NULL,
                domain VARCHAR(255) NOT NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                is_primary TINYINT(1) NOT NULL DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_store_domains_domain (domain),
                KEY idx_store_domains_store_view_id (store_view_id),
                CONSTRAINT fk_store_domains_store_view_id FOREIGN KEY (store_view_id) REFERENCES store_views (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS store_domains");
    },
];
