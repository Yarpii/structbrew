<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE order_items (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                order_id INT UNSIGNED NOT NULL,
                product_id INT UNSIGNED NULL,
                sku VARCHAR(100) NOT NULL,
                name VARCHAR(255) NOT NULL,
                qty INT NOT NULL,
                price DECIMAL(10,2) NOT NULL,
                row_total DECIMAL(10,2) NOT NULL,
                tax_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                options JSON NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                KEY idx_order_items_order_id (order_id),
                KEY idx_order_items_product_id (product_id),
                CONSTRAINT fk_order_items_order_id FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
                CONSTRAINT fk_order_items_product_id FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS order_items");
    },
];
