<div class="admin-toolbar">
  <h1>Leistungen</h1>
  <a class="btn btn-primary" href="<?= Url::to('admin/services/new') ?>">Neue Leistung</a>
</div>
<table class="admin-table">
  <thead><tr><th scope="col">Gruppe</th><th scope="col">Titel</th><th scope="col">Sortierung</th><th scope="col">Status</th><th scope="col">Aktionen</th></tr></thead>
  <tbody>
    <?php foreach ($services as $service): ?>
      <tr>
        <td><?= Security::e(Service::GROUPS[$service['group_key']] ?? $service['group_key']) ?></td>
        <td><?= Security::e($service['title']) ?></td>
        <td><?= (int) $service['sort_order'] ?></td>
        <td><?= (int) $service['active'] === 1 ? 'aktiv' : 'inaktiv' ?></td>
        <td class="admin-row-actions">
          <a href="<?= Url::to('admin/services/' . $service['id'] . '/edit') ?>">Bearbeiten</a>
          <form method="post" action="<?= Url::to('admin/services/toggle') ?>"><?= Security::csrfField() ?><input type="hidden" name="id" value="<?= (int) $service['id'] ?>"><button type="submit"><?= (int) $service['active'] === 1 ? 'Deaktivieren' : 'Aktivieren' ?></button></form>
          <form method="post" action="<?= Url::to('admin/services/delete') ?>" data-confirm="Leistung wirklich löschen?"><?= Security::csrfField() ?><input type="hidden" name="id" value="<?= (int) $service['id'] ?>"><button type="submit" class="danger">Löschen</button></form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
