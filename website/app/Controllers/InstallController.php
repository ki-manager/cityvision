<?php

declare(strict_types=1);

namespace App\Controllers;

/**
 * Installation-Wizard: DB-Test, Tabellen anlegen, Admin-Account erstellen.
 * Nach Abschluss wird storage/installed.lock geschrieben und der
 * Installer deaktiviert sich selbst.
 */

if (!class_exists(__NAMESPACE__ . '\\Security', false) && class_exists('Security')) {
    foreach (['AdminAuth','App','Database','Mailer','News','OpeningHours','Page','Response','Schema','Security','Service','Setting','TeamMember','Upload','Url','View','Session'] as $_cls) {
        if (class_exists($_cls) && !class_exists(__NAMESPACE__ . '\\' . $_cls, false)) {
            class_alias($_cls, __NAMESPACE__ . '\\' . $_cls);
        }
    }
}
final class InstallController
{
    private function isInstalled(): bool
    {
        return is_file(ROOT_PATH . '/storage/installed.lock');
    }

    public function show(): void
    {
        if ($this->isInstalled()) {
            View::make('errors/simple', [
                'title' => 'Installation bereits abgeschlossen',
                'message' => 'Die Installation wurde bereits durchgeführt. Bitte löschen Sie storage/installed.lock, um erneut zu installieren.',
            ])->renderWithLayout();
            return;
        }

        $dbOk = Database::tryConn() !== null;
        $tablesOk = false;
        if ($dbOk) {
            try {
                $count = (int) Database::value('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = \'settings\'');
                $tablesOk = $count === 1;
            } catch (Throwable $e) {
            }
        }

        View::make('install/index', [
            'pageTitle' => 'Installation – Kinderarztpraxis Königslutter',
            'metaDescription' => '',
            'dbOk' => $dbOk,
            'tablesOk' => $tablesOk,
            'errors' => [],
            'adminCreated' => false,
        ])->renderWithLayout();
    }

    public function run(): void
    {
        if ($this->isInstalled()) {
            Response::redirect(Url::to('/'));
        }
        Security::verifyCsrfRequest();

        $errors = [];
        $action = (string) ($_POST['action'] ?? '');

        if ($action === 'create_tables') {
            $schemaFile = ROOT_PATH . '/database/schema.sql';
            if (!is_file($schemaFile)) {
                $errors[] = 'schema.sql nicht gefunden.';
            } elseif (Database::tryConn() === null) {
                $errors[] = 'Keine Datenbankverbindung. Bitte .env prüfen.';
            } else {
                try {
                    Database::conn()->exec((string) file_get_contents($schemaFile));
                } catch (Throwable $e) {
                    $errors[] = 'Fehler beim Anlegen der Tabellen: ' . $e->getMessage();
                }
            }
        }

        if ($action === 'seed') {
            $seedFile = ROOT_PATH . '/database/seed.sql';
            if (!is_file($seedFile)) {
                $errors[] = 'seed.sql nicht gefunden.';
            } elseif (Database::tryConn() === null) {
                $errors[] = 'Keine Datenbankverbindung.';
            } else {
                try {
                    Database::conn()->exec((string) file_get_contents($seedFile));
                } catch (Throwable $e) {
                    $errors[] = 'Fehler beim Import: ' . $e->getMessage();
                }
            }
        }

        if ($action === 'create_admin') {
            $username = trim((string) ($_POST['username'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $name = trim((string) ($_POST['name'] ?? ''));

            if ($username === '' || mb_strlen($username) < 3) {
                $errors[] = 'Benutzername: mindestens 3 Zeichen.';
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Bitte eine gültige E-Mail-Adresse angeben.';
            }
            if (strlen($password) < 10) {
                $errors[] = 'Passwort: mindestens 10 Zeichen.';
            }
            if ($errors === [] && Database::tryConn() !== null) {
                try {
                    Database::execute(
                        'INSERT INTO users (username, email, password_hash, name, role) VALUES (?, ?, ?, ?, \'admin\')',
                        [$username, $email, password_hash($password, PASSWORD_DEFAULT), $name]
                    );
                    file_put_contents(ROOT_PATH . '/storage/installed.lock', date('c'));
                } catch (Throwable $e) {
                    $errors[] = 'Fehler: ' . $e->getMessage();
                }
            }
        }

        $dbOk = Database::tryConn() !== null;
        $tablesOk = false;
        try {
            $tablesOk = $dbOk && (int) Database::value('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = \'settings\'') === 1;
        } catch (Throwable $e) {
        }

        View::make('install/index', [
            'pageTitle' => 'Installation – Kinderarztpraxis Königslutter',
            'metaDescription' => '',
            'dbOk' => $dbOk,
            'tablesOk' => $tablesOk,
            'errors' => $errors,
            'adminCreated' => $action === 'create_admin' && $errors === [],
        ])->renderWithLayout();
    }
}
