<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE dealer_applications (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                company_name VARCHAR(191) NOT NULL,
                contact_name VARCHAR(191) NOT NULL,
                email VARCHAR(191) NOT NULL,
                phone VARCHAR(50) NULL,
                website VARCHAR(255) NULL,
                country VARCHAR(100) NULL,
                business_type ENUM('retailer','webshop','workshop','distributor','other') NOT NULL DEFAULT 'other',
                vat_number VARCHAR(100) NULL,
                annual_volume VARCHAR(100) NULL,
                message TEXT NULL,
                status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
                admin_notes TEXT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY idx_dealer_apps_status (status),
                KEY idx_dealer_apps_email (email)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $db->statement("
            CREATE TABLE dealer_accounts (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                application_id BIGINT UNSIGNED NULL,
                customer_id INT UNSIGNED NULL,
                company_name VARCHAR(191) NOT NULL,
                contact_name VARCHAR(191) NOT NULL,
                email VARCHAR(191) NOT NULL,
                phone VARCHAR(50) NULL,
                account_number VARCHAR(20) NOT NULL,
                discount_rate DECIMAL(5,2) NOT NULL DEFAULT 0.00,
                credit_limit DECIMAL(12,2) NOT NULL DEFAULT 0.00,
                payment_terms ENUM('prepaid','net15','net30','net60') NOT NULL DEFAULT 'prepaid',
                status ENUM('active','paused','suspended') NOT NULL DEFAULT 'active',
                notes TEXT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_dealer_account_number (account_number),
                KEY idx_dealer_accounts_email (email),
                KEY idx_dealer_accounts_customer (customer_id),
                KEY idx_dealer_accounts_application (application_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },

    'down' => function ($db) {
        $db->statement('DROP TABLE IF EXISTS dealer_accounts');
        $db->statement('DROP TABLE IF EXISTS dealer_applications');
    },
];
