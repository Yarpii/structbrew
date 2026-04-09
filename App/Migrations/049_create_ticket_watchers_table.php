<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE ticket_watchers (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                ticket_id INT UNSIGNED NOT NULL,
                watcher_type ENUM('admin','customer') NOT NULL DEFAULT 'admin',
                watcher_id INT UNSIGNED NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uk_tw_ticket_watcher (ticket_id, watcher_type, watcher_id),
                CONSTRAINT fk_tw_ticket FOREIGN KEY (ticket_id) REFERENCES tickets (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS ticket_watchers");
    },
];
