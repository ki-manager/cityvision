<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

/**
 * Admin: Seitentexte (pages) bearbeiten.
 */

if (!class_exists(__NAMESPACE__ . '\\Security', false) && class_exists('Security')) {
    foreach (['AdminAuth','App','Database','Mailer','News','OpeningHours','Page','Response','Schema','Security','Service','Setting','TeamMember','Upload','Url','View','Session'] as $_cls) {
        if (class_exists($_cls) && !class_exists(__NAMESPACE__ . '\\' . $_cls, false)) {
            class_alias($_cls, __NAMESPACE__ . '\\' . $_cls);
        }
    }
}
final class PagesController
{
    public function index(): void
    {
        AdminAuth::requireLogin();
        View::make('admin/pages/index', [
            'pageTitle' => 'Admin – Seiten',
            'pages' => Database::fetchAll('SELECT id, slug, locale, title, updated_at FROM pages ORDER BY slug, locale'),
        ])->renderWithAdminLayout();
    }

    public function edit(array $params): void
    {
        AdminAuth::requireLogin();
        $page = Database::fetch('SELECT * FROM pages WHERE slug = ? AND locale = ?', [$params['slug'], 'de']);
        if ($page === null) {
            Response::notFound();
        }
        View::make('admin/pages/form', [
            'pageTitle' => 'Admin – Seite bearbeiten',
            'page' => $page,
            'errors' => [],
        ])->renderWithAdminLayout();
    }

    public function save(): void
    {
        AdminAuth::requireLogin();
        Security::verifyCsrfRequest();

        $id = (int) ($_POST['id'] ?? 0);
        $title = trim((string) ($_POST['title'] ?? ''));
        $meta = mb_substr(trim((string) ($_POST['meta_description'] ?? '')), 0, 320);
        $content = (string) ($_POST['content'] ?? '');

        if ($title === '') {
            $page = Database::fetch('SELECT * FROM pages WHERE id = ?', [$id]);
            View::make('admin/pages/form', [
                'pageTitle' => 'Admin – Seite bearbeiten',
                'page' => $page ?: ['id' => $id, 'title' => $title, 'meta_description' => $meta, 'content' => $content],
                'errors' => ['Bitte einen Seitentitel angeben.'],
            ])->renderWithAdminLayout();
            return;
        }

        Database::execute(
            'UPDATE pages SET title = ?, meta_description = ?, content = ? WHERE id = ?',
            [$title, $meta, $content, $id]
        );
        AdminAuth::audit('page.save', 'Page #' . $id);
        Response::redirect(Url::to('admin/pages'));
    }
}
