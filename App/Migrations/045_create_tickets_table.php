<?php

return [
    'up' => function ($db) {
        $db->statement("
            CREATE TABLE tickets (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                ticket_number VARCHAR(30) NOT NULL,
                subject VARCHAR(255) NOT NULL,
                status ENUM(
                    'open','in_progress','waiting_customer','waiting_third_party',
                    'on_hold','resolved','closed','reopened'
                ) NOT NULL DEFAULT 'open',
                priority ENUM('low','normal','high','critical','urgent') NOT NULL DEFAULT 'normal',
                type ENUM(
                    'order_support','product_inquiry','technical','billing',
                    'shipping','returns','general','partnership','advertising'
                ) NOT NULL DEFAULT 'general',
                source ENUM('web','email','phone','api','chat','admin') NOT NULL DEFAULT 'web',
                requester_type ENUM('customer','guest','admin') NOT NULL DEFAULT 'customer',
                customer_id INT UNSIGNED NULL,
                guest_email VARCHAR(255) NULL,
                guest_name VARCHAR(150) NULL,
                assigned_agent_id INT UNSIGNED NULL,
                department_id INT UNSIGNED NULL,
                category_id INT UNSIGNED NULL,
                brand_id INT UNSIGNED NULL,
                website_id INT UNSIGNED NULL,
                store_view_id INT UNSIGNED NULL,
                order_id INT UNSIGNED NULL,
                sla_policy_id INT UNSIGNED NULL,
                sla_first_response_due_at DATETIME NULL,
                sla_resolution_due_at DATETIME NULL,
                sla_first_response_met TINYINT(1) NULL,
                sla_resolution_met TINYINT(1) NULL,
                first_response_at DATETIME NULL,
                resolved_at DATETIME NULL,
                closed_at DATETIME NULL,
                last_activity_at DATETIME NULL,
                is_escalated TINYINT(1) NOT NULL DEFAULT 0,
                escalated_at DATETIME NULL,
                merged_into_ticket_id INT UNSIGNED NULL,
                custom_fields JSON NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_tickets_number (ticket_number),
                KEY idx_tickets_customer_id (customer_id),
                KEY idx_tickets_status (status),
                KEY idx_tickets_priority (priority),
                KEY idx_tickets_assigned_agent (assigned_agent_id),
                KEY idx_tickets_department (department_id),
                KEY idx_tickets_website (website_id),
                KEY idx_tickets_store_view (store_view_id),
                KEY idx_tickets_order (order_id),
                KEY idx_tickets_last_activity (last_activity_at),
                CONSTRAINT fk_tickets_customer FOREIGN KEY (customer_id)
                    REFERENCES customers (id) ON DELETE SET NULL,
                CONSTRAINT fk_tickets_agent FOREIGN KEY (assigned_agent_id)
                    REFERENCES admin_users (id) ON DELETE SET NULL,
                CONSTRAINT fk_tickets_department FOREIGN KEY (department_id)
                    REFERENCES ticket_departments (id) ON DELETE SET NULL,
                CONSTRAINT fk_tickets_category FOREIGN KEY (category_id)
                    REFERENCES ticket_categories (id) ON DELETE SET NULL,
                CONSTRAINT fk_tickets_sla FOREIGN KEY (sla_policy_id)
                    REFERENCES ticket_sla_policies (id) ON DELETE SET NULL,
                CONSTRAINT fk_tickets_order FOREIGN KEY (order_id)
                    REFERENCES orders (id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    },
    'down' => function ($db) {
        $db->statement("DROP TABLE IF EXISTS tickets");
    },
];
