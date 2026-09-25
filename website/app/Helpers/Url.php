<?php

declare(strict_types=1);

/**
 * URL- und Pfad-Helfer.
 */
final class Url
{
    /**
     * Basis-Pfad der Anwendung unterhalb des Hosts.
     * Leer, wenn Docroot = public/; andernfalls automatisch erkannt,
     * dass die Seite über /public/ ausgeliefert wird.
     */
    public static function base(): string
    {
        static $base = null;
        if ($base !== null) {
            return $base;
        }
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        if ($script !== '' && str_contains($script, '/public/')) {
            $base = rtrim(substr($script, 0, strpos($script, '/public/') + strlen('/public')), '/');
        } else {
            $base = '';
        }
        return $base;
    }

    public static function to(string $path = ''): string
    {
        $path = ltrim($path, '/');
        $prefix = self::base();
        return ($prefix !== '' ? $prefix . '/' : '/') . $path;
    }

    public static function asset(string $path): string
    {
        return self::to('assets/' . ltrim($path, '/'));
    }

    public static function localeUrl(string $path = '', ?string $locale = null): string
    {
        $locale ??= App::locale();
        if ($locale === '' || $locale === config('app')['default_locale']) {
            return self::to($path);
        }
        return self::to($locale . '/' . ltrim($path, '/'));
    }
}
