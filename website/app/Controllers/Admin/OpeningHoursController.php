<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

/**
 * Admin: Öffnungszeiten pflegen.
 */

if (!class_exists(__NAMESPACE__ . '\\Security', false) && class_exists('Security')) {
    foreach (['AdminAuth','App','Database','Mailer','News','OpeningHours','Page','Response','Schema','Security','Service','Setting','TeamMember','Upload','Url','View','Session'] as $_cls) {
        if (class_exists($_cls) && !class_exists(__NAMESPACE__ . '\\' . $_cls, false)) {
            class_alias($_cls, __NAMESPACE__ . '\\' . $_cls);
        }
    }
}
final class OpeningHoursController
{
    public function index(): void
    {
        AdminAuth::requireLogin();
        View::make('admin/opening-hours', [
            'pageTitle' => 'Admin – Öffnungszeiten',
            'hours' => Database::fetchAll('SELECT * FROM opening_hours ORDER BY sort_order'),
            'errors' => [],
        ])->renderWithAdminLayout();
    }

    public function save(): void
    {
        AdminAuth::requireLogin();
        Security::verifyCsrfRequest();
        $errors = [];

        $days = (array) ($_POST['day'] ?? []);
        foreach ($days as $weekday => $data) {
            $weekday = (int) $weekday;
            if ($weekday < 1 || $weekday > 7) {
                continue;
            }
            $closed = (($data['closed'] ?? '') === 'on') ? 1 : 0;
            $note = mb_substr(trim((string) ($data['note'] ?? '')), 0, 190);

            $mf = self::timeOrNull($data['morning_from'] ?? null);
            $mt = self::timeOrNull($data['morning_to'] ?? null);
            $af = self::timeOrNull($data['afternoon_from'] ?? null);
            $at = self::timeOrNull($data['afternoon_to'] ?? null);

            if ($closed === 0 && $mf === null && $af === null) {
                $errors[] = 'Tag ' . $weekday . ': Bitte Zeiten angeben oder als geschlossen markieren.';
                continue;
            }

            Database::execute(
                'INSERT INTO opening_hours (weekday, morning_from, morning_to, afternoon_from, afternoon_to, closed, note, sort_order)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE
                   morning_from = VALUES(morning_from), morning_to = VALUES(morning_to),
                   afternoon_from = VALUES(afternoon_from), afternoon_to = VALUES(afternoon_to),
                   closed = VALUES(closed), note = VALUES(note), sort_order = VALUES(sort_order)',
                [$weekday, $mf, $mt, $af, $at, $closed, $note, $weekday]
            );
        }

        App::cacheSet('settings', [], 0);
        AdminAuth::audit('opening_hours.save', 'Öffnungszeiten aktualisiert');

        View::make('admin/opening-hours', [
            'pageTitle' => 'Admin – Öffnungszeiten',
            'hours' => Database::fetchAll('SELECT * FROM opening_hours ORDER BY sort_order'),
            'errors' => $errors,
        ])->renderWithAdminLayout();
    }

    private static function timeOrNull(mixed $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }
        return preg_match('/^\d{2}:\d{2}$/', $value) ? $value . ':00' : null;
    }
}
