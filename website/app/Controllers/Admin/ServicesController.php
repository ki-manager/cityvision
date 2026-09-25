<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

/**
 * Admin: Leistungen (Services) verwalten.
 */

if (!class_exists(__NAMESPACE__ . '\\Security', false) && class_exists('Security')) {
    foreach (['AdminAuth','App','Database','Mailer','News','OpeningHours','Page','Response','Schema','Security','Service','Setting','TeamMember','Upload','Url','View','Session'] as $_cls) {
        if (class_exists($_cls) && !class_exists(__NAMESPACE__ . '\\' . $_cls, false)) {
            class_alias($_cls, __NAMESPACE__ . '\\' . $_cls);
        }
    }
}
final class ServicesController
{
    public function index(): void
    {
        AdminAuth::requireLogin();
        View::make('admin/services/index', [
            'pageTitle' => 'Admin – Leistungen',
            'services' => Database::fetchAll('SELECT * FROM services ORDER BY group_key, sort_order, id'),
        ])->renderWithAdminLayout();
    }

    public function create(): void
    {
        AdminAuth::requireLogin();
        View::make('admin/services/form', [
            'pageTitle' => 'Admin – Neue Leistung',
            'item' => null,
            'errors' => [],
        ])->renderWithAdminLayout();
    }

    public function edit(array $params): void
    {
        AdminAuth::requireLogin();
        $item = Service::find((int) $params['id']);
        if ($item === null) {
            Response::notFound();
        }
        View::make('admin/services/form', [
            'pageTitle' => 'Admin – Leistung bearbeiten',
            'item' => $item,
            'errors' => [],
        ])->renderWithAdminLayout();
    }

    public function save(): void
    {
        AdminAuth::requireLogin();
        Security::verifyCsrfRequest();

        $id = isset($_POST['id']) ? (int) $_POST['id'] : null;
        $data = [
            'slug' => NewsController::slugify((string) ($_POST['slug'] ?? '')),
            'group_key' => (string) ($_POST['group_key'] ?? 'beratung'),
            'title' => trim((string) ($_POST['title'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'icon' => preg_replace('/[^a-z0-9-]/', '', (string) ($_POST['icon'] ?? 'stethoscope')) ?: 'stethoscope',
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
            'active' => isset($_POST['active']) ? 1 : 0,
        ];
        if (!array_key_exists($data['group_key'], Service::GROUPS)) {
            $data['group_key'] = 'beratung';
        }

        if ($data['title'] === '') {
            View::make('admin/services/form', [
                'pageTitle' => 'Admin – Leistung',
                'item' => array_merge(['id' => $id], $data),
                'errors' => ['Bitte einen Titel angeben.'],
            ])->renderWithAdminLayout();
            return;
        }

        try {
            $savedId = Service::save($data, $id);
            AdminAuth::audit('service.save', 'Service #' . $savedId);
        } catch (Throwable $e) {
            Security::logError($e);
            Response::abort(500, 'Speichern fehlgeschlagen (Slug bereits vergeben?)');
        }
        Response::redirect(Url::to('admin/services'));
    }

    public function toggle(): void
    {
        AdminAuth::requireLogin();
        Security::verifyCsrfRequest();
        $id = (int) ($_POST['id'] ?? 0);
        Database::execute('UPDATE services SET active = 1 - active WHERE id = ?', [$id]);
        AdminAuth::audit('service.toggle', 'Service #' . $id);
        Response::redirect(Url::to('admin/services'));
    }

    public function delete(): void
    {
        AdminAuth::requireLogin();
        Security::verifyCsrfRequest();
        $id = (int) ($_POST['id'] ?? 0);
        Service::delete($id);
        AdminAuth::audit('service.delete', 'Service #' . $id);
        Response::redirect(Url::to('admin/services'));
    }
}
