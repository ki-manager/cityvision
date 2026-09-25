<?php

$base = '/' . (App::locale() !== 'de' ? App::locale() . '/' : '');
$errors = $errors ?? [];
$old = $old ?? [];
$sent = isset($_GET['sent']);
?>
<header class="page-header">
  <div class="container">
    <h1>Kontakt</h1>
    <p class="page-lead">Haben Sie Fragen oder möchten Sie einen Termin vereinbaren? Unser Team ist gerne für Sie da!</p>
  </div>
</header>
<section class="section">
  <div class="container contact-grid">
    <div class="contact-info">
      <h2>Kinderarztpraxis Königslutter am Elm</h2>
      <p>Andreas Clare, Facharzt für Kinderheilkunde und Jugendmedizin – Neonatologie<br>
      Salim Shammas, Facharzt für Kinderheilkunde und Jugendmedizin (angestellter Facharzt)</p>
      <address>
        <p><strong>Adresse:</strong><br><?= Security::e(Setting::get('street', 'Westernstraße 31')) ?><br><?= Security::e(Setting::get('zip_city', '38154 Königslutter am Elm')) ?></p>
        <p><strong>Telefon:</strong> <a href="tel:<?= Security::e(Setting::get('phone_intl', '+4953535511')) ?>"><?= Security::e(Setting::get('phone', '05353 5511')) ?></a><br>
        <strong>Fax:</strong> <?= Security::e(Setting::get('fax', '05353 8038')) ?></p>
      </address>
      <div class="contact-actions">
        <a class="btn btn-primary" href="tel:<?= Security::e(Setting::get('phone_intl', '+4953535511')) ?>">Anrufen</a>
        <a class="btn btn-primary" href="https://www.openstreetmap.org/?mlat=52.2640&mlon=10.8170#map=18/52.2640/10.8170" target="_blank" rel="noopener">Route planen</a>
        <a class="btn btn-outline" href="<?= Security::e(Setting::get('doctolib_url', '#')) ?>" target="_blank" rel="noopener">Termin buchen</a>
      </div>
      <h2>Sprechzeiten</h2>
      <table class="hours-table">
        <?php foreach (OpeningHours::all() as $h): ?>
          <tr<?= (int) $h['closed'] === 1 ? ' class="closed"' : '' ?>>
            <th scope="row"><?= Security::e(OpeningHours::weekdays()[(int) $h['weekday']]) ?></th>
            <td>
              <?= (int) $h['closed'] === 1
                ? 'geschlossen'
                : Security::e(OpeningHours::formatRange($h['morning_from'], $h['morning_to'], (string) ($h['note'] ?? '')))
                   . ($h['afternoon_from'] !== null ? '<br>' . Security::e(OpeningHours::formatRange($h['afternoon_from'], $h['afternoon_to'])) : '') ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
      <p class="map-note">Karte: <a href="https://www.openstreetmap.org/?mlat=52.2640&mlon=10.8170#map=18/52.2640/10.8170" target="_blank" rel="noopener">Auf OpenStreetMap ansehen</a> (externer Link, kein automatisches Laden externer Karten).</p>
    </div>
    <div class="contact-form-wrap">
      <h2>Schreiben Sie uns</h2>
      <?php if ($sent): ?>
        <p class="form-success" role="status">Vielen Dank! Ihre Nachricht wurde übermittelt. Wir melden uns zeitnah.</p>
      <?php endif; ?>
      <p class="form-warning"><strong>Wichtig:</strong> Das Kontaktformular ist nicht für medizinische Notfälle geeignet. Im Notfall rufen Sie bitte den Notarzt unter <a href="tel:112">112</a>.</p>
      <form method="post" action="<?= Security::e($base . 'kontakt') ?>" class="contact-form" novalidate>
        <?= Security::csrfField() ?>
        <input type="hidden" name="ts" value="<?= time() ?>">
        <p class="hp-field" aria-hidden="true"><label>Website (bitte leer lassen)<input type="text" name="website" tabindex="-1" autocomplete="off"></label></p>
        <div class="form-row">
          <div class="form-field">
            <label for="firstname">Vorname *</label>
            <input type="text" id="firstname" name="firstname" required autocomplete="given-name" value="<?= Security::e($old['firstname'] ?? '') ?>">
            <?php if (isset($errors['firstname'])): ?><p class="form-error" role="alert"><?= Security::e($errors['firstname']) ?></p><?php endif; ?>
          </div>
          <div class="form-field">
            <label for="lastname">Nachname *</label>
            <input type="text" id="lastname" name="lastname" required autocomplete="family-name" value="<?= Security::e($old['lastname'] ?? '') ?>">
            <?php if (isset($errors['lastname'])): ?><p class="form-error" role="alert"><?= Security::e($errors['lastname']) ?></p><?php endif; ?>
          </div>
        </div>
        <div class="form-row">
          <div class="form-field">
            <label for="email">E-Mail *</label>
            <input type="email" id="email" name="email" required autocomplete="email" value="<?= Security::e($old['email'] ?? '') ?>">
            <?php if (isset($errors['email'])): ?><p class="form-error" role="alert"><?= Security::e($errors['email']) ?></p><?php endif; ?>
          </div>
          <div class="form-field">
            <label for="phone">Telefonnummer (optional)</label>
            <input type="tel" id="phone" name="phone" autocomplete="tel" value="<?= Security::e($old['phone'] ?? '') ?>">
          </div>
        </div>
        <div class="form-field">
          <label for="subject">Betreff *</label>
          <input type="text" id="subject" name="subject" required value="<?= Security::e($old['subject'] ?? '') ?>">
          <?php if (isset($errors['subject'])): ?><p class="form-error" role="alert"><?= Security::e($errors['subject']) ?></p><?php endif; ?>
        </div>
        <div class="form-field">
          <label for="message">Nachricht *</label>
          <textarea id="message" name="message" rows="6" required><?= Security::e($old['message'] ?? '') ?></textarea>
          <?php if (isset($errors['message'])): ?><p class="form-error" role="alert"><?= Security::e($errors['message']) ?></p><?php endif; ?>
        </div>
        <div class="form-field form-check">
          <input type="checkbox" id="privacy" name="privacy" required>
          <label for="privacy">Ich habe die <a href="<?= Security::e($base . 'datenschutz') ?>">Datenschutzerklärung</a> gelesen und stimme der Verarbeitung meiner Daten zur Bearbeitung der Anfrage zu. *</label>
        </div>
        <?php if (isset($errors['privacy'])): ?><p class="form-error" role="alert"><?= Security::e($errors['privacy']) ?></p><?php endif; ?>
        <button type="submit" class="btn btn-primary">Nachricht senden</button>
        <p class="form-hint">* Pflichtfelder. Bitte geben Sie keine sensiblen Gesundheitsdaten ein.</p>
      </form>
    </div>
  </div>
</section>
