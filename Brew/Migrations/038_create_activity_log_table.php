<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE activity_log (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                admin_user_id INT UNSIGNED NULL,
                action VARCHAR(100) NOT NULL,
                entity_type VARCHAR(100) NOT NULL,
                entity_id INT UNSIGNED NULL,
                old_data JSON NULL,
                new_data JSON NULL,
                ip_address VARCHAR(45) NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                KEY idx_activity_log_admin_user_id (admin_user_id),
                KEY idx_activity_log_entity (entity_type, entity_id),
                CONSTRAINT fk_activity_log_admin_user_id FOREIGN KEY (admin_user_id) REFERENCES admin_users (id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS activity_log");
    },
];
