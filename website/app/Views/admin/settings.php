<h1>Einstellungen</h1>
<?php if (!empty($saved)): ?><p class="form-success" role="status">Einstellungen gespeichert.</p><?php endif; ?>
<?php foreach (($errors ?? []) as $err): ?><p class="form-error" role="alert"><?= Security::e($err) ?></p><?php endforeach; ?>
<form method="post" action="<?= Url::to('admin/settings/save') ?>" class="admin-form">
  <?= Security::csrfField() ?>
  <div class="admin-form-row">
    <p><label for="practice_name">Praxisname</label><input type="text" id="practice_name" name="practice_name" value="<?= Security::e($settings['practice_name'] ?? '') ?>"></p>
    <p><label for="practice_owner">Inhaber</label><input type="text" id="practice_owner" name="practice_owner" value="<?= Security::e($settings['practice_owner'] ?? '') ?>"></p>
  </div>
  <div class="admin-form-row">
    <p><label for="street">Straße</label><input type="text" id="street" name="street" value="<?= Security::e($settings['street'] ?? '') ?>"></p>
    <p><label for="zip_city">PLZ / Ort</label><input type="text" id="zip_city" name="zip_city" value="<?= Security::e($settings['zip_city'] ?? '') ?>"></p>
  </div>
  <div class="admin-form-row">
    <p><label for="phone">Telefon</label><input type="text" id="phone" name="phone" value="<?= Security::e($settings['phone'] ?? '') ?>"></p>
    <p><label for="phone_intl">Telefon international (tel:)</label><input type="text" id="phone_intl" name="phone_intl" value="<?= Security::e($settings['phone_intl'] ?? '') ?>"></p>
  </div>
  <div class="admin-form-row">
    <p><label for="fax">Fax</label><input type="text" id="fax" name="fax" value="<?= Security::e($settings['fax'] ?? '') ?>"></p>
    <p><label for="email">E-Mail</label><input type="text" id="email" name="email" value="<?= Security::e($settings['email'] ?? '') ?>"></p>
  </div>
  <p><label for="doctolib_url">Doctolib-Link</label><input type="url" id="doctolib_url" name="doctolib_url" value="<?= Security::e($settings['doctolib_url'] ?? '') ?>"></p>
  <p><label for="languages_spoken">Gesprochene Sprachen</label><input type="text" id="languages_spoken" name="languages_spoken" value="<?= Security::e($settings['languages_spoken'] ?? '') ?>"></p>
  <p><label for="emergency_note">Notfall-Hinweis</label><input type="text" id="emergency_note" name="emergency_note" value="<?= Security::e($settings['emergency_note'] ?? '') ?>"></p>
  <p><label for="footer_note">Footer-Text</label><input type="text" id="footer_note" name="footer_note" value="<?= Security::e($settings['footer_note'] ?? '') ?>"></p>
  <button class="btn btn-primary" type="submit">Speichern</button>
</form>
