<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE products (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                sku VARCHAR(100) NOT NULL,
                slug VARCHAR(255) NOT NULL,
                brand_id INT UNSIGNED NULL,
                weight DECIMAL(8,2) NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                is_featured TINYINT(1) NOT NULL DEFAULT 0,
                manage_stock TINYINT(1) NOT NULL DEFAULT 1,
                stock_qty INT NOT NULL DEFAULT 0,
                low_stock_threshold INT NOT NULL DEFAULT 5,
                oem_number VARCHAR(100) NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_products_sku (sku),
                UNIQUE KEY uk_products_slug (slug),
                KEY idx_products_brand_id (brand_id),
                CONSTRAINT fk_products_brand_id FOREIGN KEY (brand_id) REFERENCES brands (id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS products");
    },
];
