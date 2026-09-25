<?php

declare(strict_types=1);

/**
 * CLI-Helfer: legt einen Admin-Benutzer an (alternativ zum Installer).
 * Verwendung: php scripts/create-admin.php <benutzername> <email> <passwort> [name]
 */

define('ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . '/app/bootstrap.php';

if (PHP_SAPI !== 'cli' || $argc < 4) {
    fwrite(STDERR, "Verwendung: php scripts/create-admin.php <benutzername> <email> <passwort> [name]\n");
    exit(1);
}

[, $username, $email, $password, $name] = array_pad($argv, 5, '');

if (strlen($password) < 10) {
    fwrite(STDERR, "Fehler: Passwort muss mindestens 10 Zeichen haben.\n");
    exit(1);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Fehler: ungültige E-Mail-Adresse.\n");
    exit(1);
}

Database::execute(
    'INSERT INTO users (username, email, password_hash, name, role) VALUES (?, ?, ?, ?, \'admin\')
     ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), active = 1, role = \'admin\'',
    [$username, $email, password_hash($password, PASSWORD_DEFAULT), $name]
);

echo "Admin-Benutzer '$username' angelegt.\n";
