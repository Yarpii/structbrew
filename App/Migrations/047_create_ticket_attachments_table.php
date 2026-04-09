<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE ticket_attachments (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                ticket_id INT UNSIGNED NOT NULL,
                reply_id INT UNSIGNED NULL,
                uploader_type ENUM('customer','admin') NOT NULL DEFAULT 'customer',
                uploader_id INT UNSIGNED NULL,
                original_filename VARCHAR(255) NOT NULL,
                stored_filename VARCHAR(255) NOT NULL,
                mime_type VARCHAR(100) NULL,
                file_size INT UNSIGNED NOT NULL DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                KEY idx_ta_ticket_id (ticket_id),
                KEY idx_ta_reply_id (reply_id),
                CONSTRAINT fk_ta_ticket_id FOREIGN KEY (ticket_id)
                    REFERENCES tickets (id) ON DELETE CASCADE,
                CONSTRAINT fk_ta_reply_id FOREIGN KEY (reply_id)
                    REFERENCES ticket_replies (id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS ticket_attachments");
    },
];
