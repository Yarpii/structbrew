<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE product_pricing (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                product_id INT UNSIGNED NOT NULL,
                store_view_id INT UNSIGNED NOT NULL,
                price DECIMAL(10,2) NOT NULL,
                sale_price DECIMAL(10,2) NULL,
                cost_price DECIMAL(10,2) NULL,
                currency_code CHAR(3) NOT NULL,
                UNIQUE KEY uk_product_pricing_prod_sv (product_id, store_view_id),
                KEY idx_product_pricing_store_view_id (store_view_id),
                CONSTRAINT fk_product_pricing_product_id FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE,
                CONSTRAINT fk_product_pricing_store_view_id FOREIGN KEY (store_view_id) REFERENCES store_views (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS product_pricing");
    },
];
