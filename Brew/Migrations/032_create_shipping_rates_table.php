<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE shipping_rates (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                shipping_zone_id INT UNSIGNED NOT NULL,
                shipping_method_id INT UNSIGNED NOT NULL,
                min_weight DECIMAL(8,2) NOT NULL DEFAULT 0.00,
                max_weight DECIMAL(8,2) NULL,
                min_order_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                price DECIMAL(10,2) NOT NULL,
                free_shipping_threshold DECIMAL(10,2) NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY idx_shipping_rates_zone_id (shipping_zone_id),
                KEY idx_shipping_rates_method_id (shipping_method_id),
                CONSTRAINT fk_shipping_rates_zone_id FOREIGN KEY (shipping_zone_id) REFERENCES shipping_zones (id) ON DELETE CASCADE,
                CONSTRAINT fk_shipping_rates_method_id FOREIGN KEY (shipping_method_id) REFERENCES shipping_methods (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS shipping_rates");
    },
];
