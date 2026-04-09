<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE cart_items (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                cart_id INT UNSIGNED NOT NULL,
                product_id INT UNSIGNED NOT NULL,
                qty INT NOT NULL DEFAULT 1,
                price DECIMAL(10,2) NOT NULL,
                row_total DECIMAL(10,2) NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY idx_cart_items_cart_id (cart_id),
                KEY idx_cart_items_product_id (product_id),
                CONSTRAINT fk_cart_items_cart_id FOREIGN KEY (cart_id) REFERENCES carts (id) ON DELETE CASCADE,
                CONSTRAINT fk_cart_items_product_id FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS cart_items");
    },
];
