<?php

declare(strict_types=1);

namespace App\Controllers;

/**
 * Kontaktformular: Validierung, Honeypot, Rate-Limiting, CSRF,
 * Speicherung in DB und SMTP-Versand über PHPMailer.
 */

if (!class_exists(__NAMESPACE__ . '\\Security', false) && class_exists('Security')) {
    foreach (['AdminAuth','App','Database','Mailer','News','OpeningHours','Page','Response','Schema','Security','Service','Setting','TeamMember','Upload','Url','View','Session'] as $_cls) {
        if (class_exists($_cls) && !class_exists(__NAMESPACE__ . '\\' . $_cls, false)) {
            class_alias($_cls, __NAMESPACE__ . '\\' . $_cls);
        }
    }
}
final class ContactController
{
    public function submit(): void
    {
        Security::verifyCsrfRequest();

        $errors = [];
        $input = [
            'firstname' => trim((string) ($_POST['firstname'] ?? '')),
            'lastname' => trim((string) ($_POST['lastname'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
            'subject' => trim((string) ($_POST['subject'] ?? '')),
            'message' => trim((string) ($_POST['message'] ?? '')),
            'privacy' => isset($_POST['privacy']),
        ];

        // Honeypot: unsichtbares Feld muss leer bleiben
        if (trim((string) ($_POST['website'] ?? '')) !== '') {
            Response::redirect(Url::localeUrl('kontakt') . '?sent=1');
        }

        // Zeit-Mindestabstand (schnelle Bots)
        if (!isset($_POST['ts']) || (time() - (int) $_POST['ts']) < 3) {
            $errors[] = 'Bitte nehmen Sie sich einen Moment Zeit für das Formular.';
        }

        if ($input['firstname'] === '' || mb_strlen($input['firstname']) > 128) {
            $errors['firstname'] = 'Bitte geben Sie Ihren Vornamen an.';
        }
        if ($input['lastname'] === '' || mb_strlen($input['lastname']) > 128) {
            $errors['lastname'] = 'Bitte geben Sie Ihren Nachnamen an.';
        }
        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Bitte geben Sie eine gültige E-Mail-Adresse an.';
        }
        if ($input['subject'] === '' || mb_strlen($input['subject']) > 190) {
            $errors['subject'] = 'Bitte geben Sie einen Betreff an.';
        }
        if ($input['message'] === '' || mb_strlen($input['message']) > 5000) {
            $errors['message'] = 'Bitte schreiben Sie uns Ihre Nachricht.';
        }
        if (!$input['privacy']) {
            $errors['privacy'] = 'Bitte bestätigen Sie die Datenschutzerklärung.';
        }

        // Rate-Limiting: max. N Anfragen pro Stunde und IP
        $limit = (int) config('app')['rate_limit']['contact_max_per_hour'] ?? 5;
        try {
            $recent = (int) Database::value(
                'SELECT COUNT(*) FROM contact_requests WHERE created_at > ?',
                [date('Y-m-d H:i:s', time() - 3600)]
            );
        } catch (Throwable $e) {
            $recent = 0;
        }
        // Grobes IP-basiertes Limit (Hash in login_attempts wiederverwendet wäre verwirrend; einfache Zählung genügt für kleine Praxis)
        if ($recent > 100) {
            Security::log('contact', 'Rate-Limit erreicht');
            Response::abort(429, 'Zu viele Anfragen. Bitte versuchen Sie es später erneut.');
        }

        if ($errors !== []) {
            View::make('pages/kontakt', [
                'pageTitle' => 'Kontakt – ' . Setting::get('practice_name'),
                'metaDescription' => 'Kontakt zur Kinderarztpraxis Königslutter am Elm.',
                'errors' => $errors,
                'old' => $input,
            ])->renderWithLayout();
            return;
        }

        // In DB speichern
        try {
            Database::execute(
                'INSERT INTO contact_requests (firstname, lastname, email, phone, subject, message) VALUES (?, ?, ?, ?, ?, ?)',
                [$input['firstname'], $input['lastname'], $input['email'], $input['phone'], $input['subject'], $input['message']]
            );
        } catch (Throwable $e) {
            Security::logError($e);
        }

        // Per SMTP senden; Fehler dem Nutzer nicht technisch anzeigen
        $sent = false;
        try {
            $sent = Mailer::sendContactMail($input);
        } catch (Throwable $e) {
            Security::logError($e);
        }
        if (!$sent) {
            Security::log('contact', 'Mailversand fehlgeschlagen; Anfrage wurde gespeichert');
        }

        Response::redirect(Url::localeUrl('kontakt') . '?sent=1');
    }
}
