<?php

declare(strict_types=1);

/**
 * Pages-Modell.
 */
final class Page
{
    public static function find(string $slug, string $locale = 'de'): ?array
    {
        try {
            $row = Database::fetch(
                'SELECT * FROM pages WHERE slug = ? AND locale = ?',
                [$slug, $locale]
            );
            return $row ?: null;
        } catch (Throwable $e) {
            return null;
        }
    }

    public static function all(): array
    {
        return Database::fetchAll('SELECT * FROM pages ORDER BY slug, locale');
    }
}
