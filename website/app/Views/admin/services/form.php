<h1><?= $item === null ? 'Neue Leistung' : 'Leistung bearbeiten' ?></h1>
<?php foreach (($errors ?? []) as $err): ?><p class="form-error" role="alert"><?= Security::e($err) ?></p><?php endforeach; ?>
<form method="post" action="<?= Url::to('admin/services/save') ?>" class="admin-form">
  <?= Security::csrfField() ?>
  <?php if ($item !== null): ?><input type="hidden" name="id" value="<?= (int) $item['id'] ?>"><?php endif; ?>
  <p><label for="title">Titel *</label><input type="text" id="title" name="title" required value="<?= Security::e($item['title'] ?? '') ?>"></p>
  <p><label for="slug">Slug (leer für automatisch)</label><input type="text" id="slug" name="slug" value="<?= Security::e($item['slug'] ?? '') ?>"></p>
  <p><label for="group_key">Gruppe</label>
    <select id="group_key" name="group_key">
      <?php foreach (Service::GROUPS as $key => $label): ?>
        <option value="<?= Security::e($key) ?>" <?= ($item['group_key'] ?? '') === $key ? 'selected' : '' ?>><?= Security::e($label) ?></option>
      <?php endforeach; ?>
    </select></p>
  <p><label for="description">Beschreibung</label><textarea id="description" name="description" rows="3"><?= Security::e($item['description'] ?? '') ?></textarea></p>
  <p><label for="icon">Icon (Kennung)</label><input type="text" id="icon" name="icon" value="<?= Security::e($item['icon'] ?? 'stethoscope') ?>"></p>
  <p><label for="sort_order">Reihenfolge</label><input type="number" id="sort_order" name="sort_order" value="<?= (int) ($item['sort_order'] ?? 0) ?>"></p>
  <p class="form-check"><input type="checkbox" id="active" name="active" <?= (int) ($item['active'] ?? 1) === 1 ? 'checked' : '' ?>> <label for="active">Aktiv</label></p>
  <button class="btn btn-primary" type="submit">Speichern</button>
</form>
