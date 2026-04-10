<?php

return [
    'up' => function ($db) {
        $db->statement("
            ALTER TABLE customer_vehicles
                ADD COLUMN photo_path VARCHAR(500) NULL DEFAULT NULL AFTER notes,
                ADD COLUMN spec_year SMALLINT UNSIGNED NULL DEFAULT NULL AFTER photo_path,
                ADD COLUMN spec_colour VARCHAR(100) NULL DEFAULT NULL AFTER spec_year,
                ADD COLUMN spec_engine_cc SMALLINT UNSIGNED NULL DEFAULT NULL AFTER spec_colour,
                ADD COLUMN spec_mods_summary TEXT NULL DEFAULT NULL AFTER spec_engine_cc
        ");
    },
    'down' => function ($db) {
        $db->statement("
            ALTER TABLE customer_vehicles
                DROP COLUMN IF EXISTS photo_path,
                DROP COLUMN IF EXISTS spec_year,
                DROP COLUMN IF EXISTS spec_colour,
                DROP COLUMN IF EXISTS spec_engine_cc,
                DROP COLUMN IF EXISTS spec_mods_summary
        ");
    },
];
