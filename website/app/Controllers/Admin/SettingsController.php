<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

/**
 * Admin: Einstellungen (Kontaktdaten, Doctolib, Sprachen, Social).
 */

if (!class_exists(__NAMESPACE__ . '\\Security', false) && class_exists('Security')) {
    foreach (['AdminAuth','App','Database','Mailer','News','OpeningHours','Page','Response','Schema','Security','Service','Setting','TeamMember','Upload','Url','View','Session'] as $_cls) {
        if (class_exists($_cls) && !class_exists(__NAMESPACE__ . '\\' . $_cls, false)) {
            class_alias($_cls, __NAMESPACE__ . '\\' . $_cls);
        }
    }
}
final class SettingsController
{
    private const KEYS = [
        'practice_name', 'practice_owner', 'street', 'zip_city', 'phone', 'phone_intl',
        'fax', 'email', 'doctolib_url', 'languages_spoken', 'emergency_note', 'footer_note', 'og_image',
    ];

    public function index(): void
    {
        AdminAuth::requireLogin();
        View::make('admin/settings', [
            'pageTitle' => 'Admin – Einstellungen',
            'settings' => Setting::all(),
            'errors' => [],
            'saved' => false,
        ])->renderWithAdminLayout();
    }

    public function save(): void
    {
        AdminAuth::requireLogin();
        Security::verifyCsrfRequest();

        $errors = [];
        $phone = trim((string) ($_POST['phone'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $doctolib = trim((string) ($_POST['doctolib_url'] ?? ''));

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL) && !str_contains($email, 'TODO')) {
            $errors[] = 'Bitte eine gültige E-Mail-Adresse angeben.';
        }
        if ($doctolib !== '' && !filter_var($doctolib, FILTER_VALIDATE_URL)) {
            $errors[] = 'Doctolib-Link ist keine gültige URL.';
        }

        if ($errors !== []) {
            View::make('admin/settings', [
                'pageTitle' => 'Admin – Einstellungen',
                'settings' => Setting::all(),
                'errors' => $errors,
                'saved' => false,
            ])->renderWithAdminLayout();
            return;
        }

        foreach (self::KEYS as $key) {
            if (isset($_POST[$key])) {
                Setting::set($key, mb_substr(trim((string) $_POST[$key]), 0, 2000));
            }
        }
        AdminAuth::audit('settings.save', 'Einstellungen aktualisiert');

        View::make('admin/settings', [
            'pageTitle' => 'Admin – Einstellungen',
            'settings' => Setting::all(),
            'errors' => [],
            'saved' => true,
        ])->renderWithAdminLayout();
    }
}
