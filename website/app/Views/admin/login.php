<div class="admin-login-box">
  <h1>Admin-Login</h1>
  <?php foreach (($errors ?? []) as $err): ?>
    <p class="form-error" role="alert"><?= Security::e($err) ?></p>
  <?php endforeach; ?>
  <form method="post" action="<?= Url::to('admin/login') ?>">
    <?= Security::csrfField() ?>
    <p><label for="login">Benutzername oder E-Mail</label><br>
    <input type="text" id="login" name="login" required autocomplete="username"></p>
    <p><label for="password">Passwort</label><br>
    <input type="password" id="password" name="password" required autocomplete="current-password"></p>
    <button class="btn btn-primary" type="submit">Anmelden</button>
  </form>
</div>
