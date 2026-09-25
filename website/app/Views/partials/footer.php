<?php

$locale = App::locale();
$base = '/' . ($locale !== 'de' ? $locale . '/' : '');
?>
<footer class="site-footer">
  <div class="container footer-grid">
    <div class="footer-col">
      <h3>Kinderarztpraxis Königslutter am Elm</h3>
      <p><?= Security::e(Setting::get('practice_owner', 'Andreas Clare')) ?><br>
      Facharzt für Kinderheilkunde und Jugendmedizin<br>Zusatzbezeichnung Neonatologie</p>
    </div>
    <div class="footer-col">
      <h3><?= Security::e(App::t('footer.contact', 'Kontakt')) ?></h3>
      <address>
        <?= Security::e(Setting::get('street', 'Westernstraße 31')) ?><br>
        <?= Security::e(Setting::get('zip_city', '38154 Königslutter am Elm')) ?><br>
        <a href="tel:<?= Security::e(Setting::get('phone_intl', '+4953535511')) ?>">Tel.: <?= Security::e(Setting::get('phone', '05353 5511')) ?></a><br>
        Fax: <?= Security::e(Setting::get('fax', '05353 8038')) ?><br>
        <?php $mail = Setting::get('email'); if ($mail !== '' && !str_contains($mail, 'TODO')): ?>
          <a href="mailto:<?= Security::e($mail) ?>"><?= Security::e($mail) ?></a>
        <?php endif; ?>
      </address>
    </div>
    <div class="footer-col">
      <h3><?= Security::e(App::t('footer.times', 'Öffnungszeiten')) ?></h3>
      <table class="footer-hours">
        <?php foreach (OpeningHours::all() as $h): if ((int) $h['weekday'] > 5) continue; ?>
          <tr>
            <th scope="row"><?= Security::e(OpeningHours::weekdays()[(int) $h['weekday']]) ?></th>
            <td><?= Security::e(OpeningHours::formatRange($h['morning_from'], $h['morning_to'], (string) ($h['note'] ?? ''))) ?><br>
            <?= $h['afternoon_from'] !== null ? Security::e(OpeningHours::formatRange($h['afternoon_from'], $h['afternoon_to'])) : '' ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
    <div class="footer-col">
      <h3>Links</h3>
      <ul class="footer-links">
        <li><a href="<?= Security::e($base . 'impressum') ?>">Impressum</a></li>
        <li><a href="<?= Security::e($base . 'datenschutz') ?>">Datenschutz</a></li>
        <li><a href="<?= Security::e($base . 'kontakt') ?>">Kontakt</a></li>
        <li><a href="<?= Security::e(Setting::get('doctolib_url', '#')) ?>" target="_blank" rel="noopener">Termine online (Doctolib)</a></li>
      </ul>
    </div>
  </div>
  <div class="container footer-bottom">
    <p><?= Security::e(Setting::get('footer_note', 'Copyright © ' . date('Y') . ' Andreas Clare. Alle Rechte vorbehalten.')) ?></p>
  </div>
</footer>
