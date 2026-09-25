<?php

declare(strict_types=1);

/**
 * Front-Controller: Kinderarztpraxis Königslutter am Elm
 * Zentrale Einstiegsdatei; alle Requests laufen über .htaccess hierher.
 */

define('ROOT_PATH', dirname(__DIR__));
define('START_TIME', microtime(true));

require ROOT_PATH . '/app/bootstrap.php';

// ---- HTTPS erzwingen (vermeidet Schleifen hinter goneo-SSL-Proxies) ----
if (!isRequestHttps() && php_sapi_name() !== 'cli') {
    $httpsUrl = 'https://' . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '/');
    header('Location: ' . $httpsUrl, true, 301);
    exit;
}

// ---- www / non-www kanonisieren (APP_URL ist Maßstab) ----
$appUrl = App::url();
$expectedHost = strtolower((string) (parse_url($appUrl, PHP_URL_HOST) ?: ''));
if ($expectedHost !== '' && php_sapi_name() !== 'cli') {
    $host = strtolower(preg_replace('/:\d+$/', '', (string) ($_SERVER['HTTP_HOST'] ?? '')));
    if ($host !== '' && $host !== $expectedHost) {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $query = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_QUERY) ?? '');
        header('Location: ' . $appUrl . $path . ($query !== '' ? '?' . $query : ''), true, 301);
        exit;
    }
}

// ---- Route ermitteln ----
$basePath = Url::base();
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if ($basePath !== '' && str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath));
}

$router = new Router();
$localePattern = '(?P<locale>de|en|ar)';
$lp = '#^' . $localePattern . '(?:/|$)#'; // Hilfs-Regex-Vorlage

// ---- Systemrouten ----
$router->get('/sitemap\.xml', [App\Controllers\SitemapController::class, 'show']);
$router->get('/robots\.txt', [App\Controllers\RobotsController::class, 'show']);

// ---- Öffentliche Seiten (de ohne Präfix, en/ar mit Präfix) ----
$publicRoutes = static function (Router $router, string $prefix = ''): void {
    $router->get($prefix . '/', [App\Controllers\PageController::class, 'home']);
    $router->get($prefix . '/praxis', [App\Controllers\PageController::class, 'praxis']);
    $router->get($prefix . '/team', [App\Controllers\PageController::class, 'team']);
    $router->get($prefix . '/team/(?P<slug>[a-z0-9-]+)', [App\Controllers\PageController::class, 'teamDetail']);
    $router->get($prefix . '/leistungen', [App\Controllers\PageController::class, 'leistungen']);
    $router->get($prefix . '/vorsorge', [App\Controllers\PageController::class, 'vorsorge']);
    $router->get($prefix . '/impfungen', [App\Controllers\PageController::class, 'impfungen']);
    $router->get($prefix . '/informationen', [App\Controllers\PageController::class, 'informationen']);
    $router->get($prefix . '/aktuelles', [App\Controllers\PageController::class, 'aktuelles']);
    $router->get($prefix . '/kontakt', [App\Controllers\PageController::class, 'kontakt']);
    $router->post($prefix . '/kontakt', [App\Controllers\ContactController::class, 'submit']);
    $router->get($prefix . '/impressum', [App\Controllers\PageController::class, 'impressum']);
    $router->get($prefix . '/datenschutz', [App\Controllers\PageController::class, 'datenschutz']);
};

$publicRoutes($router);
$publicRoutes($router, '/(?P<locale>en|ar)');
$router->get('/(?P<locale>en|ar)', [App\Controllers\PageController::class, 'home']);

// ---- Installer ----
$router->get('/install', [App\Controllers\InstallController::class, 'show']);
$router->post('/install', [App\Controllers\InstallController::class, 'run']);

// ---- Adminbereich ----
$router->get('/admin/login', [App\Controllers\Admin\AuthController::class, 'loginForm']);
$router->post('/admin/login', [App\Controllers\Admin\AuthController::class, 'login']);
$router->get('/admin/logout', [App\Controllers\Admin\AuthController::class, 'logout']);
$router->get('/admin', [App\Controllers\Admin\DashboardController::class, 'index']);
$router->get('/admin/news', [App\Controllers\Admin\NewsController::class, 'index']);
$router->get('/admin/news/new', [App\Controllers\Admin\NewsController::class, 'create']);
$router->get('/admin/news/(?P<id>\d+)/edit', [App\Controllers\Admin\NewsController::class, 'edit']);
$router->post('/admin/news/save', [App\Controllers\Admin\NewsController::class, 'save']);
$router->post('/admin/news/toggle', [App\Controllers\Admin\NewsController::class, 'toggle']);
$router->post('/admin/news/delete', [App\Controllers\Admin\NewsController::class, 'delete']);
$router->get('/admin/pages', [App\Controllers\Admin\PagesController::class, 'index']);
$router->get('/admin/pages/(?P<slug>[a-z0-9-]+)/edit', [App\Controllers\Admin\PagesController::class, 'edit']);
$router->post('/admin/pages/save', [App\Controllers\Admin\PagesController::class, 'save']);
$router->get('/admin/team', [App\Controllers\Admin\TeamController::class, 'index']);
$router->get('/admin/team/new', [App\Controllers\Admin\TeamController::class, 'create']);
$router->get('/admin/team/(?P<id>\d+)/edit', [App\Controllers\Admin\TeamController::class, 'edit']);
$router->post('/admin/team/save', [App\Controllers\Admin\TeamController::class, 'save']);
$router->post('/admin/team/toggle', [App\Controllers\Admin\TeamController::class, 'toggle']);
$router->post('/admin/team/delete', [App\Controllers\Admin\TeamController::class, 'delete']);
$router->get('/admin/services', [App\Controllers\Admin\ServicesController::class, 'index']);
$router->get('/admin/services/new', [App\Controllers\Admin\ServicesController::class, 'create']);
$router->get('/admin/services/(?P<id>\d+)/edit', [App\Controllers\Admin\ServicesController::class, 'edit']);
$router->post('/admin/services/save', [App\Controllers\Admin\ServicesController::class, 'save']);
$router->post('/admin/services/toggle', [App\Controllers\Admin\ServicesController::class, 'toggle']);
$router->post('/admin/services/delete', [App\Controllers\Admin\ServicesController::class, 'delete']);
$router->get('/admin/opening-hours', [App\Controllers\Admin\OpeningHoursController::class, 'index']);
$router->post('/admin/opening-hours/save', [App\Controllers\Admin\OpeningHoursController::class, 'save']);
$router->get('/admin/settings', [App\Controllers\Admin\SettingsController::class, 'index']);
$router->post('/admin/settings/save', [App\Controllers\Admin\SettingsController::class, 'save']);
$router->get('/admin/contact-requests', [App\Controllers\Admin\ContactRequestsController::class, 'index']);
$router->post('/admin/contact-requests/status', [App\Controllers\Admin\ContactRequestsController::class, 'setStatus']);
$router->post('/admin/contact-requests/delete', [App\Controllers\Admin\ContactRequestsController::class, 'delete']);
$router->post('/admin/smtp-test', [App\Controllers\Admin\SmtpTestController::class, 'test']);
$router->get('/admin/system-check', [App\Controllers\Admin\SystemCheckController::class, 'index']);

// ---- Redirects alter URLs (301) ----
$redirects = require ROOT_PATH . '/config/redirects.php';
foreach ($redirects as $old => $new) {
    $router->get(preg_quote($old, '#'), fn () => Response::redirect(Url::to($new), 301));
}

// ---- Fehlende Route -> 404 ----
$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $uri);
