<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE product_categories (
                product_id INT UNSIGNED NOT NULL,
                category_id INT UNSIGNED NOT NULL,
                position INT NOT NULL DEFAULT 0,
                PRIMARY KEY (product_id, category_id),
                KEY idx_product_categories_category_id (category_id),
                CONSTRAINT fk_product_categories_product_id FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE,
                CONSTRAINT fk_product_categories_category_id FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS product_categories");
    },
];
