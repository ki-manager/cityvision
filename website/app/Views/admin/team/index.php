<div class="admin-toolbar">
  <h1>Team</h1>
  <a class="btn btn-primary" href="<?= Url::to('admin/team/new') ?>">Neues Teammitglied</a>
</div>
<table class="admin-table">
  <thead><tr><th scope="col">Sortierung</th><th scope="col">Name</th><th scope="col">Funktion</th><th scope="col">Status</th><th scope="col">Aktionen</th></tr></thead>
  <tbody>
    <?php foreach ($team as $member): ?>
      <tr>
        <td><?= (int) $member['sort_order'] ?></td>
        <td><?= Security::e($member['name']) ?><?= $member['role_type'] === 'arzt' ? ' (Arzt)' : '' ?></td>
        <td><?= Security::e($member['role']) ?></td>
        <td><?= (int) $member['active'] === 1 ? 'aktiv' : 'inaktiv' ?></td>
        <td class="admin-row-actions">
          <a href="<?= Url::to('admin/team/' . $member['id'] . '/edit') ?>">Bearbeiten</a>
          <form method="post" action="<?= Url::to('admin/team/toggle') ?>"><?= Security::csrfField() ?><input type="hidden" name="id" value="<?= (int) $member['id'] ?>"><button type="submit"><?= (int) $member['active'] === 1 ? 'Deaktivieren' : 'Aktivieren' ?></button></form>
          <form method="post" action="<?= Url::to('admin/team/delete') ?>" data-confirm="Teammitglied wirklich löschen?"><?= Security::csrfField() ?><input type="hidden" name="id" value="<?= (int) $member['id'] ?>"><button type="submit" class="danger">Löschen</button></form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
