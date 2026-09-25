<?php

declare(strict_types=1);

/**
 * Bootstrap: Autoloading, Konfiguration, Fehlerbehandlung.
 */

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', ROOT_PATH . '/storage/logs/php-error.log');

// Autoloader (PSR-4-artig für App\ und einzelne Klassen im app/-Baum)
spl_autoload_register(static function (string $class): void {
    $prefixes = [
        'App\\Controllers\\' => ROOT_PATH . '/app/Controllers/',
        'App\\Models\\' => ROOT_PATH . '/app/Models/',
        'App\\Services\\' => ROOT_PATH . '/app/Services/',
        'App\\Helpers\\' => ROOT_PATH . '/app/Helpers/',
        'App\\Middleware\\' => ROOT_PATH . '/app/Middleware/',
    ];
    foreach ($prefixes as $prefix => $dir) {
        if (str_starts_with($class, $prefix)) {
            $file = $dir . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
            if (is_file($file)) {
                require $file;
            }
            return;
        }
    }
    // Kernklassen ohne Namensraum (Router, Env, App, Security, ...)
    $candidates = [
        ROOT_PATH . '/app/' . $class . '.php',
    ];
    foreach (['Helpers', 'Models', 'Services', 'Controllers', 'Middleware'] as $dir) {
        $candidates[] = ROOT_PATH . '/app/' . $dir . '/' . $class . '.php';
    }
    foreach ($candidates as $file) {
        if (is_file($file)) {
            require $file;
            return;
        }
    }
});

// Composer-Autoload (falls vorhanden, z. B. PHPMailer über Composer installiert)
if (is_file(ROOT_PATH . '/vendor/autoload.php')) {
    require ROOT_PATH . '/vendor/autoload.php';
} else {
    // PHPMailer manuell einbinden (für FTP-Upload ohne Composer)
    $pmDir = ROOT_PATH . '/vendor/phpmailer/phpmailer/src/';
    if (is_file($pmDir . 'PHPMailer.php')) {
        require $pmDir . 'PHPMailer.php';
        require $pmDir . 'SMTP.php';
        require $pmDir . 'Exception.php';
    }
}

require ROOT_PATH . '/app/compat.php';
require ROOT_PATH . '/app/Helpers/Env.php';

Env::load(ROOT_PATH . '/.env');
App::boot();

// Globaler Fehlerhandler: technische Details nur ins Log, nie in die Ausgabe
set_exception_handler(static function (Throwable $e): void {
    Security::logError($e);
    if (php_sapi_name() === 'cli') {
        fwrite(STDERR, 'Error: ' . $e->getMessage() . PHP_EOL);
        exit(1);
    }
    if (!headers_sent()) {
        http_response_code(500);
    }
    if (App::debug()) {
        echo '<pre>' . Security::e($e->getMessage()) . "\n" . Security::e($e->getTraceAsString()) . '</pre>';
        exit;
    }
    Response::abort(500);
});

set_error_handler(static function (int $no, string $str, string $file, int $line): bool {
    if (!(error_reporting() & $no)) {
        return false;
    }
    throw new ErrorException($str, 0, $no, $file, $line);
});
