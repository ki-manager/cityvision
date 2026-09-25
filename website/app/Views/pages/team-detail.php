<?php $base = '/' . (App::locale() !== 'de' ? App::locale() . '/' : ''); ?>
<header class="page-header">
  <div class="container">
    <nav class="breadcrumb" aria-label="Brotkrumen"><a href="<?= Security::e($base) ?>">Start</a> › <a href="<?= Security::e($base . 'team') ?>">Team</a> › <?= Security::e($member['name']) ?></nav>
    <h1><?= Security::e($member['name']) ?></h1>
    <p class="page-lead"><?= Security::e($member['role']) ?></p>
  </div>
</header>
<div class="container prose team-detail-grid">
  <div class="team-photo">
    <?php if ($member['photo'] !== '' && is_file(ROOT_PATH . '/public/uploads/' . $member['photo'])): ?>
      <img src="<?= Url::to('uploads/' . $member['photo']) ?>" alt="Portrait <?= Security::e($member['name']) ?>" width="260" height="260" loading="lazy">
    <?php else: ?>
      <img src="<?= asset('images/portrait-placeholder.svg') ?>" alt="" width="260" height="260" loading="lazy">
    <?php endif; ?>
  </div>
  <div>
    <?php if ($member['specialty'] !== ''): ?>
      <p class="team-spec"><strong><?= Security::e($member['specialty']) ?></strong></p>
    <?php endif; ?>
    <?= nl2br_esc($member['qualifications']) ?>
    <?php if (!empty($member['bio'])): ?>
      <p><?= nl2br_esc($member['bio']) ?></p>
    <?php endif; ?>
  </div>
</div>
