<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE vehicles (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                brand_id INT UNSIGNED NOT NULL,
                model VARCHAR(255) NOT NULL,
                year_from INT NOT NULL,
                year_to INT NULL,
                engine_cc INT NOT NULL,
                slug VARCHAR(255) NOT NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_vehicles_slug (slug),
                KEY idx_vehicles_brand_id (brand_id),
                CONSTRAINT fk_vehicles_brand_id FOREIGN KEY (brand_id) REFERENCES brands (id) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS vehicles");
    },
];
