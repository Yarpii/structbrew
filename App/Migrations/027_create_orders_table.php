<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE orders (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                order_number VARCHAR(50) NOT NULL,
                customer_id INT UNSIGNED NULL,
                store_view_id INT UNSIGNED NOT NULL,
                status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded') NOT NULL DEFAULT 'pending',
                currency_code CHAR(3) NOT NULL,
                subtotal DECIMAL(10,2) NOT NULL,
                tax_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                shipping_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                grand_total DECIMAL(10,2) NOT NULL,
                coupon_code VARCHAR(50) NULL,
                shipping_method VARCHAR(100) NOT NULL,
                payment_method VARCHAR(100) NOT NULL,
                billing_address JSON NOT NULL,
                shipping_address JSON NOT NULL,
                customer_email VARCHAR(255) NOT NULL,
                customer_note TEXT NULL,
                ip_address VARCHAR(45) NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_orders_order_number (order_number),
                KEY idx_orders_customer_id (customer_id),
                KEY idx_orders_store_view_id (store_view_id),
                KEY idx_orders_status (status),
                CONSTRAINT fk_orders_customer_id FOREIGN KEY (customer_id) REFERENCES customers (id) ON DELETE SET NULL,
                CONSTRAINT fk_orders_store_view_id FOREIGN KEY (store_view_id) REFERENCES store_views (id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS orders");
    },
];
