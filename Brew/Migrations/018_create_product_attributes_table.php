<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE product_attributes (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                product_id INT UNSIGNED NOT NULL,
                attribute_key VARCHAR(100) NOT NULL,
                attribute_value TEXT NOT NULL,
                store_view_id INT UNSIGNED NULL,
                KEY idx_product_attributes_prod_key (product_id, attribute_key),
                KEY idx_product_attributes_store_view_id (store_view_id),
                CONSTRAINT fk_product_attributes_product_id FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE,
                CONSTRAINT fk_product_attributes_store_view_id FOREIGN KEY (store_view_id) REFERENCES store_views (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS product_attributes");
    },
];
