<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE ticket_canned_responses (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                department_id INT UNSIGNED NULL,
                name VARCHAR(150) NOT NULL,
                subject VARCHAR(255) NULL,
                body TEXT NOT NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY idx_canned_department_id (department_id),
                CONSTRAINT fk_canned_department_id FOREIGN KEY (department_id)
                    REFERENCES ticket_departments (id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS ticket_canned_responses");
    },
];
