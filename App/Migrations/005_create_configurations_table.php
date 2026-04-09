<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE configurations (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                path VARCHAR(255) NOT NULL,
                value TEXT NULL,
                scope ENUM('global', 'website', 'store', 'store_view') NOT NULL DEFAULT 'global',
                scope_id INT UNSIGNED NOT NULL DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_configurations_path_scope (path, scope, scope_id),
                KEY idx_configurations_scope (scope, scope_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS configurations");
    },
];
