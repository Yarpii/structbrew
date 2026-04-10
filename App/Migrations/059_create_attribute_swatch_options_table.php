<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE attribute_swatch_options (
                id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                attribute_id  INT UNSIGNED NOT NULL,
                label         VARCHAR(255) NOT NULL,
                value         VARCHAR(255) NOT NULL COMMENT 'hex code for swatch_color, filename for swatch_image, raw value for multi_select',
                sort_order    INT NOT NULL DEFAULT 0,
                created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                KEY idx_aso_attribute_id (attribute_id),
                CONSTRAINT fk_aso_attribute_id FOREIGN KEY (attribute_id) REFERENCES attributes (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS attribute_swatch_options");
    },
];
