<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE customer_vehicles (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                customer_id INT UNSIGNED NOT NULL,
                vehicle_id INT UNSIGNED NOT NULL,
                vehicle_type VARCHAR(50) NOT NULL DEFAULT 'scooter',
                nickname VARCHAR(100) NULL,
                is_default TINYINT(1) NOT NULL DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_customer_vehicle_unique (customer_id, vehicle_id, vehicle_type),
                KEY idx_customer_vehicles_customer_id (customer_id),
                KEY idx_customer_vehicles_vehicle_id (vehicle_id),
                CONSTRAINT fk_customer_vehicles_customer_id FOREIGN KEY (customer_id) REFERENCES customers (id) ON DELETE CASCADE,
                CONSTRAINT fk_customer_vehicles_vehicle_id FOREIGN KEY (vehicle_id) REFERENCES vehicles (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS customer_vehicles");
    },
];
