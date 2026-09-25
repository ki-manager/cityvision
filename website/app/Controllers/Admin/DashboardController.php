<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

/**
 * Admin-Dashboard.
 */

if (!class_exists(__NAMESPACE__ . '\\Security', false) && class_exists('Security')) {
    foreach (['AdminAuth','App','Database','Mailer','News','OpeningHours','Page','Response','Schema','Security','Service','Setting','TeamMember','Upload','Url','View','Session'] as $_cls) {
        if (class_exists($_cls) && !class_exists(__NAMESPACE__ . '\\' . $_cls, false)) {
            class_alias($_cls, __NAMESPACE__ . '\\' . $_cls);
        }
    }
}
final class DashboardController
{
    public function index(): void
    {
        AdminAuth::requireLogin();
        $stats = [
            'news' => (int) Database::value('SELECT COUNT(*) FROM news'),
            'news_active' => (int) Database::value('SELECT COUNT(*) FROM news WHERE active = 1'),
            'contact_new' => (int) Database::value("SELECT COUNT(*) FROM contact_requests WHERE status = 'neu'"),
            'team' => (int) Database::value('SELECT COUNT(*) FROM team WHERE active = 1'),
        ];
        View::make('admin/dashboard', [
            'pageTitle' => 'Admin – Dashboard',
            'stats' => $stats,
        ])->renderWithAdminLayout();
    }
}
