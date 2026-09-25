<?php

declare(strict_types=1);

/**
 *optionale Kompatibilitäts-/Hilfsfunktionen.
 */

function config(string $key): array
{
    return App::config($key);
}

function isRequestHttps(): bool
{
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return true;
    }
    if (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') {
        return true;
    }
    if (($_SERVER['SERVER_PORT'] ?? '') == 443) {
        return true;
    }
    return false;
}

function e(?string $value): string
{
    return Security::e($value);
}

function url(string $path = ''): string
{
    return Url::to($path);
}

function asset(string $path): string
{
    return Url::asset($path);
}

function t(string $key, ?string $fallback = null): string
{
    return App::t($key, $fallback);
}

function nl2br_esc(?string $text): string
{
    return nl2br(Security::e($text));
}

/**
 * Rendert erlaubtes Mini-Markdown der pages/news (##, -, Aufzählungen).
 * Kein Roh-HTML aus der DB in die Ausgabe.
 */
function render_safe_text(?string $text): string
{
    if ($text === null || $text === '') {
        return '';
    }
    $html = '';
    $lines = explode("\n", str_replace("\r\n", "\n", $text));
    $inList = false;
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '') {
            if ($inList) { $html .= "</ul>\n"; $inList = false; }
            continue;
        }
        if (str_starts_with($trimmed, '## ')) {
            if ($inList) { $html .= "</ul>\n"; $inList = false; }
            $html .= '<h2>' . Security::e(substr($trimmed, 3)) . "</h2>\n";
        } elseif (str_starts_with($trimmed, '- ')) {
            if (!$inList) { $html .= "<ul>\n"; $inList = true; }
            $html .= '<li>' . Security::e(substr($trimmed, 2)) . "</li>\n";
        } else {
            if ($inList) { $html .= "</ul>\n"; $inList = false; }
            $html .= '<p>' . Security::e($trimmed) . "</p>\n";
        }
    }
    if ($inList) {
        $html .= "</ul>\n";
    }
    // TODO-Marker hervorheben (redaktioneller Platzhalter, kein Fehler)
    return $html;
}
