<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE marketing_ads (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(120) NOT NULL,
                placement VARCHAR(80) NOT NULL,
                title VARCHAR(255) NOT NULL,
                subtitle TEXT NULL,
                image_url VARCHAR(255) NULL,
                cta_label VARCHAR(80) NULL,
                cta_url VARCHAR(255) NULL,
                background_value VARCHAR(255) NULL,
                starts_at DATETIME NULL,
                ends_at DATETIME NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY idx_marketing_ads_placement (placement),
                KEY idx_marketing_ads_active (is_active),
                KEY idx_marketing_ads_schedule (starts_at, ends_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS marketing_ads");
    },
];
