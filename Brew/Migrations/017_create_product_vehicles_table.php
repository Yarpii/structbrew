<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE product_vehicles (
                product_id INT UNSIGNED NOT NULL,
                vehicle_id INT UNSIGNED NOT NULL,
                notes TEXT NULL,
                PRIMARY KEY (product_id, vehicle_id),
                KEY idx_product_vehicles_vehicle_id (vehicle_id),
                CONSTRAINT fk_product_vehicles_product_id FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE,
                CONSTRAINT fk_product_vehicles_vehicle_id FOREIGN KEY (vehicle_id) REFERENCES vehicles (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS product_vehicles");
    },
];
