<?php

return [
    'up' => function ($db) {
        $db->statement("ALTER TABLE ticket_mailboxes ADD COLUMN domain_id INT UNSIGNED NULL AFTER department_id");
        $db->statement("ALTER TABLE ticket_mailboxes ADD COLUMN use_global_smtp TINYINT(1) NOT NULL DEFAULT 1 AFTER domain_id");

        $db->statement("ALTER TABLE ticket_mailboxes MODIFY smtp_host VARCHAR(255) NULL");
        $db->statement("ALTER TABLE ticket_mailboxes MODIFY smtp_port SMALLINT UNSIGNED NULL");
        $db->statement("ALTER TABLE ticket_mailboxes MODIFY smtp_username VARCHAR(255) NULL");
        $db->statement("ALTER TABLE ticket_mailboxes MODIFY smtp_password VARCHAR(255) NULL");

        $db->statement("ALTER TABLE ticket_mailboxes ADD KEY idx_ticket_mailboxes_domain (domain_id)");
        $db->statement("ALTER TABLE ticket_mailboxes ADD CONSTRAINT fk_ticket_mailboxes_domain FOREIGN KEY (domain_id) REFERENCES store_domains (id) ON DELETE SET NULL");
    },
    'down' => function ($db) {
        $db->statement("ALTER TABLE ticket_mailboxes DROP FOREIGN KEY fk_ticket_mailboxes_domain");
        $db->statement("ALTER TABLE ticket_mailboxes DROP KEY idx_ticket_mailboxes_domain");

        $db->statement("ALTER TABLE ticket_mailboxes MODIFY smtp_host VARCHAR(255) NOT NULL");
        $db->statement("ALTER TABLE ticket_mailboxes MODIFY smtp_port SMALLINT UNSIGNED NOT NULL DEFAULT 587");
        $db->statement("ALTER TABLE ticket_mailboxes MODIFY smtp_username VARCHAR(255) NOT NULL");
        $db->statement("ALTER TABLE ticket_mailboxes MODIFY smtp_password VARCHAR(255) NOT NULL");

        $db->statement("ALTER TABLE ticket_mailboxes DROP COLUMN use_global_smtp");
        $db->statement("ALTER TABLE ticket_mailboxes DROP COLUMN domain_id");
    },
];
