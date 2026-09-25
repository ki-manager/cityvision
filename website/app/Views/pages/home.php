<?php

$base = '/' . (App::locale() !== 'de' ? App::locale() . '/' : '');
?>
<section class="hero">
  <div class="container hero-grid">
    <div class="hero-text">
      <h1><?= Security::e(App::t('hero.title', 'Gesund groß werden.')) ?></h1>
      <p class="hero-sub"><?= Security::e(App::t('hero.subtitle', 'Kinder- und Jugendmedizin mit Erfahrung, Zeit und Herz in Königslutter am Elm.')) ?></p>
      <div class="hero-doctors">
        <?php foreach ($doctors as $doc): ?>
          <div class="hero-doctor">
            <span class="hero-doctor-name"><?= Security::e($doc['name']) ?></span>
            <span class="hero-doctor-role"><?= Security::e($doc['role']) ?></span>
            <?php if ($doc['specialty'] !== ''): ?>
              <span class="hero-doctor-spec"><?= Security::e($doc['specialty']) ?></span>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="hero-actions">
        <a class="btn btn-primary" href="<?= Security::e(Setting::get('doctolib_url', '#')) ?>" target="_blank" rel="noopener"><?= Security::e(App::t('cta.termin', 'Termin online buchen')) ?></a>
        <a class="btn btn-outline" href="tel:<?= Security::e(Setting::get('phone_intl', '+4953535511')) ?>"><?= Security::e(App::t('cta.call', 'Praxis anrufen')) ?></a>
      </div>
    </div>
    <div class="hero-visual" aria-hidden="true">
      <img src="<?= asset('images/hero-kinderarzt.svg') ?>" alt="" width="560" height="420" fetchpriority="high">
    </div>
  </div>
  <div class="hero-wave" aria-hidden="true"><svg viewBox="0 0 1440 90" preserveAspectRatio="none"><path d="M0,40 C240,90 480,0 720,40 C960,80 1200,20 1440,50 L1440,90 L0,90 Z" fill="#FFFFFF"/></svg></div>
</section>

<?php if ($important !== []): ?>
<section class="container" aria-label="Wichtige Hinweise">
  <?php foreach ($important as $note): ?>
    <div class="alert-banner" role="status">
      <span class="alert-icon" aria-hidden="true">ℹ️</span>
      <div>
        <strong><?= Security::e($note['title']) ?></strong>
        <p><?= Security::e($note['summary']) ?></p>
        <a href="<?= Security::e($base . 'aktuelles') ?>">Mehr erfahren</a>
      </div>
    </div>
  <?php endforeach; ?>
</section>
<?php endif; ?>

<section class="quick-access" aria-label="Schnellzugriff">
  <div class="container quick-grid">
    <a class="quick-card" href="<?= Security::e(Setting::get('doctolib_url', '#')) ?>" target="_blank" rel="noopener">
      <span class="quick-icon" aria-hidden="true">📅</span>
      <h2><?= Security::e(App::t('quick.termin', 'Termin buchen')) ?></h2>
      <p>Doctolib</p>
    </a>
    <div class="quick-card">
      <span class="quick-icon" aria-hidden="true">🕒</span>
      <h2><?= Security::e(App::t('quick.zeiten', 'Öffnungszeiten')) ?></h2>
      <?php foreach (OpeningHours::all() as $h): if ((int) $h['weekday'] > 5) continue; ?>
        <p class="quick-hours-row"><span><?= Security::e(OpeningHours::weekdays()[(int) $h['weekday']]) ?></span>
        <span><?= Security::e(OpeningHours::formatRange($h['morning_from'], $h['morning_to'], (string) ($h['note'] ?? ''))) ?>
        <?= $h['afternoon_from'] !== null ? ' · ' . Security::e(OpeningHours::formatRange($h['afternoon_from'], $h['afternoon_to'])) : '' ?></span></p>
      <?php endforeach; ?>
    </div>
    <a class="quick-card" href="<?= Security::e($base . 'informationen') ?>">
      <span class="quick-icon" aria-hidden="true">🩺</span>
      <h2><?= Security::e(App::t('quick.akut', 'Akuter Krankheitsfall')) ?></h2>
      <p>Wichtige Hinweise</p>
    </a>
    <a class="quick-card" href="<?= Security::e($base . 'informationen') ?>">
      <span class="quick-icon" aria-hidden="true">📋</span>
      <h2><?= Security::e(App::t('quick.rezepte', 'Rezepte & Überweisungen')) ?></h2>
      <p>Informationen zum Ablauf</p>
    </a>
    <div class="quick-card">
      <span class="quick-icon" aria-hidden="true">📍</span>
      <h2><?= Security::e(App::t('quick.anfahrt', 'Anfahrt')) ?></h2>
      <p><?= Security::e(Setting::get('street', 'Westernstraße 31')) ?><br><?= Security::e(Setting::get('zip_city', '38154 Königslutter am Elm')) ?></p>
      <a class="btn btn-small" href="https://www.openstreetmap.org/?mlat=52.2640&mlon=10.8170#map=18/52.2640/10.8170" target="_blank" rel="noopener"><?= Security::e(App::t('cta.route', 'Route planen')) ?></a>
    </div>
    <a class="quick-card quick-card-accent" href="tel:<?= Security::e(Setting::get('phone_intl', '+4953535511')) ?>">
      <span class="quick-icon" aria-hidden="true">📞</span>
      <h2><?= Security::e(App::t('quick.anruf', 'Praxis anrufen')) ?></h2>
      <p><?= Security::e(Setting::get('phone', '05353 5511')) ?></p>
    </a>
  </div>
