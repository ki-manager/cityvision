<?php

declare(strict_types=1);

/**
 * Datenbank-Konfiguration (goneo: MySQL/MariaDB, utf8mb4).
 */

return [
    'host' => Env::get('DB_HOST', 'localhost'),
    'port' => (int) (Env::get('DB_PORT', '3306') ?? '3306'),
    'database' => Env::get('DB_DATABASE', ''),
    'username' => Env::get('DB_USERNAME', ''),
    'password' => Env::get('DB_PASSWORD', ''),
    'charset' => Env::get('DB_CHARSET', 'utf8mb4') ?? 'utf8mb4',
];
