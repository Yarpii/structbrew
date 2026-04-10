<?php

return [
    'up' => function ($db) {
        $db->statement("
            ALTER TABLE customer_vehicles
                ADD COLUMN mileage_km INT UNSIGNED NULL DEFAULT NULL AFTER nickname,
                ADD COLUMN service_interval_km INT UNSIGNED NULL DEFAULT NULL AFTER mileage_km,
                ADD COLUMN last_service_km INT UNSIGNED NULL DEFAULT NULL AFTER service_interval_km,
                ADD COLUMN notes TEXT NULL DEFAULT NULL AFTER last_service_km
        ");
    },
    'down' => function ($db) {
        $db->statement("
            ALTER TABLE customer_vehicles
                DROP COLUMN IF EXISTS mileage_km,
                DROP COLUMN IF EXISTS service_interval_km,
                DROP COLUMN IF EXISTS last_service_km,
                DROP COLUMN IF EXISTS notes
        ");
    },
];
