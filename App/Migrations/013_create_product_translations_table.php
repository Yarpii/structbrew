<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE product_translations (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                product_id INT UNSIGNED NOT NULL,
                store_view_id INT UNSIGNED NOT NULL,
                name VARCHAR(255) NOT NULL,
                short_description TEXT NULL,
                description LONGTEXT NULL,
                meta_title VARCHAR(255) NULL,
                meta_description TEXT NULL,
                url_key VARCHAR(255) NOT NULL,
                UNIQUE KEY uk_product_translations_prod_sv (product_id, store_view_id),
                KEY idx_product_translations_store_view_id (store_view_id),
                CONSTRAINT fk_product_translations_product_id FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE,
                CONSTRAINT fk_product_translations_store_view_id FOREIGN KEY (store_view_id) REFERENCES store_views (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS product_translations");
    },
];
