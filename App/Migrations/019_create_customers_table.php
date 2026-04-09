<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE customers (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                store_view_id INT UNSIGNED NOT NULL,
                email VARCHAR(255) NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                phone VARCHAR(50) NULL,
                date_of_birth DATE NULL,
                gender VARCHAR(20) NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                email_verified_at TIMESTAMP NULL,
                last_login_at TIMESTAMP NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_customers_email (email),
                KEY idx_customers_store_view_id (store_view_id),
                CONSTRAINT fk_customers_store_view_id FOREIGN KEY (store_view_id) REFERENCES store_views (id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS customers");
    },
];
