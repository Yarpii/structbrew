<?php

return [
    'up' => function ($db) {
        $db->statement("ALTER TABLE customers ADD COLUMN loyalty_points INT UNSIGNED NOT NULL DEFAULT 0 AFTER customer_group");
        $db->statement("ALTER TABLE customers ADD COLUMN credits_balance DECIMAL(12,2) NOT NULL DEFAULT 0.00 AFTER loyalty_points");

        $db->statement("
            CREATE TABLE loyalty_point_transactions (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                customer_id INT UNSIGNED NOT NULL,
                points INT NOT NULL,
                balance_after INT UNSIGNED NOT NULL DEFAULT 0,
                event_type VARCHAR(60) NOT NULL,
                reference VARCHAR(120) NULL,
                description VARCHAR(255) NULL,
                metadata JSON NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                KEY idx_loyalty_customer_date (customer_id, created_at),
                UNIQUE KEY uk_loyalty_customer_event_ref (customer_id, event_type, reference),
                CONSTRAINT fk_loyalty_transactions_customer_id FOREIGN KEY (customer_id) REFERENCES customers (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $db->statement("
            CREATE TABLE credit_transactions (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                customer_id INT UNSIGNED NOT NULL,
                amount DECIMAL(12,2) NOT NULL,
                balance_after DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                direction ENUM('credit','debit') NOT NULL,
                event_type VARCHAR(60) NOT NULL,
                reference VARCHAR(120) NULL,
                description VARCHAR(255) NULL,
                metadata JSON NULL,
                is_withdrawable TINYINT(1) NOT NULL DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                KEY idx_credit_customer_date (customer_id, created_at),
                UNIQUE KEY uk_credit_customer_event_ref_direction (customer_id, event_type, reference, direction),
                CONSTRAINT fk_credit_transactions_customer_id FOREIGN KEY (customer_id) REFERENCES customers (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS credit_transactions");
        $db->statement("DROP TABLE IF EXISTS loyalty_point_transactions");
        $db->statement("ALTER TABLE customers DROP COLUMN credits_balance");
        $db->statement("ALTER TABLE customers DROP COLUMN loyalty_points");
    },
];
