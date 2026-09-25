<h1>Seiten</h1>
<table class="admin-table">
  <thead><tr><th scope="col">Slug</th><th scope="col">Sprache</th><th scope="col">Titel</th><th scope="col">Aktualisiert</th><th scope="col">Aktion</th></tr></thead>
  <tbody>
    <?php foreach ($pages as $page): ?>
      <tr>
        <td><?= Security::e($page['slug']) ?></td>
        <td><?= Security::e($page['locale']) ?></td>
        <td><?= Security::e($page['title']) ?></td>
        <td><?= Security::e($page['updated_at']) ?></td>
        <td><a href="<?= Url::to('admin/pages/' . rawurlencode((string) $page['slug']) . '/edit') ?>">Bearbeiten</a></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
