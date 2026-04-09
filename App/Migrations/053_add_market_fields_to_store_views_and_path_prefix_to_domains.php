<?php

return [
    'up' => function ($db) {
        $db->statement("ALTER TABLE store_views ADD COLUMN country_code CHAR(2) NULL AFTER locale");
        $db->statement("ALTER TABLE store_views ADD COLUMN hreflang VARCHAR(20) NULL AFTER country_code");
        $db->statement("ALTER TABLE store_views ADD COLUMN timezone VARCHAR(100) NULL AFTER hreflang");

        $db->statement("UPDATE store_views SET country_code = UPPER(SUBSTRING(locale, 4, 2)) WHERE country_code IS NULL OR country_code = ''");
        $db->statement("UPDATE store_views SET hreflang = REPLACE(locale, '_', '-') WHERE hreflang IS NULL OR hreflang = ''");
        $db->statement("UPDATE store_views SET timezone = 'UTC' WHERE timezone IS NULL OR timezone = ''");

        $db->statement("ALTER TABLE store_domains ADD COLUMN path_prefix VARCHAR(255) NOT NULL DEFAULT '/' AFTER domain");
        $db->statement("UPDATE store_domains SET path_prefix = '/' WHERE path_prefix IS NULL OR path_prefix = ''");
        $db->statement("ALTER TABLE store_domains DROP INDEX uk_store_domains_domain");
        $db->statement("ALTER TABLE store_domains ADD UNIQUE KEY uk_store_domains_domain_path (domain, path_prefix)");
        $db->statement("ALTER TABLE store_domains ADD KEY idx_store_domains_lookup (domain, is_active)");
    },
    'down' => function ($db) {
        $db->statement("ALTER TABLE store_domains DROP INDEX idx_store_domains_lookup");
        $db->statement("ALTER TABLE store_domains DROP INDEX uk_store_domains_domain_path");
        $db->statement("ALTER TABLE store_domains ADD UNIQUE KEY uk_store_domains_domain (domain)");
        $db->statement("ALTER TABLE store_domains DROP COLUMN path_prefix");

        $db->statement("ALTER TABLE store_views DROP COLUMN timezone");
        $db->statement("ALTER TABLE store_views DROP COLUMN hreflang");
        $db->statement("ALTER TABLE store_views DROP COLUMN country_code");
    },
];