</section>

<section class="section section-services">
  <div class="container">
    <h2 class="section-title"><?= Security::e(App::t('section.leistungen', 'Unsere Leistungen')) ?></h2>
    <div class="card-grid">
      <?php foreach ($services as $service): ?>
        <article class="card">
          <span class="card-icon icon-<?= Security::e($service['icon']) ?>" aria-hidden="true"></span>
          <h3><?= Security::e($service['title']) ?></h3>
          <p><?= Security::e($service['description']) ?></p>
          <a class="card-link" href="<?= Security::e($base . 'leistungen') ?>"><?= Security::e(App::t('link.more', 'Mehr erfahren')) ?></a>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section-team">
  <div class="container">
    <h2 class="section-title"><?= Security::e(App::t('section.team', 'Unser Team')) ?></h2>
    <div class="team-grid">
      <?php foreach (array_slice($doctors, 0, 2) as $doc): ?>
        <article class="team-card">
          <div class="team-photo">
            <?php if ($doc['photo'] !== '' && is_file(ROOT_PATH . '/public/uploads/' . $doc['photo'])): ?>
              <img src="<?= Url::to('uploads/' . $doc['photo']) ?>" alt="Portrait <?= Security::e($doc['name']) ?>" width="220" height="220" loading="lazy">
            <?php else: ?>
              <img src="<?= asset('images/portrait-placeholder.svg') ?>" alt="" width="220" height="220" loading="lazy">
            <?php endif; ?>
          </div>
          <div class="team-info">
            <h3><?= Security::e($doc['name']) ?></h3>
            <p class="team-role"><?= Security::e($doc['role']) ?></p>
            <p class="team-spec"><?= Security::e($doc['specialty']) ?></p>
            <a class="btn btn-small btn-primary" href="<?= Security::e($base . 'team/' . $doc['slug']) ?>"><?= Security::e(App::t('link.more', 'Mehr erfahren')) ?></a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section-news">
  <div class="container">
    <h2 class="section-title"><?= Security::e(App::t('section.aktuelles', 'Aktuelles aus der Praxis')) ?></h2>
    <?php if ($news === []): ?>
      <p class="muted">Zurzeit gibt es keine aktuellen Meldungen.</p>
    <?php else: ?>
      <div class="news-grid">
        <?php foreach ($news as $item): ?>
          <article class="news-card<?= (int) $item['important'] === 1 ? ' news-important' : '' ?>">
            <p class="news-date"><time datetime="<?= Security::e($item['publish_date']) ?>"><?= date('d.m.Y', strtotime((string) $item['publish_date'])) ?></time><?= (int) $item['important'] === 1 ? ' · Wichtig' : '' ?></p>
            <h3><?= Security::e($item['title']) ?></h3>
            <p><?= Security::e($item['summary']) ?></p>
            <details class="news-details">
              <summary>Mehr lesen</summary>
              <?= render_safe_text($item['content']) ?>
            </details>
          </article>
        <?php endforeach; ?>
      </div>
      <p class="center"><a class="btn btn-outline" href="<?= Security::e($base . 'aktuelles') ?>">Alle Nachrichten</a></p>
    <?php endif; ?>
  </div>
</section>

<section class="section section-parents">
  <div class="container">
    <h2 class="section-title"><?= Security::e(App::t('section.eltern', 'Informationen für Eltern')) ?></h2>
    <ul class="chip-list">
      <li>Impfpass</li>
      <li>Versichertenkarte</li>
      <li>Vorsorgeheft</li>
      <li>Medikamentenplan</li>
      <li>Vorbefunde</li>
      <li>Termin absagen</li>
      <li>Notfälle</li>
    </ul>
    <p class="center"><a class="btn btn-primary" href="<?= Security::e($base . 'informationen') ?>">Alle Informationen</a></p>
  </div>
</section>
