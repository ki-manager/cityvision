<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

/**
 * Admin: Kontaktanfragen anzeigen, Status setzen, löschen.
 */

if (!class_exists(__NAMESPACE__ . '\\Security', false) && class_exists('Security')) {
    foreach (['AdminAuth','App','Database','Mailer','News','OpeningHours','Page','Response','Schema','Security','Service','Setting','TeamMember','Upload','Url','View','Session'] as $_cls) {
        if (class_exists($_cls) && !class_exists(__NAMESPACE__ . '\\' . $_cls, false)) {
            class_alias($_cls, __NAMESPACE__ . '\\' . $_cls);
        }
    }
}
final class ContactRequestsController
{
    public function index(): void
    {
        AdminAuth::requireLogin();
        $status = $_GET['status'] ?? '';
        if (in_array($status, ['neu', 'in_bearbeitung', 'erledigt'], true)) {
            $requests = Database::fetchAll(
                'SELECT * FROM contact_requests WHERE status = ? ORDER BY created_at DESC LIMIT 200',
                [$status]
            );
        } else {
            $requests = Database::fetchAll(
                "SELECT * FROM contact_requests WHERE status != 'geloescht' ORDER BY created_at DESC LIMIT 200"
            );
        }
        View::make('admin/contact-requests', [
            'pageTitle' => 'Admin – Kontaktanfragen',
            'requests' => $requests,
            'statusFilter' => $status,
        ])->renderWithAdminLayout();
    }

    public function setStatus(): void
    {
        AdminAuth::requireLogin();
        Security::verifyCsrfRequest();
        $id = (int) ($_POST['id'] ?? 0);
        $status = (string) ($_POST['status'] ?? '');
        if (in_array($status, ['neu', 'in_bearbeitung', 'erledigt', 'geloescht'], true)) {
            Database::execute('UPDATE contact_requests SET status = ? WHERE id = ?', [$status, $id]);
            AdminAuth::audit('contact.status', 'Anfrage #' . $id . ' -> ' . $status);
        }
        Response::redirect(Url::to('admin/contact-requests'));
    }

    public function delete(): void
    {
        AdminAuth::requireLogin();
        Security::verifyCsrfRequest();
        $id = (int) ($_POST['id'] ?? 0);
        Database::execute('DELETE FROM contact_requests WHERE id = ?', [$id]);
        AdminAuth::audit('contact.delete', 'Anfrage #' . $id);
        Response::redirect(Url::to('admin/contact-requests'));
    }
}
