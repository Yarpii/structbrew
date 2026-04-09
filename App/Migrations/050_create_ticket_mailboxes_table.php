<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE ticket_mailboxes (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                department_id INT UNSIGNED NULL,
                name VARCHAR(120) NOT NULL,
                email VARCHAR(255) NOT NULL,
                from_name VARCHAR(120) NULL,
                smtp_host VARCHAR(255) NOT NULL,
                smtp_port SMALLINT UNSIGNED NOT NULL DEFAULT 587,
                smtp_encryption ENUM('none','ssl','tls') NOT NULL DEFAULT 'tls',
                smtp_username VARCHAR(255) NOT NULL,
                smtp_password VARCHAR(255) NOT NULL,
                incoming_host VARCHAR(255) NULL,
                incoming_port SMALLINT UNSIGNED NULL,
                incoming_encryption ENUM('none','ssl','tls') NOT NULL DEFAULT 'ssl',
                incoming_username VARCHAR(255) NULL,
                incoming_password VARCHAR(255) NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_ticket_mailboxes_email (email),
                KEY idx_ticket_mailboxes_department (department_id),
                KEY idx_ticket_mailboxes_active (is_active),
                CONSTRAINT fk_ticket_mailboxes_department FOREIGN KEY (department_id)
                    REFERENCES ticket_departments (id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS ticket_mailboxes");
    },
];
