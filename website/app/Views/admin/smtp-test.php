<h1>SMTP-Test</h1>
<form method="post" action="<?= Url::to('admin/smtp-test') ?>" class="admin-form">
  <?= Security::csrfField() ?>
  <p><label for="recipient">Empfänger für Testmail (optional)</label><input type="email" id="recipient" name="recipient" placeholder="praxis@example.de"></p>
  <button class="btn btn-primary" type="submit">SMTP-Verbindung testen</button>
</form>
<?php if (isset($result)): ?>
  <section class="smtp-result" aria-live="polite">
    <h2>Ergebnis</h2>
    <?php if ($result['ok']): ?>
      <p class="form-success">✅ SMTP-Verbindung erfolgreich.</p>
    <?php else: ?>
      <p class="form-error">❌ Test fehlgeschlagen: <?= Security::e($result['error'] ?? 'unbekannter Fehler') ?></p>
    <?php endif; ?>
    <?php if (!empty($result['steps'])): ?>
      <ul>
        <?php foreach ($result['steps'] as $step): ?><li><?= Security::e($step) ?></li><?php endforeach; ?>
      </ul>
    <?php endif; ?>
    <p class="muted">Hinweis: Zugangsdaten werden nicht angezeigt.</p>
  </section>
<?php endif; ?>
