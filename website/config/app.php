<?php

declare(strict_types=1);

/**
 * Zentrale Konfiguration. Werte können über .env überschrieben werden.
 */



return [
    'name' => 'Kinderarztpraxis Königslutter am Elm',
    'env' => Env::get('APP_ENV', 'production'),
    'debug' => Env::bool('APP_DEBUG', false),
    'url' => rtrim(Env::get('APP_URL', 'https://www.kinderarzt-koenigslutter.de') ?? '', '/'),
    'locales' => ['de' => 'Deutsch', 'en' => 'English', 'ar' => 'العربية'],
    'default_locale' => 'de',
    'session_name' => Env::get('SESSION_NAME', 'kinderarzt_session'),
    'timezone' => 'Europe/Berlin',
    'upload_max_bytes' => 4 * 1024 * 1024,
    'rate_limit' => [
        'contact_max_per_hour' => 5,
        'login_max_attempts' => 5,
        'login_lock_minutes' => 15,
    ],
];
