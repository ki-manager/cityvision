<?php

declare(strict_types=1);

/**
 * Mailer-Service auf Basis von PHPMailer (SMTP only).
 * mail() wird bewusst nicht als Fallback verwendet.
 */
final class Mailer
{
    public static function factory(): PHPMailer\PHPMailer\PHPMailer
    {
        if (!class_exists(PHPMailer\PHPMailer\PHPMailer::class)) {
            throw new RuntimeException('PHPMailer ist nicht installiert (vendor/phpmailer fehlt).');
        }
        $cfg = config('mail');
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $cfg['host'];
        $mail->Port = $cfg['port'];
        $mail->SMTPAuth = true;
        $mail->Username = $cfg['username'];
        $mail->Password = $cfg['password'];
        if (in_array(strtolower((string) $cfg['encryption']), ['tls', 'ssl'], true)) {
            $mail->SMTPSecure = strtolower((string) $cfg['encryption']);
        }
        $mail->CharSet = 'UTF-8';
        $mail->Timeout = 15;
        return $mail;
    }

    public static function sendContactMail(array $input): bool
    {
        $cfg = config('mail');
        $to = $cfg['to'] !== '' ? $cfg['to'] : $cfg['from'];
        if ($to === '' || $cfg['from'] === '') {
            Security::log('mail', 'MAIL_FROM/MAIL_TO nicht konfiguriert');
            return false;
        }

        $mail = self::factory();
        $mail->setFrom($cfg['from'], $cfg['from_name']);
        $mail->addAddress($to);
        $mail->addReplyTo($input['email'], $input['firstname'] . ' ' . $input['lastname']);
        $mail->Subject = '[Website] ' . $input['subject'];
        $mail->isHTML(false);
        $mail->Body = "Neue Kontaktanfrage über die Website:\n\n"
            . 'Name: ' . $input['firstname'] . ' ' . $input['lastname'] . "\n"
            . 'E-Mail: ' . $input['email'] . "\n"
            . 'Telefon: ' . ($input['phone'] ?: '-') . "\n\n"
            . "Nachricht:\n" . $input['message'] . "\n";
        return $mail->send();
    }

    /**
     * SMTP-Verbindungstest für den Adminbereich (SMTP-Verbindung +
     * Authentifizierung + optionaler Testversand). Gibt Ergebnis-Array zurück.
     */
    public static function testConnection(string $testRecipient): array
    {
        $result = ['ok' => false, 'steps' => [], 'error' => null];
        try {
            $mail = self::factory();
            $result['steps'][] = 'SMTP-Host konfiguriert: ' . config('mail')['host'] . ':' . config('mail')['port'];

            // Verbindung + Auth prüfen (SMTP connect)
            $smtp = $mail->getSMTPInstance();
            $smtp->Timeout = 10;
            $connected = $smtp->connect(
                $mail->Host,
                $mail->Port,
                $mail->Timeout,
                []
            );
            if (!$connected) {
                throw new RuntimeException('Verbindung zum SMTP-Server fehlgeschlagen.');
            }
            $result['steps'][] = 'Verbindung hergestellt';

            if (!$smtp->hello('')) {
                throw new RuntimeException('HELO/EHLO fehlgeschlagen.');
            }
            $result['steps'][] = 'EHLO erfolgreich';

            if (config('mail')['encryption'] === 'tls') {
                if (!$smtp->startTLS()) {
                    throw new RuntimeException('STARTTLS fehlgeschlagen: ' . $smtp->getError()['error']);
                }
                $result['steps'][] = 'STARTTLS aktiv';
            }
            if (!$smtp->authenticate($mail->Username, $mail->Password)) {
                $err = $smtp->getError();
                throw new RuntimeException('Authentifizierung fehlgeschlagen: ' . ($err['error'] ?? 'unbekannt'));
            }
            $result['steps'][] = 'Authentifizierung erfolgreich';
            $smtp->close(true);

            if ($testRecipient !== '') {
                $mail2 = self::factory();
                $mail2->setFrom(config('mail')['from'], config('mail')['from_name']);
                $mail2->addAddress($testRecipient);
                $mail2->Subject = 'SMTP-Test kinderarzt-koenigslutter.de';
                $mail2->Body = 'Diese Testmail bestätigt, dass der SMTP-Versand korrekt konfiguriert ist.';
                if (!$mail2->send()) {
                    throw new RuntimeException('Testmail konnte nicht gesendet werden.');
                }
                $result['steps'][] = 'Testmail versendet an ' . $testRecipient;
            }
            $result['ok'] = true;
        } catch (Throwable $e) {
            $result['error'] = $e->getMessage();
            Security::log('mail', 'SMTP-Test fehlgeschlagen', ['error' => $e->getMessage()]);
        }
        return $result;
    }
}
