<?php $base = '/' . (App::locale() !== 'de' ? App::locale() . '/' : ''); ?>
<header class="page-header">
  <div class="container">
    <h1>Unser Team</h1>
    <p class="page-lead">Lernen Sie uns kennen!</p>
  </div>
</header>
<section class="section">
  <div class="container">
    <h2 class="section-title">Ärzte</h2>
    <div class="team-grid">
      <?php foreach ($doctors as $doc): ?>
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
            <a class="btn btn-small btn-primary" href="<?= Security::e($base . 'team/' . $doc['slug']) ?>">Mehr erfahren</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
    <h2 class="section-title">Praxisteam</h2>
    <div class="staff-grid">
      <?php foreach ($staff as $member): ?>
        <article class="staff-card">
          <h3><?= Security::e($member['name']) ?></h3>
          <p class="team-role"><?= Security::e($member['role']) ?></p>
          <p class="team-spec"><?= Security::e($member['specialty']) ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
