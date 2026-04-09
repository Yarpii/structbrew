<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE cms_page_translations (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                cms_page_id INT UNSIGNED NOT NULL,
                store_view_id INT UNSIGNED NOT NULL,
                title VARCHAR(255) NOT NULL,
                content LONGTEXT NOT NULL,
                meta_title VARCHAR(255) NULL,
                meta_description TEXT NULL,
                UNIQUE KEY uk_cms_page_translations_page_sv (cms_page_id, store_view_id),
                KEY idx_cms_page_translations_store_view_id (store_view_id),
                CONSTRAINT fk_cms_page_translations_page_id FOREIGN KEY (cms_page_id) REFERENCES cms_pages (id) ON DELETE CASCADE,
                CONSTRAINT fk_cms_page_translations_store_view_id FOREIGN KEY (store_view_id) REFERENCES store_views (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS cms_page_translations");
    },
];
