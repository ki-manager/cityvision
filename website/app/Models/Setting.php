<?php

declare(strict_types=1);

/**
 * Settings-Modell mit Datei-Cache (wenige DB-Queries pro Request).
 */
final class Setting
{
    private static ?array $cache = null;

    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }
        $cached = App::cacheGet('settings');
        if (is_array($cached)) {
            return self::$cache = $cached;
        }
        self::$cache = [];
        if (Database::tryConn() !== null) {
            try {
                foreach (Database::fetchAll('SELECT setting_key, setting_value FROM settings') as $row) {
                    self::$cache[$row['setting_key']] = (string) $row['setting_value'];
                }
            } catch (Throwable $e) {
                self::$cache = [];
            }
        }
        App::cacheSet('settings', self::$cache, 300);
        return self::$cache;
    }

    public static function get(string $key, string $default = ''): string
    {
        $all = self::all();
        return $all[$key] ?? $default;
    }

    public static function set(string $key, string $value): void
    {
        Database::execute(
            'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)',
            [$key, $value]
        );
        self::$cache = null;
    }
}
