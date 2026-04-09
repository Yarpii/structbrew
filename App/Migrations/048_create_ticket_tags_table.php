<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE ticket_tags (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(60) NOT NULL,
                slug VARCHAR(60) NOT NULL,
                color VARCHAR(7) NOT NULL DEFAULT '#6b7280',
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uk_ticket_tags_slug (slug)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $db->statement("
            CREATE TABLE ticket_ticket_tags (
                ticket_id INT UNSIGNED NOT NULL,
                tag_id INT UNSIGNED NOT NULL,
                PRIMARY KEY (ticket_id, tag_id),
                CONSTRAINT fk_ttt_ticket FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE,
                CONSTRAINT fk_ttt_tag FOREIGN KEY (tag_id) REFERENCES ticket_tags (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS ticket_ticket_tags");
        $db->statement("DROP TABLE IF EXISTS ticket_tags");
    },
];
