<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE partner_applications (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                email VARCHAR(191) NOT NULL,
                company VARCHAR(191) NULL,
                website VARCHAR(255) NULL,
                country VARCHAR(100) NULL,
                message TEXT NULL,
                status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
                admin_notes TEXT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY idx_partner_apps_status (status),
                KEY idx_partner_apps_email (email)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $db->statement("
            CREATE TABLE partner_accounts (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                application_id BIGINT UNSIGNED NULL,
                customer_id INT UNSIGNED NULL,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                email VARCHAR(191) NOT NULL,
                company VARCHAR(191) NULL,
                referral_code VARCHAR(32) NOT NULL,
                commission_rate DECIMAL(5,2) NOT NULL DEFAULT 10.00,
                total_clicks INT UNSIGNED NOT NULL DEFAULT 0,
                total_conversions INT UNSIGNED NOT NULL DEFAULT 0,
                total_commission_earned DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                balance DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                status ENUM('active','paused','suspended') NOT NULL DEFAULT 'active',
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_partner_referral_code (referral_code),
                KEY idx_partner_accounts_email (email),
                KEY idx_partner_accounts_customer (customer_id),
                KEY idx_partner_accounts_application (application_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $db->statement("
            CREATE TABLE partner_referrals (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                partner_account_id BIGINT UNSIGNED NOT NULL,
                order_id BIGINT UNSIGNED NULL,
                referral_code VARCHAR(32) NOT NULL,
                order_total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                commission_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                status ENUM('pending','approved','paid','rejected') NOT NULL DEFAULT 'pending',
                note VARCHAR(255) NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY idx_partner_referrals_account (partner_account_id),
                KEY idx_partner_referrals_order (order_id),
                KEY idx_partner_referrals_code (referral_code),
                CONSTRAINT fk_partner_referrals_account FOREIGN KEY (partner_account_id)
                    REFERENCES partner_accounts (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },

    'down' => function ($db) {
        $db->statement('DROP TABLE IF EXISTS partner_referrals');
        $db->statement('DROP TABLE IF EXISTS partner_accounts');
        $db->statement('DROP TABLE IF EXISTS partner_applications');
    },
];
