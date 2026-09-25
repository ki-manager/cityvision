<?php

declare(strict_types=1);

/**
 * Admin-Authentifizierungs-Prüfung (Middleware).
 */
final class AdminAuth
{
    public static function check(): bool
    {
        Session::start();
        return isset($_SESSION['admin_user_id']);
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            Response::redirect(Url::to('admin/login'));
        }
    }

    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (($_SESSION['admin_role'] ?? '') !== 'admin') {
            Response::abort(403);
        }
    }

    public static function userId(): int
    {
        return (int) ($_SESSION['admin_user_id'] ?? 0);
    }

    public static function audit(string $action, string $details = ''): void
    {
        try {
            Database::execute(
                'INSERT INTO audit_log (user_id, action, details, ip_hash) VALUES (?, ?, ?, ?)',
                [self::userId(), $action, mb_substr($details, 0, 500), Security::clientIpHash()]
            );
        } catch (Throwable $e) {
        }
    }
}
