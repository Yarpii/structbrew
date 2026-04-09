<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE addresses (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                customer_id INT UNSIGNED NOT NULL,
                type ENUM('billing', 'shipping') NOT NULL,
                is_default TINYINT(1) NOT NULL DEFAULT 0,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                company VARCHAR(255) NULL,
                street_1 VARCHAR(255) NOT NULL,
                street_2 VARCHAR(255) NULL,
                city VARCHAR(100) NOT NULL,
                state VARCHAR(100) NULL,
                postcode VARCHAR(20) NOT NULL,
                country_code CHAR(2) NOT NULL,
                phone VARCHAR(50) NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY idx_addresses_customer_id (customer_id),
                CONSTRAINT fk_addresses_customer_id FOREIGN KEY (customer_id) REFERENCES customers (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS addresses");
    },
];
