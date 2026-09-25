<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

/**
 * Admin: System-Check (Health) – PHP, DB, Schreibrechte, SMTP, Extensions, HTTPS.
 * Zeigt keine Passwörter.
 */

if (!class_exists(__NAMESPACE__ . '\\Security', false) && class_exists('Security')) {
    foreach (['AdminAuth','App','Database','Mailer','News','OpeningHours','Page','Response','Schema','Security','Service','Setting','TeamMember','Upload','Url','View','Session'] as $_cls) {
        if (class_exists($_cls) && !class_exists(__NAMESPACE__ . '\\' . $_cls, false)) {
            class_alias($_cls, __NAMESPACE__ . '\\' . $_cls);
        }
    }
}
final class SystemCheckController
{
    public function index(): void
    {
        AdminAuth::requireLogin();

        $required = ['pdo_mysql', 'mbstring', 'openssl', 'fileinfo', 'json', 'curl', 'session'];
        $extensions = [];
        foreach ($required as $ext) {
            $extensions[$ext] = extension_loaded($ext);
        }

        $dbOk = Database::tryConn() !== null;

        $writable = [
            'storage/logs' => is_writable(ROOT_PATH . '/storage/logs'),
            'storage/cache' => is_writable(ROOT_PATH . '/storage/cache'),
            'public/uploads' => is_writable(ROOT_PATH . '/public/uploads'),
        ];

        $mail = config('mail');
        $smtpConfigured = $mail['host'] !== '' && $mail['username'] !== '' && $mail['from'] !== '';

        $checks = [
            'PHP-Version' => PHP_VERSION . ' (' . (version_compare(PHP_VERSION, '8.2.0', '>=') ? '≥ 8.2 OK' : 'zu alt!') . ')',
            'Datenbankverbindung' => $dbOk ? 'OK' : 'fehlgeschlagen',
            'HTTPS' => isRequestHttps() ? 'aktiv' : 'nicht aktiv',
            'SMTP konfiguriert' => $smtpConfigured ? 'ja (' . Security::e($mail['host']) . ':' . $mail['port'] . ')' : 'nein – .env prüfen',
        ];

        View::make('admin/system-check', [
            'pageTitle' => 'Admin – System-Check',
            'checks' => $checks,
            'extensions' => $extensions,
            'writable' => $writable,
        ])->renderWithAdminLayout();
    }
}
