<?php

declare(strict_types=1);

/**
 * SMTP-Konfiguration (goneo: smtp.goneo.de, Port 587, STARTTLS).
 * Versand ausschließlich über PHPMailer; mail() ist kein Fallback im Frontend.
 */

return [
    'host' => Env::get('SMTP_HOST', 'smtp.goneo.de'),
    'port' => (int) (Env::get('SMTP_PORT', '587') ?? '587'),
    'username' => Env::get('SMTP_USERNAME', ''),
    'password' => Env::get('SMTP_PASSWORD', ''),
    'encryption' => Env::get('SMTP_ENCRYPTION', 'tls') ?? 'tls',
    'from' => Env::get('MAIL_FROM', ''),
    'from_name' => Env::get('MAIL_FROM_NAME', 'Kinderarztpraxis Königslutter'),
    'to' => Env::get('MAIL_TO', ''),
];
