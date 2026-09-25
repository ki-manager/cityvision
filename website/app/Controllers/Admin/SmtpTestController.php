<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

/**
 * Admin: SMTP-Verbindung testen (inkl. optionalem Testversand).
 * Passwörter werden nie angezeigt.
 */

if (!class_exists(__NAMESPACE__ . '\\Security', false) && class_exists('Security')) {
    foreach (['AdminAuth','App','Database','Mailer','News','OpeningHours','Page','Response','Schema','Security','Service','Setting','TeamMember','Upload','Url','View','Session'] as $_cls) {
        if (class_exists($_cls) && !class_exists(__NAMESPACE__ . '\\' . $_cls, false)) {
            class_alias($_cls, __NAMESPACE__ . '\\' . $_cls);
        }
    }
}
final class SmtpTestController
{
    public function test(): void
    {
        AdminAuth::requireLogin();
        Security::verifyCsrfRequest();

        $recipient = trim((string) ($_POST['recipient'] ?? ''));
        $errors = [];
        if ($recipient !== '' && !filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Bitte eine gültige Empfängeradresse angeben.';
        }

        $result = $errors === [] ? Mailer::testConnection($recipient) : ['ok' => false, 'steps' => [], 'error' => implode(' ', $errors)];

        View::make('admin/smtp-test', [
            'pageTitle' => 'Admin – SMTP-Test',
            'result' => $result,
        ])->renderWithAdminLayout();
    }
}
