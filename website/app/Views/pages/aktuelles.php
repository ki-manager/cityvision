<header class="page-header">
  <div class="container">
    <h1>Aktuelles</h1>
    <p class="page-lead">Alle aktuellen Informationen, Urlaubszeiten, Vertretungen etc.</p>
  </div>
</header>
<section class="section">
  <div class="container">
    <?php if ($news === []): ?>
      <p class="muted">Zurzeit gibt es keine aktuellen Meldungen.</p>
    <?php else: ?>
      <div class="news-grid">
        <?php foreach ($news as $item): ?>
          <article class="news-card<?= (int) $item['important'] === 1 ? ' news-important' : '' ?>">
            <p class="news-date"><time datetime="<?= Security::e($item['publish_date']) ?>"><?= date('d.m.Y', strtotime((string) $item['publish_date'])) ?></time><?= (int) $item['important'] === 1 ? ' · Wichtig' : '' ?></p>
            <h2><?= Security::e($item['title']) ?></h2>
            <p><?= Security::e($item['summary']) ?></p>
            <details class="news-details">
              <summary>Mehr lesen</summary>
              <?= render_safe_text($item['content']) ?>
            </details>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>
