<?php

$locale = App::locale();
$navItems = [
    ['/' . ($locale !== 'de' ? $locale . '/' : ''), 'nav.home', false],
    ['/' . ($locale !== 'de' ? $locale . '/' : '') . 'praxis', 'nav.praxis', false],
    ['/' . ($locale !== 'de' ? $locale . '/' : '') . 'team', 'nav.team', false],
    ['/' . ($locale !== 'de' ? $locale . '/' : '') . 'leistungen', 'nav.leistungen', false],
    ['/' . ($locale !== 'de' ? $locale . '/' : '') . 'vorsorge', 'nav.vorsorge', false],
    ['/' . ($locale !== 'de' ? $locale . '/' : '') . 'impfungen', 'nav.impfungen', false],
    ['/' . ($locale !== 'de' ? $locale . '/' : '') . 'informationen', 'nav.informationen', false],
    ['/' . ($locale !== 'de' ? $locale . '/' : '') . 'kontakt', 'nav.kontakt', false],
];
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
?>
<header class="site-header">
  <div class="header-top">
    <div class="container header-top-inner">
      <p class="header-languages"><?= Security::e(App::t('header.languages', 'Wir sprechen: Deutsch, Englisch, Polnisch, Arabisch, Russisch')) ?></p>
      <nav class="locale-switch" aria-label="Sprache wählen">
        <?php foreach (config('app')['locales'] as $code => $label): ?>
          <a href="<?= Url::localeUrl($currentPath === '/' ? '' : substr($currentPath, strlen(Url::base())), $code) ?>"
             lang="<?= Security::e($code) ?>"
             <?= $code === $locale ? 'aria-current="true"' : '' ?>><?= Security::e($code) ?></a>
        <?php endforeach; ?>
      </nav>
    </div>
  </div>
  <div class="header-main">
    <div class="container header-main-inner">
      <a class="brand" href="<?= Url::localeUrl('') ?>">
        <span class="brand-mark" aria-hidden="true">
          <svg viewBox="0 0 48 48" width="42" height="42"><circle cx="24" cy="24" r="22" fill="#167D8D"/><path d="M16 26c2 6 14 6 16 0" stroke="#fff" stroke-width="3" fill="none" stroke-linecap="round"/><circle cx="17" cy="18" r="2.4" fill="#fff"/><circle cx="31" cy="18" r="2.4" fill="#fff"/></svg>
        </span>
        <span class="brand-text">
          <span class="brand-name">Kinderarztpraxis<br>Königslutter am Elm</span>
          <span class="brand-sub">Kinder- und Jugendmedizin</span>
        </span>
      </a>
      <button class="nav-toggle" aria-expanded="false" aria-controls="main-nav" aria-label="Menü öffnen">
        <span></span><span></span><span></span>
      </button>
      <nav id="main-nav" class="main-nav" aria-label="Hauptnavigation">
        <ul>
          <?php foreach ($navItems as [$href, $key, $child]): ?>
            <li><a href="<?= Security::e($href) ?>" <?= str_starts_with($currentPath, rtrim($href, '/')) && $href !== Url::localeUrl('') ? 'aria-current="page"' : '' ?>><?= Security::e(App::t($key)) ?></a></li>
          <?php endforeach; ?>
        </ul>
        <a class="btn btn-primary nav-cta" href="<?= Security::e(Setting::get('doctolib_url', '#')) ?>" target="_blank" rel="noopener"><?= Security::e(App::t('cta.termin', 'Termin buchen')) ?></a>
      </nav>
    </div>
  </div>
</header>
