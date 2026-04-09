<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE translations (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                store_view_id INT UNSIGNED NOT NULL,
                `key` VARCHAR(255) NOT NULL,
                value TEXT NOT NULL,
                `group` VARCHAR(100) NOT NULL DEFAULT 'general',
                UNIQUE KEY uk_translations_sv_key_group (store_view_id, `key`, `group`),
                CONSTRAINT fk_translations_store_view_id FOREIGN KEY (store_view_id) REFERENCES store_views (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS translations");
    },
];
