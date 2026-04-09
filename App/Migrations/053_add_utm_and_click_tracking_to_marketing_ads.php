<?php

return [
    'up' => function ($db) {
        $db->statement("ALTER TABLE marketing_ads ADD COLUMN utm_source VARCHAR(100) NULL AFTER cta_url");
        $db->statement("ALTER TABLE marketing_ads ADD COLUMN utm_medium VARCHAR(100) NULL AFTER utm_source");
        $db->statement("ALTER TABLE marketing_ads ADD COLUMN utm_campaign VARCHAR(120) NULL AFTER utm_medium");
        $db->statement("ALTER TABLE marketing_ads ADD COLUMN utm_term VARCHAR(120) NULL AFTER utm_campaign");
        $db->statement("ALTER TABLE marketing_ads ADD COLUMN utm_content VARCHAR(120) NULL AFTER utm_term");
        $db->statement("ALTER TABLE marketing_ads ADD COLUMN clicks_count INT UNSIGNED NOT NULL DEFAULT 0 AFTER sort_order");
        $db->statement("ALTER TABLE marketing_ads ADD COLUMN last_clicked_at DATETIME NULL AFTER clicks_count");

        $db->statement("\n            CREATE TABLE marketing_ad_clicks (\n                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,\n                ad_id INT UNSIGNED NOT NULL,\n                target_url VARCHAR(255) NULL,\n                referrer VARCHAR(255) NULL,\n                user_agent VARCHAR(255) NULL,\n                ip_address VARCHAR(45) NULL,\n                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,\n                KEY idx_marketing_ad_clicks_ad_id (ad_id),\n                KEY idx_marketing_ad_clicks_created_at (created_at),\n                CONSTRAINT fk_marketing_ad_clicks_ad FOREIGN KEY (ad_id)\n                    REFERENCES marketing_ads (id) ON DELETE CASCADE\n            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci\n        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS marketing_ad_clicks");

        $db->statement("ALTER TABLE marketing_ads DROP COLUMN last_clicked_at");
        $db->statement("ALTER TABLE marketing_ads DROP COLUMN clicks_count");
        $db->statement("ALTER TABLE marketing_ads DROP COLUMN utm_content");
        $db->statement("ALTER TABLE marketing_ads DROP COLUMN utm_term");
        $db->statement("ALTER TABLE marketing_ads DROP COLUMN utm_campaign");
        $db->statement("ALTER TABLE marketing_ads DROP COLUMN utm_medium");
        $db->statement("ALTER TABLE marketing_ads DROP COLUMN utm_source");
    },
];
