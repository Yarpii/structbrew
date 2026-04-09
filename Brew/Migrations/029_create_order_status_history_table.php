<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE order_status_history (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                order_id INT UNSIGNED NOT NULL,
                status VARCHAR(50) NOT NULL,
                comment TEXT NULL,
                is_customer_notified TINYINT(1) NOT NULL DEFAULT 0,
                created_by VARCHAR(100) NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                KEY idx_order_status_history_order_id (order_id),
                CONSTRAINT fk_order_status_history_order_id FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS order_status_history");
    },
];
