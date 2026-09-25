<?php

declare(strict_types=1);

/**
 * HTTP-Response-Helfer inkl. Fehlerseiten im Websitedesign.
 */
final class Response
{
    public static function redirect(string $url, int $status = 302): never
    {
        header('Location: ' . $url, true, $status);
        exit;
    }

    public static function status(int $code): void
    {
        http_response_code($code);
    }

    public static function json(array $data, int $status = 200): never
    {
        header('Content-Type: application/json; charset=utf-8');
        self::status($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function notFound(): never
    {
        self::abort(404);
    }

    public static function abort(int $code, ?string $message = null): never
    {
        http_response_code($code);
        $titles = [
            403 => 'Zugriff nicht möglich',
            404 => 'Seite nicht gefunden',
            500 => 'Ein Fehler ist aufgetreten',
        ];
        $messages = [
            403 => 'Sie haben keine Berechtigung, diese Seite aufzurufen.',
            404 => 'Diese Seite konnten wir leider nicht finden. Vielleicht hilft Ihnen unsere Navigation weiter.',
            500 => 'Es ist ein technischer Fehler aufgetreten. Bitte versuchen Sie es später erneut.',
        ];
        $view = new View('errors/error', [
            'code' => $code,
            'title' => $titles[$code] ?? 'Fehler',
            'message' => $message ?? ($messages[$code] ?? ''),
        ]);
        $view->renderWithLayout();
        exit;
    }
}
