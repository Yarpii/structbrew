<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE ticket_sla_policies (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                applies_to_priority ENUM('low','normal','high','critical','urgent') NOT NULL,
                first_response_hours SMALLINT UNSIGNED NOT NULL DEFAULT 24,
                resolution_hours SMALLINT UNSIGNED NOT NULL DEFAULT 72,
                escalation_hours SMALLINT UNSIGNED NULL,
                business_hours_only TINYINT(1) NOT NULL DEFAULT 0,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_sla_priority (applies_to_priority)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $db->statement("
            INSERT INTO ticket_sla_policies
                (name, applies_to_priority, first_response_hours, resolution_hours, escalation_hours) VALUES
            ('Low Priority SLA',      'low',      48, 120, NULL),
            ('Normal Priority SLA',   'normal',   24,  72, NULL),
            ('High Priority SLA',     'high',      8,  24,   48),
            ('Critical Priority SLA', 'critical',  2,   8,   12),
            ('Urgent Priority SLA',   'urgent',    1,   4,    6)
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS ticket_sla_policies");
    },
];
