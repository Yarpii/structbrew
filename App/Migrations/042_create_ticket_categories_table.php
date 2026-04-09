<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE ticket_categories (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                department_id INT UNSIGNED NULL,
                name VARCHAR(100) NOT NULL,
                slug VARCHAR(100) NOT NULL,
                description TEXT NULL,
                default_priority ENUM('low','normal','high','critical','urgent') NOT NULL DEFAULT 'normal',
                auto_assign_agent_id INT UNSIGNED NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_ticket_categories_slug (slug),
                KEY idx_ticket_categories_department_id (department_id),
                CONSTRAINT fk_tc_department_id FOREIGN KEY (department_id) REFERENCES ticket_departments (id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $db->statement("
            INSERT INTO ticket_categories (department_id, name, slug, default_priority, sort_order) VALUES
            (1, 'Order Issue',          'order-issue',         'normal', 1),
            (1, 'Product Inquiry',      'product-inquiry',     'normal', 2),
            (1, 'Delivery Problem',     'delivery-problem',    'high',   3),
            (2, 'Pricing & Quotes',     'pricing-quotes',      'normal', 4),
            (2, 'Partnership Request',  'partnership-request', 'normal', 5),
            (3, 'Technical Bug',        'technical-bug',       'high',   6),
            (3, 'Integration Issue',    'integration-issue',   'high',   7),
            (4, 'Invoice Request',      'invoice-request',     'normal', 8),
            (4, 'Payment Issue',        'payment-issue',       'critical',9),
            (5, 'Shipping Update',      'shipping-update',     'normal', 10),
            (6, 'Return Request',       'return-request',      'normal', 11),
            (6, 'Refund Request',       'refund-request',      'high',   12),
            (7, 'Brand Partnership',    'brand-partnership',   'normal', 13),
            (8, 'Advertising Inquiry',  'advertising-inquiry', 'normal', 14),
            (NULL,'General Question',   'general-question',    'low',    15)
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS ticket_categories");
    },
];
