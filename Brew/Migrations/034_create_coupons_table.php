<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE coupons (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                price_rule_id INT UNSIGNED NOT NULL,
                code VARCHAR(50) NOT NULL,
                usage_limit INT NULL,
                usage_per_customer INT NOT NULL DEFAULT 1,
                times_used INT NOT NULL DEFAULT 0,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_coupons_code (code),
                KEY idx_coupons_price_rule_id (price_rule_id),
                CONSTRAINT fk_coupons_price_rule_id FOREIGN KEY (price_rule_id) REFERENCES price_rules (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS coupons");
    },
];
