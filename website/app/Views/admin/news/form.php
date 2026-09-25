<h1><?= $item === null ? 'Neue Meldung' : 'Meldung bearbeiten' ?></h1>
<?php foreach (($errors ?? []) as $err): ?><p class="form-error" role="alert"><?= Security::e($err) ?></p><?php endforeach; ?>
<form method="post" action="<?= Url::to('admin/news/save') ?>" class="admin-form">
  <?= Security::csrfField() ?>
  <?php if ($item !== null): ?><input type="hidden" name="id" value="<?= (int) $item['id'] ?>"><?php endif; ?>
  <p><label for="title">Titel *</label><input type="text" id="title" name="title" required value="<?= Security::e($item['title'] ?? '') ?>"></p>
  <p><label for="slug">Slug (leer lassen für automatisch)</label><input type="text" id="slug" name="slug" value="<?= Security::e($item['slug'] ?? '') ?>"></p>
  <p><label for="summary">Kurzfassung</label><textarea id="summary" name="summary" rows="2"><?= Security::e($item['summary'] ?? '') ?></textarea></p>
  <p><label for="content">Text</label><textarea id="content" name="content" rows="8"><?= Security::e($item['content'] ?? '') ?></textarea></p>
  <div class="admin-form-row">
    <p><label for="publish_date">Veröffentlichung *</label><input type="date" id="publish_date" name="publish_date" required value="<?= Security::e($item['publish_date'] ?? date('Y-m-d')) ?>"></p>
    <p><label for="start_date">Anzeigen von (optional)</label><input type="date" id="start_date" name="start_date" value="<?= Security::e($item['start_date'] ?? '') ?>"></p>
    <p><label for="end_date">Anzeigen bis (optional)</label><input type="date" id="end_date" name="end_date" value="<?= Security::e($item['end_date'] ?? '') ?>"></p>
  </div>
  <p class="form-check"><input type="checkbox" id="important" name="important" <?= (int) ($item['important'] ?? 0) === 1 ? 'checked' : '' ?>> <label for="important">Wichtig (als Banner hervorheben)</label></p>
  <p class="form-check"><input type="checkbox" id="active" name="active" <?= (int) ($item['active'] ?? 1) === 1 ? 'checked' : '' ?>> <label for="active">Aktiv</label></p>
  <button class="btn btn-primary" type="submit">Speichern</button>
</form>
