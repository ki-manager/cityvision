<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

/**
 * Admin: Aktuelles (News) verwalten.
 */

if (!class_exists(__NAMESPACE__ . '\\Security', false) && class_exists('Security')) {
    foreach (['AdminAuth','App','Database','Mailer','News','OpeningHours','Page','Response','Schema','Security','Service','Setting','TeamMember','Upload','Url','View','Session'] as $_cls) {
        if (class_exists($_cls) && !class_exists(__NAMESPACE__ . '\\' . $_cls, false)) {
            class_alias($_cls, __NAMESPACE__ . '\\' . $_cls);
        }
    }
}
final class NewsController
{
    public function index(): void
    {
        AdminAuth::requireLogin();
        View::make('admin/news/index', [
            'pageTitle' => 'Admin – Aktuelles',
            'news' => News::all(),
        ])->renderWithAdminLayout();
    }

    public function create(): void
    {
        AdminAuth::requireLogin();
        View::make('admin/news/form', [
            'pageTitle' => 'Admin – Neue Meldung',
            'item' => null,
            'errors' => [],
        ])->renderWithAdminLayout();
    }

    public function edit(array $params): void
    {
        AdminAuth::requireLogin();
        $item = News::find((int) $params['id']);
        if ($item === null) {
            Response::notFound();
        }
        View::make('admin/news/form', [
            'pageTitle' => 'Admin – Meldung bearbeiten',
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
            'title' => trim((string) ($_POST['title'] ?? '')),
            'slug' => trim((string) ($_POST['slug'] ?? '')),
            'summary' => trim((string) ($_POST['summary'] ?? '')),
            'content' => trim((string) ($_POST['content'] ?? '')),
            'publish_date' => (string) ($_POST['publish_date'] ?? date('Y-m-d')),
            'start_date' => (string) ($_POST['start_date'] ?? ''),
            'end_date' => (string) ($_POST['end_date'] ?? ''),
            'important' => isset($_POST['important']) ? 1 : 0,
            'active' => isset($_POST['active']) ? 1 : 0,
        ];

        $errors = [];
        if ($data['title'] === '') {
            $errors[] = 'Bitte einen Titel angeben.';
        }
        if ($data['slug'] === '') {
            $data['slug'] = self::slugify($data['title']);
        } else {
            $data['slug'] = self::slugify($data['slug']);
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['publish_date'])) {
            $errors[] = 'Ungültiges Veröffentlichungsdatum.';
        }

        if ($errors !== []) {
            View::make('admin/news/form', [
                'pageTitle' => 'Admin – Meldung',
                'item' => array_merge(['id' => $id], $data),
                'errors' => $errors,
            ])->renderWithAdminLayout();
            return;
        }

        try {
            $savedId = News::save($data, $id);
            AdminAuth::audit('news.save', 'News #' . $savedId);
        } catch (Throwable $e) {
            Security::logError($e);
            $errors[] = 'Speichern fehlgeschlagen (Slug bereits vergeben?)';
            View::make('admin/news/form', [
                'pageTitle' => 'Admin – Meldung',
                'item' => array_merge(['id' => $id], $data),
                'errors' => $errors,
            ])->renderWithAdminLayout();
            return;
        }
        Response::redirect(Url::to('admin/news'));
    }

    public function toggle(): void
    {
        AdminAuth::requireLogin();
        Security::verifyCsrfRequest();
        $id = (int) ($_POST['id'] ?? 0);
        Database::execute('UPDATE news SET active = 1 - active WHERE id = ?', [$id]);
        AdminAuth::audit('news.toggle', 'News #' . $id);
        Response::redirect(Url::to('admin/news'));
    }

    public function delete(): void
    {
        AdminAuth::requireLogin();
        Security::verifyCsrfRequest();
        $id = (int) ($_POST['id'] ?? 0);
        News::delete($id);
        AdminAuth::audit('news.delete', 'News #' . $id);
        Response::redirect(Url::to('admin/news'));
    }

    public static function slugify(string $text): string
    {
        $text = strtolower($text);
        $text = preg_replace('/[^\p{L}\p{Nd}]+/u', '-', $text) ?? '';
        return trim($text, '-') ?: 'meldung-' . date('Ymd-His');
    }
}
