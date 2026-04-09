<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE tax_rates (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                tax_zone_id INT UNSIGNED NOT NULL,
                name VARCHAR(255) NOT NULL,
                rate DECIMAL(5,2) NOT NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY idx_tax_rates_tax_zone_id (tax_zone_id),
                CONSTRAINT fk_tax_rates_tax_zone_id FOREIGN KEY (tax_zone_id) REFERENCES tax_zones (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS tax_rates");
    },
];
