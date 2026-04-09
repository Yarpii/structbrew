<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE carts (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                customer_id INT UNSIGNED NULL,
                store_view_id INT UNSIGNED NOT NULL,
                session_id VARCHAR(255) NOT NULL,
                currency_code CHAR(3) NOT NULL,
                coupon_code VARCHAR(50) NULL,
                subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                tax_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                grand_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY idx_carts_customer_id (customer_id),
                KEY idx_carts_store_view_id (store_view_id),
                KEY idx_carts_session_id (session_id),
                CONSTRAINT fk_carts_customer_id FOREIGN KEY (customer_id) REFERENCES customers (id) ON DELETE SET NULL,
                CONSTRAINT fk_carts_store_view_id FOREIGN KEY (store_view_id) REFERENCES store_views (id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS carts");
    },
];
