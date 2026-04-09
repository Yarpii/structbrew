<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE product_images (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                product_id INT UNSIGNED NOT NULL,
                path VARCHAR(500) NOT NULL,
                alt_text VARCHAR(255) NULL,
                position INT NOT NULL DEFAULT 0,
                is_main TINYINT(1) NOT NULL DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                KEY idx_product_images_product_id (product_id),
                CONSTRAINT fk_product_images_product_id FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS product_images");
    },
];
