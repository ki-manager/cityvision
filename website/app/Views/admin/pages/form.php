<h1>Seite bearbeiten: <?= Security::e($page['slug']) ?></h1>
<?php foreach (($errors ?? []) as $err): ?><p class="form-error" role="alert"><?= Security::e($err) ?></p><?php endforeach; ?>
<form method="post" action="<?= Url::to('admin/pages/save') ?>" class="admin-form">
  <?= Security::csrfField() ?>
  <input type="hidden" name="id" value="<?= (int) $page['id'] ?>">
  <p><label for="title">Seitentitel *</label><input type="text" id="title" name="title" required value="<?= Security::e($page['title']) ?>"></p>
  <p><label for="meta_description">Meta-Description</label><input type="text" id="meta_description" name="meta_description" maxlength="320" value="<?= Security::e($page['meta_description']) ?>"></p>
  <p><label for="content">Inhalt (## für Überschriften, - für Listen)</label><textarea id="content" name="content" rows="16"><?= Security::e($page['content']) ?></textarea></p>
  <button class="btn btn-primary" type="submit">Speichern</button>
</form>
