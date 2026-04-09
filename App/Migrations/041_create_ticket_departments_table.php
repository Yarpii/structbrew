<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE ticket_departments (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                code VARCHAR(50) NOT NULL,
                description TEXT NULL,
                color VARCHAR(7) NOT NULL DEFAULT '#3b82f6',
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_ticket_departments_code (code)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $db->statement("
            INSERT INTO ticket_departments (name, code, color, sort_order) VALUES
            ('Customer Support', 'support',   '#3b82f6', 1),
            ('Sales',            'sales',     '#10b981', 2),
            ('Technical',        'technical', '#8b5cf6', 3),
            ('Billing',          'billing',   '#f59e0b', 4),
            ('Logistics',        'logistics', '#06b6d4', 5),
            ('Returns & Refunds','returns',   '#ef4444', 6),
            ('Partnerships',     'partnerships','#ec4899', 7),
            ('Advertising',      'advertising','#f97316', 8)
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS ticket_departments");
    },
];
