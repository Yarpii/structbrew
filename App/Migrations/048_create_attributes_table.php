<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE attributes (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(100) NOT NULL,
                label VARCHAR(255) NOT NULL,
                input_type VARCHAR(20) NOT NULL DEFAULT 'text',
                options_json JSON NULL,
                is_required TINYINT(1) NOT NULL DEFAULT 0,
                is_filterable TINYINT(1) NOT NULL DEFAULT 0,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                sort_order INT NOT NULL DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_attributes_code (code),
                KEY idx_attributes_active_sort (is_active, sort_order)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS attributes");
    },
];
