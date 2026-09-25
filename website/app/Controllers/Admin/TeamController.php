<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

/**
 * Admin: Team verwalten (inkl. Bild-Upload).
 */

if (!class_exists(__NAMESPACE__ . '\\Security', false) && class_exists('Security')) {
    foreach (['AdminAuth','App','Database','Mailer','News','OpeningHours','Page','Response','Schema','Security','Service','Setting','TeamMember','Upload','Url','View','Session'] as $_cls) {
        if (class_exists($_cls) && !class_exists(__NAMESPACE__ . '\\' . $_cls, false)) {
            class_alias($_cls, __NAMESPACE__ . '\\' . $_cls);
        }
    }
}
final class TeamController
{
    public function index(): void
    {
        AdminAuth::requireLogin();
        View::make('admin/team/index', [
            'pageTitle' => 'Admin – Team',
            'team' => TeamMember::all(),
        ])->renderWithAdminLayout();
    }

    public function create(): void
    {
        AdminAuth::requireLogin();
        View::make('admin/team/form', [
            'pageTitle' => 'Admin – Neues Teammitglied',
            'item' => null,
            'errors' => [],
        ])->renderWithAdminLayout();
    }

    public function edit(array $params): void
    {
        AdminAuth::requireLogin();
        $item = TeamMember::find((int) $params['id']);
        if ($item === null) {
            Response::notFound();
        }
        View::make('admin/team/form', [
            'pageTitle' => 'Admin – Teammitglied bearbeiten',
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
            'slug' => trim((string) ($_POST['slug'] ?? '')),
            'name' => trim((string) ($_POST['name'] ?? '')),
            'role' => trim((string) ($_POST['role'] ?? '')),
            'role_type' => in_array($_POST['role_type'] ?? '', ['arzt', 'mitarbeitende'], true) ? $_POST['role_type'] : 'mitarbeitende',
            'specialty' => trim((string) ($_POST['specialty'] ?? '')),
            'qualifications' => trim((string) ($_POST['qualifications'] ?? '')) ?: null,
            'bio' => trim((string) ($_POST['bio'] ?? '')) ?: null,
            'photo' => trim((string) ($_POST['photo_current'] ?? '')),
            'sort_order' => (int) ($_POST['sort_order'] ?? 0),
            'active' => isset($_POST['active']) ? 1 : 0,
        ];

        $errors = [];
        if ($data['name'] === '') {
            $errors[] = 'Bitte einen Namen angeben.';
        }
        if ($data['slug'] === '') {
            $data['slug'] = NewsController::slugify($data['name']);
        }

        // Bild-Upload (optional)
        if (isset($_FILES['photo']) && ($_FILES['photo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $upload = Upload::image($_FILES['photo']);
            if ($upload['ok']) {
                $data['photo'] = $upload['filename'];
            } else {
                $errors[] = $upload['error'];
            }
        }

        if ($errors !== []) {
            View::make('admin/team/form', [
                'pageTitle' => 'Admin – Teammitglied',
                'item' => array_merge(['id' => $id], $data),
                'errors' => $errors,
            ])->renderWithAdminLayout();
            return;
        }

        try {
            $savedId = TeamMember::save($data, $id);
            AdminAuth::audit('team.save', 'Team #' . $savedId);
        } catch (Throwable $e) {
            Security::logError($e);
            Response::abort(500, 'Speichern fehlgeschlagen (Slug bereits vergeben?)');
        }
        Response::redirect(Url::to('admin/team'));
    }

    public function toggle(): void
    {
        AdminAuth::requireLogin();
        Security::verifyCsrfRequest();
        $id = (int) ($_POST['id'] ?? 0);
        Database::execute('UPDATE team SET active = 1 - active WHERE id = ?', [$id]);
        AdminAuth::audit('team.toggle', 'Team #' . $id);
        Response::redirect(Url::to('admin/team'));
    }

    public function delete(): void
    {
        AdminAuth::requireLogin();
        Security::verifyCsrfRequest();
        $id = (int) ($_POST['id'] ?? 0);
        TeamMember::delete($id);
        AdminAuth::audit('team.delete', 'Team #' . $id);
        Response::redirect(Url::to('admin/team'));
    }
}
