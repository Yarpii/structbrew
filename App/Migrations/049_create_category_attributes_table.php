<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE category_attributes (
                category_id INT UNSIGNED NOT NULL,
                attribute_id INT UNSIGNED NOT NULL,
                PRIMARY KEY (category_id, attribute_id),
                KEY idx_category_attributes_attribute_id (attribute_id),
                CONSTRAINT fk_category_attributes_category_id FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE,
                CONSTRAINT fk_category_attributes_attribute_id FOREIGN KEY (attribute_id) REFERENCES attributes (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS category_attributes");
    },
];
