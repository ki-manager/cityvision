<div class="error-page container">
  <div class="error-illustration" aria-hidden="true">
    <img src="<?= asset('images/error-bear.svg') ?>" alt="" width="240" height="200">
  </div>
  <h1><?= (int) $code ?> – <?= Security::e($title) ?></h1>
  <p><?= Security::e($message) ?></p>
  <p class="error-actions">
    <a class="btn btn-primary" href="<?= Url::localeUrl('') ?>">Zur Startseite</a>
    <a class="btn btn-outline" href="<?= Url::localeUrl('kontakt') ?>">Kontakt</a>
  </p>
</div>
