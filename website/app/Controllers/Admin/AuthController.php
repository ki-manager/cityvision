<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

/**
 * Admin-Authentifizierung mit Brute-Force-Schutz und Session-Regeneration.
 */

if (!class_exists(__NAMESPACE__ . '\\Security', false) && class_exists('Security')) {
    foreach (['AdminAuth','App','Database','Mailer','News','OpeningHours','Page','Response','Schema','Security','Service','Setting','TeamMember','Upload','Url','View','Session'] as $_cls) {
        if (class_exists($_cls) && !class_exists(__NAMESPACE__ . '\\' . $_cls, false)) {
            class_alias($_cls, __NAMESPACE__ . '\\' . $_cls);
        }
    }
}
final class AuthController
{
    public function loginForm(): void
    {
        if (AdminAuth::check()) {
            Response::redirect(Url::to('admin'));
        }
        View::make('admin/login', [
            'pageTitle' => 'Admin-Login',
            'errors' => [],
        ])->renderWithLayout(['noindex' => true, 'hideHeaderFooter' => true]);
    }

    public function login(): void
    {
        Security::verifyCsrfRequest();
        $app = config('app');
        $max = (int) ($app['rate_limit']['login_max_attempts'] ?? 5);
        $lock = (int) ($app['rate_limit']['login_lock_minutes'] ?? 15);

        $login = trim((string) ($_POST['login'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($login === '' || $password === '') {
            View::make('admin/login', ['pageTitle' => 'Admin-Login', 'errors' => ['Bitte Benutzername/E-Mail und Passwort eingeben.']])
                ->renderWithLayout(['noindex' => true, 'hideHeaderFooter' => true]);
            return;
        }

        if (Security::tooManyAttempts($login, $max, $lock)) {
            Security::log('auth', 'Login gesperrt (Rate-Limit)', ['login' => $login]);
            View::make('admin/login', ['pageTitle' => 'Admin-Login', 'errors' => ['Zu viele fehlgeschlagene Versuche. Bitte versuchen Sie es in ' . $lock . ' Minuten erneut.']])
                ->renderWithLayout(['noindex' => true, 'hideHeaderFooter' => true]);
            return;
        }

        $user = Database::fetch(
            'SELECT * FROM users WHERE (username = ? OR email = ?) AND active = 1 LIMIT 1',
            [$login, $login]
        );

        if ($user !== null && password_verify($password, $user['password_hash'])) {
            Security::recordAttempt($login, true);
            Session::start();
            Session::regenerate();
            $_SESSION['admin_user_id'] = (int) $user['id'];
            $_SESSION['admin_role'] = $user['role'];
            Database::execute('UPDATE users SET last_login_at = NOW() WHERE id = ?', [$user['id']]);
            try {
                Database::execute(
                    'INSERT INTO audit_log (user_id, action, details, ip_hash) VALUES (?, \'login\', ?, ?)',
                    [$user['id'], 'Anmeldung', Security::clientIpHash()]
                );
            } catch (Throwable $e) {
            }
            Response::redirect(Url::to('admin'));
        }

        Security::recordAttempt($login, false);
        Security::log('auth', 'Login fehlgeschlagen', ['login' => $login]);
        View::make('admin/login', ['pageTitle' => 'Admin-Login', 'errors' => ['Benutzername/E-Mail oder Passwort ist falsch.']])
            ->renderWithLayout(['noindex' => true, 'hideHeaderFooter' => true]);
    }

    public function logout(): void
    {
        Session::start();
        Session::destroy();
        Response::redirect(Url::to('admin/login'));
    }
}
