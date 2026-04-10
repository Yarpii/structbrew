<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE customer_vehicle_mods (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                customer_vehicle_id INT UNSIGNED NOT NULL,
                title VARCHAR(255) NOT NULL,
                product_search VARCHAR(255) NULL DEFAULT NULL,
                is_done TINYINT(1) NOT NULL DEFAULT 0,
                sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY idx_cvm_vehicle_id (customer_vehicle_id),
                CONSTRAINT fk_cvm_customer_vehicle_id FOREIGN KEY (customer_vehicle_id)
                    REFERENCES customer_vehicles (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS customer_vehicle_mods");
    },
];
