<?php

/** @var string $content */
$practiceName = Setting::get('practice_name', 'Kinderarztpraxis');
$nav = [
    'admin' => ['Dashboard', '📊'],
    'admin/news' => ['Aktuelles', '📰'],
    'admin/pages' => ['Seiten', '📄'],
    'admin/team' => ['Team', '👩‍⚕️'],
    'admin/services' => ['Leistungen', '🩺'],
    'admin/opening-hours' => ['Öffnungszeiten', '🕒'],
    'admin/settings' => ['Einstellungen', '⚙️'],
    'admin/contact-requests' => ['Kontaktanfragen', '✉️'],
    'admin/system-check' => ['System-Check', '🔧'],
];
$current = ltrim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/', '/');
$base = Url::base();
$baseLength = strlen($base);
if ($base !== '' && str_starts_with($current, $base)) {
    $current = ltrim(substr($current, $baseLength), '/');
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title><?= Security::e($pageTitle ?? 'Admin') ?></title>
<link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
</head>
<body class="admin-body">
<aside class="admin-sidebar">
  <p class="admin-brand"><?= Security::e($practiceName) ?></p>
  <nav aria-label="Admin-Navigation">
    <ul>
      <?php foreach ($nav as $route => $item): ?>
        <?php [$label, $icon] = $item; ?>
        <li><a href="<?= Url::to($route) ?>" class="<?= $current === $route ? 'active' : '' ?>"><span aria-hidden="true"><?= $icon ?></span> <?= Security::e($label) ?></a></li>
      <?php endforeach; ?>
    </ul>
  </nav>
  <p class="admin-footer-nav"><a href="<?= Url::to('') ?>">← Website</a> &middot; <a href="<?= Url::to('admin/logout') ?>">Logout</a></p>
</aside>
<main class="admin-main">
<?= $content ?>
</main>
<script src="<?= asset('js/admin.js') ?>" defer></script>
</body>
</html>
