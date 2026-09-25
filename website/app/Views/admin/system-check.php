<h1>System-Check</h1>
<table class="admin-table">
  <thead><tr><th scope="col">Prüfung</th><th scope="col">Ergebnis</th></tr></thead>
  <tbody>
    <?php foreach ($checks as $label => $value): ?>
      <tr><th scope="row"><?= Security::e($label) ?></th><td><?= Security::e((string) $value) ?></td></tr>
    <?php endforeach; ?>
  </tbody>
</table>
<h2>PHP-Erweiterungen</h2>
<ul class="check-list">
  <?php foreach ($extensions as $ext => $ok): ?>
    <li class="<?= $ok ? 'ok' : 'fail' ?>"><?= Security::e($ext) ?> <?= $ok ? '✅' : '❌ fehlt' ?></li>
  <?php endforeach; ?>
</ul>
<h2>Schreibrechte</h2>
<ul class="check-list">
  <?php foreach ($writable as $path => $ok): ?>
    <li class="<?= $ok ? 'ok' : 'fail' ?>"><?= Security::e($path) ?> <?= $ok ? '✅ beschreibbar' : '❌ nicht beschreibbar (chmod 755/775 prüfen)' ?></li>
  <?php endforeach; ?>
</ul>
