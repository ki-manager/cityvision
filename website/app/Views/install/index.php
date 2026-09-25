<header class="page-header">
  <div class="container">
    <h1>Installation</h1>
    <p class="page-lead">Assistent für die Erstinstallation der Website.</p>
  </div>
</header>
<div class="container prose">
  <?php foreach ($errors as $err): ?>
    <p class="form-error" role="alert"><?= Security::e($err) ?></p>
  <?php endforeach; ?>
  <?php if ($adminCreated): ?>
    <p class="form-success" role="status">Admin-Konto wurde erstellt. Die Installation ist abgeschlossen. Bitte löschen Sie das <code>/install</code>-Verzeichnis oder sperren Sie es.</p>
  <?php endif; ?>

  <h2>Schritt 1: Systemvoraussetzungen</h2>
  <ul>
    <li>Datenbankverbindung: <?= $dbOk ? '✅ OK' : '❌ fehlgeschlagen – .env prüfen (DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD)' ?></li>
    <li>Tabellen angelegt: <?= $tablesOk ? '✅ OK' : '– noch nicht' ?></li>
  </ul>

  <h2>Schritt 2: Tabellen anlegen</h2>
  <form method="post">
    <?= Security::csrfField() ?>
    <input type="hidden" name="action" value="create_tables">
    <button class="btn btn-primary" type="submit">schema.sql importieren</button>
  </form>

  <h2>Schritt 3: Beispieldaten / Inhalte</h2>
  <form method="post">
    <?= Security::csrfField() ?>
    <input type="hidden" name="action" value="seed">
    <button class="btn btn-primary" type="submit">seed.sql importieren (Inhalte der bisherigen Website)</button>
  </form>

  <h2>Schritt 4: Admin-Konto erstellen</h2>
  <form method="post" class="install-admin">
    <?= Security::csrfField() ?>
    <input type="hidden" name="action" value="create_admin">
    <p><label for="username">Benutzername (min. 3 Zeichen)</label><br>
    <input type="text" id="username" name="username" required minlength="3"></p>
    <p><label for="iname">Name</label><br>
    <input type="text" id="iname" name="name"></p>
    <p><label for="email">E-Mail</label><br>
    <input type="email" id="email" name="email" required></p>
    <p><label for="password">Passwort (min. 10 Zeichen)</label><br>
    <input type="password" id="password" name="password" required minlength="10" autocomplete="new-password"></p>
    <button class="btn btn-primary" type="submit">Admin-Konto anlegen und Installation abschließen</button>
  </form>
</div>
