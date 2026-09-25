<?php

declare(strict_types=1);

/**
 * Anwendungszustand: Konfiguration, Locale, Translation-Lookup.
 */
final class App
{
    private static array $config = [];
    private static string $locale = '';
    private static ?array $translations = null;

    public static function boot(): void
    {
        foreach (glob(ROOT_PATH . '/config/*.php') ?: [] as $file) {
            self::$config[basename($file, '.php')] = require $file;
        }
        date_default_timezone_set(self::config('app')['timezone'] ?? 'Europe/Berlin');
        mb_internal_encoding('UTF-8');
    }

    public static function config(string $key): array
    {
        return self::$config[$key] ?? [];
    }

    public static function debug(): bool
    {
        return (bool) (self::config('app')['debug'] ?? false);
    }

    public static function url(): string
    {
        return (string) (self::config('app')['url'] ?? '');
    }

    public static function setLocale(string $locale): void
    {
        $allowed = array_keys(self::config('app')['locales'] ?? ['de' => '']);
        self::$locale = in_array($locale, $allowed, true) ? $locale : 'de';
    }

    public static function locale(): string
    {
        if (self::$locale === '') {
            self::$locale = self::config('app')['default_locale'] ?? 'de';
        }
        return self::$locale;
    }

    public static function dir(): string
    {
        return self::locale() === 'ar' ? 'rtl' : 'ltr';
    }

    /**
     * Übersetzungsschlüssel; medizinische Inhalte werden nicht
     * automatisch übersetzt – nur UI-Strings aus der translations-Tabelle.
     */
    public static function t(string $key, ?string $fallback = null): string
    {
        if (self::$translations === null) {
            self::$translations = [];
            if (Database::tryConn() !== null) {
                try {
                    foreach (Database::fetchAll(
                        'SELECT `key`, `value` FROM translations WHERE locale = ?',
                        [self::locale()]
                    ) as $row) {
                        self::$translations[$row['key']] = $row['value'];
                    }
                } catch (Throwable $e) {
                }
            }
        }
        return self::$translations[$key] ?? $fallback ?? $key;
    }

    /**
     * Einfacher Datei-Cache (storage/cache), nur wo sinnvoll.
     */
    public static function cacheGet(string $key): ?array
    {
        $file = ROOT_PATH . '/storage/cache/' . sha1($key) . '.json';
        if (!is_file($file)) {
            return null;
        }
        $data = json_decode((string) file_get_contents($file), true);
        if (!is_array($data) || ($data['expires'] ?? 0) < time()) {
            @unlink($file);
            return null;
        }
        return $data['value'] ?? null;
    }

    public static function cacheSet(string $key, array $value, int $ttl = 300): void
    {
        $file = ROOT_PATH . '/storage/cache/' . sha1($key) . '.json';
        @file_put_contents($file, json_encode([
            'expires' => time() + $ttl,
            'value' => $value,
        ], JSON_UNESCAPED_UNICODE), LOCK_EX);
    }
}
