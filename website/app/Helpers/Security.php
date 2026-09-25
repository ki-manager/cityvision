<?php

declare(strict_types=1);

/**
 * Sicherheits- und Ausgabe-Helfer: Escaping, CSRF, Rate-Limiting, Logging.
 */
final class Security
{
    public static function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function csrfToken(): string
    {
        Session::start();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function csrfField(): string
    {
        $token = self::csrfToken();
        return '<input type="hidden" name="_csrf" value="' . self::e($token) . '">';
    }

    public static function csrfCheck(?string $token): bool
    {
        Session::start();
        $expected = $_SESSION['csrf_token'] ?? '';
        return $expected !== '' && is_string($token) && hash_equals($expected, $token);
    }

    public static function verifyCsrfRequest(): void
    {
        if (!self::csrfCheck($_POST['_csrf'] ?? null)) {
            Response::abort(403, 'Ungültige Sitzung. Bitte laden Sie die Seite neu und versuchen Sie es erneut.');
        }
    }

    public static function clientIpHash(): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        return hash('sha256', $ip . '|kinderarzt');
    }

    /**
     * Rate-Limiting über die login_attempts-Tabelle.
     */
    public static function tooManyAttempts(string $key, int $max, int $windowMinutes): bool
    {
        $pdo = Database::tryConn();
        if ($pdo === null) {
            return false;
        }
        $since = date('Y-m-d H:i:s', time() - $windowMinutes * 60);
        $count = (int) Database::value(
            'SELECT COUNT(*) FROM login_attempts WHERE username = ? AND ip_hash = ? AND success = 0 AND created_at > ?',
            [$key, self::clientIpHash(), $since]
        );
        return $count >= $max;
    }

    public static function recordAttempt(string $key, bool $success): void
    {
        if (Database::tryConn() === null) {
            return;
        }
        Database::execute(
            'INSERT INTO login_attempts (username, ip_hash, success) VALUES (?, ?, ?)',
            [$key, self::clientIpHash(), $success ? 1 : 0]
        );
    }

    public static function log(string $channel, string $message, array $context = []): void
    {
        $dir = ROOT_PATH . '/storage/logs';
        if (!is_dir($dir)) {
            return;
        }
        $line = sprintf(
            "[%s] %s: %s %s\n",
            date('Y-m-d H:i:s'),
            $channel,
            $message,
            $context ? json_encode($context, JSON_UNESCAPED_UNICODE) : ''
        );
        @file_put_contents($dir . '/app-' . date('Y-m') . '.log', $line, FILE_APPEND | LOCK_EX);
    }

    public static function logError(Throwable $e): void
    {
        self::log('error', $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
    }
}
