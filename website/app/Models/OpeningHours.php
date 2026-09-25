<?php

declare(strict_types=1);

/**
 * Öffnungszeiten-Modell (aus DB, nicht hart im Template).
 */
final class OpeningHours
{
    private static ?array $cache = null;

    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }
        self::$cache = [];
        if (Database::tryConn() !== null) {
            try {
                self::$cache = Database::fetchAll('SELECT * FROM opening_hours ORDER BY sort_order');
            } catch (Throwable $e) {
                self::$cache = [];
            }
        }
        return self::$cache;
    }

    public static function weekdays(): array
    {
        return [1 => 'Montag', 2 => 'Dienstag', 3 => 'Mittwoch', 4 => 'Donnerstag', 5 => 'Freitag', 6 => 'Samstag', 7 => 'Sonntag'];
    }

    public static function formatRange(?string $from, ?string $to, string $note = ''): string
    {
        if ($from === null || $to === null) {
            return $note;
        }
        $text = substr($from, 0, 5) . ' – ' . substr($to, 0, 5) . ' Uhr';
        return $note !== '' ? $text . ' (' . $note . ')' : $text;
    }

    /**
     * JSON für Schema.org OpeningHoursSpecification.
     */
    public static function schemaSpecs(): array
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $specs = [];
        foreach (self::all() as $row) {
            if ((int) $row['closed'] === 1) {
                continue;
            }
            $day = $days[(int) $row['weekday'] - 1] ?? null;
            if ($day === null) {
                continue;
            }
            if ($row['morning_from'] !== null && $row['morning_to'] !== null) {
                $specs[] = ['dayOfWeek' => $day, 'opens' => substr((string) $row['morning_from'], 0, 5), 'closes' => substr((string) $row['morning_to'], 0, 5)];
            }
            if ($row['afternoon_from'] !== null && $row['afternoon_to'] !== null) {
                $specs[] = ['dayOfWeek' => $day, 'opens' => substr((string) $row['afternoon_from'], 0, 5), 'closes' => substr((string) $row['afternoon_to'], 0, 5)];
            }
        }
        return $specs;
    }
}
