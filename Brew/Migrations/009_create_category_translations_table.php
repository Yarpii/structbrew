<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE category_translations (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                category_id INT UNSIGNED NOT NULL,
                store_view_id INT UNSIGNED NOT NULL,
                name VARCHAR(255) NOT NULL,
                description TEXT NULL,
                meta_title VARCHAR(255) NULL,
                meta_description TEXT NULL,
                UNIQUE KEY uk_category_translations_cat_sv (category_id, store_view_id),
                KEY idx_category_translations_store_view_id (store_view_id),
                CONSTRAINT fk_category_translations_category_id FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE,
                CONSTRAINT fk_category_translations_store_view_id FOREIGN KEY (store_view_id) REFERENCES store_views (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS category_translations");
    },
];
