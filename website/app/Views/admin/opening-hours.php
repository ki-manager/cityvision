<h1>Öffnungszeiten</h1>
<?php foreach (($errors ?? []) as $err): ?><p class="form-error" role="alert"><?= Security::e($err) ?></p><?php endforeach; ?>
<form method="post" action="<?= Url::to('admin/opening-hours/save') ?>" class="admin-form">
  <?= Security::csrfField() ?>
  <table class="admin-table hours-edit">
    <thead><tr><th scope="col">Tag</th><th scope="col">Vormittag von</th><th scope="col">bis</th><th scope="col">Nachmittag von</th><th scope="col">bis</th><th scope="col">Hinweis</th><th scope="col">Geschlossen</th></tr></thead>
    <tbody>
      <?php $byDay = []; foreach ($hours as $h) { $byDay[(int) $h['weekday']] = $h; } ?>
      <?php foreach (OpeningHours::weekdays() as $weekday => $label): $h = $byDay[$weekday] ?? null; ?>
        <tr>
          <th scope="row"><?= Security::e($label) ?></th>
          <td><input type="time" name="day[<?= $weekday ?>][morning_from]" value="<?= $h ? Security::e(substr((string) $h['morning_from'], 0, 5)) : '' ?>"></td>
          <td><input type="time" name="day[<?= $weekday ?>][morning_to]" value="<?= $h ? Security::e(substr((string) $h['morning_to'], 0, 5)) : '' ?>"></td>
          <td><input type="time" name="day[<?= $weekday ?>][afternoon_from]" value="<?= $h ? Security::e(substr((string) $h['afternoon_from'], 0, 5)) : '' ?>"></td>
          <td><input type="time" name="day[<?= $weekday ?>][afternoon_to]" value="<?= $h ? Security::e(substr((string) $h['afternoon_to'], 0, 5)) : '' ?>"></td>
          <td><input type="text" name="day[<?= $weekday ?>][note]" value="<?= Security::e($h['note'] ?? '') ?>" placeholder="z. B. nach Vereinbarung"></td>
          <td><input type="checkbox" name="day[<?= $weekday ?>][closed]" <?= $h && (int) $h['closed'] === 1 ? 'checked' : '' ?>></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <button class="btn btn-primary" type="submit">Speichern</button>
</form>
