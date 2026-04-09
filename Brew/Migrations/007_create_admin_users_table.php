<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE admin_users (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                role_id INT UNSIGNED NOT NULL,
                email VARCHAR(255) NOT NULL,
                password_hash VARCHAR(255) NOT NULL,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                is_superadmin TINYINT(1) NOT NULL DEFAULT 0,
                last_login_at TIMESTAMP NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_admin_users_email (email),
                KEY idx_admin_users_role_id (role_id),
                CONSTRAINT fk_admin_users_role_id FOREIGN KEY (role_id) REFERENCES admin_roles (id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS admin_users");
    },
];
