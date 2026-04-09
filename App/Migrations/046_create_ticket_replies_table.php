<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE ticket_replies (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                ticket_id INT UNSIGNED NOT NULL,
                author_type ENUM('customer','admin','system') NOT NULL DEFAULT 'admin',
                author_id INT UNSIGNED NULL,
                author_name VARCHAR(150) NULL,
                author_email VARCHAR(255) NULL,
                body TEXT NOT NULL,
                is_internal TINYINT(1) NOT NULL DEFAULT 0,
                is_resolution TINYINT(1) NOT NULL DEFAULT 0,
                time_spent_minutes SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY idx_tr_ticket_id (ticket_id),
                KEY idx_tr_author (author_type, author_id),
                CONSTRAINT fk_tr_ticket_id FOREIGN KEY (ticket_id)
                    REFERENCES tickets (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS ticket_replies");
    },
];
