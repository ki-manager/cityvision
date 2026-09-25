<div class="admin-toolbar">
  <h1>Aktuelles</h1>
  <a class="btn btn-primary" href="<?= Url::to('admin/news/new') ?>">Neue Meldung</a>
</div>
<table class="admin-table">
  <thead><tr><th scope="col">Datum</th><th scope="col">Titel</th><th scope="col">Status</th><th scope="col">Aktionen</th></tr></thead>
  <tbody>
    <?php foreach ($news as $item): ?>
      <tr>
        <td><?= Security::e($item['publish_date']) ?></td>
        <td><?= Security::e($item['title']) ?><?= (int) $item['important'] === 1 ? ' ⭐' : '' ?></td>
        <td><?= (int) $item['active'] === 1 ? 'aktiv' : 'inaktiv' ?></td>
        <td class="admin-row-actions">
          <a href="<?= Url::to('admin/news/' . $item['id'] . '/edit') ?>">Bearbeiten</a>
          <form method="post" action="<?= Url::to('admin/news/toggle') ?>"><?= Security::csrfField() ?><input type="hidden" name="id" value="<?= (int) $item['id'] ?>"><button type="submit"><?= (int) $item['active'] === 1 ? 'Deaktivieren' : 'Aktivieren' ?></button></form>
          <form method="post" action="<?= Url::to('admin/news/delete') ?>" data-confirm="Meldung wirklich löschen?"><?= Security::csrfField() ?><input type="hidden" name="id" value="<?= (int) $item['id'] ?>"><button type="submit" class="danger">Löschen</button></form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
