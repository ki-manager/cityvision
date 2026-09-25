<?php

/** @var string $content */
$locale = App::locale();
$practiceName = Setting::get('practice_name', 'Kinderarztpraxis Königslutter am Elm');
$pageTitle = $pageTitle ?? $practiceName;
$metaDescription = $metaDescription ?? '';
$noindex = $noindex ?? false;
$hideHeaderFooter = $hideHeaderFooter ?? false;
$canonicalPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$canonical = rtrim(App::url(), '/') . ($locale === 'de' ? $canonicalPath : '/' . $locale . $canonicalPath);
?>
<!DOCTYPE html>
<html lang="<?= Security::e($locale) ?>" dir="<?= App::dir() ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= Security::e($pageTitle) ?></title>
<meta name="description" content="<?= Security::e($metaDescription) ?>">
<?php if ($noindex): ?>
<meta name="robots" content="noindex,nofollow">
<?php else: ?>
<link rel="canonical" href="<?= Security::e($canonical) ?>">
<?php endif; ?>
<meta property="og:title" content="<?= Security::e($pageTitle) ?>">
<meta property="og:description" content="<?= Security::e($metaDescription) ?>">
<meta property="og:url" content="<?= Security::e($canonical) ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= Security::e($practiceName) ?>">
<meta property="og:image" content="<?= Security::e(App::url()) ?>/<?= Security::e(Setting::get('og_image', 'assets/images/og-default.svg')) ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="theme-color" content="#167D8D">
<link rel="icon" href="<?= asset('icons/favicon.svg') ?>" type="image/svg+xml">
<link rel="apple-touch-icon" href="<?= asset('icons/apple-touch-icon.png') ?>">
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">
<script type="application/ld+json"><?= Schema::medicalClinic($practiceName, $metaDescription) ?></script>
</head>
<body>
<a class="skip-link" href="#main">Zum Inhalt springen</a>
<?php if (!$hideHeaderFooter): ?>
<?php echo (new View('partials/header'))->render(); ?>
<?php endif; ?>
<main id="main">
<?= $content ?>
</main>
<?php if (!$hideHeaderFooter): ?>
<?php echo (new View('partials/footer'))->render(); ?>
<?php endif; ?>
<?php if (!$hideHeaderFooter): ?>
<div class="mobile-action-bar" aria-hidden="false">
  <a href="tel:<?= Security::e(Setting::get('phone_intl', '+4953535511')) ?>" class="mobile-action call"><span class="icon">📞</span>Anrufen</a>
  <a href="<?= Security::e(Setting::get('doctolib_url')) ?>" class="mobile-action termin" target="_blank" rel="noopener">Termin</a>
  <a href="https://www.openstreetmap.org/?mlat=52.2640&mlon=10.8170#map=18/52.2640/10.8170" class="mobile-action route" target="_blank" rel="noopener">Route</a>
</div>
<?php endif; ?>
<script src="<?= asset('js/app.js') ?>" defer></script>
</body>
</html>
