<h1><?= $item === null ? 'Neues Teammitglied' : 'Teammitglied bearbeiten' ?></h1>
<?php foreach (($errors ?? []) as $err): ?><p class="form-error" role="alert"><?= Security::e($err) ?></p><?php endforeach; ?>
<form method="post" action="<?= Url::to('admin/team/save') ?>" enctype="multipart/form-data" class="admin-form">
  <?= Security::csrfField() ?>
  <?php if ($item !== null): ?><input type="hidden" name="id" value="<?= (int) $item['id'] ?>"><input type="hidden" name="photo_current" value="<?= Security::e($item['photo']) ?>"><?php endif; ?>
  <p><label for="name">Name *</label><input type="text" id="name" name="name" required value="<?= Security::e($item['name'] ?? '') ?>"></p>
  <p><label for="slug">Slug (leer für automatisch)</label><input type="text" id="slug" name="slug" value="<?= Security::e($item['slug'] ?? '') ?>"></p>
  <p><label for="role">Funktion</label><input type="text" id="role" name="role" value="<?= Security::e($item['role'] ?? '') ?>"></p>
  <p><label for="role_type">Typ</label>
    <select id="role_type" name="role_type">
      <option value="arzt" <?= ($item['role_type'] ?? '') === 'arzt' ? 'selected' : '' ?>>Arzt/Ärztin</option>
      <option value="mitarbeitende" <?= ($item['role_type'] ?? '') === 'mitarbeitende' ? 'selected' : '' ?>>Mitarbeitende</option>
    </select></p>
  <p><label for="specialty">Fachgebiet</label><input type="text" id="specialty" name="specialty" value="<?= Security::e($item['specialty'] ?? '') ?>"></p>
  <p><label for="qualifications">Qualifikationen / Werdegang</label><textarea id="qualifications" name="qualifications" rows="8"><?= Security::e($item['qualifications'] ?? '') ?></textarea></p>
  <p><label for="bio">Kurztext</label><textarea id="bio" name="bio" rows="3"><?= Security::e($item['bio'] ?? '') ?></textarea></p>
  <p><label for="photo">Bild (JPG, PNG, WebP, AVIF; max. 4 MB)</label><input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp,image/avif"></p>
  <p><label for="sort_order">Reihenfolge</label><input type="number" id="sort_order" name="sort_order" value="<?= (int) ($item['sort_order'] ?? 0) ?>"></p>
  <p class="form-check"><input type="checkbox" id="active" name="active" <?= (int) ($item['active'] ?? 1) === 1 ? 'checked' : '' ?>> <label for="active">Aktiv</label></p>
  <button class="btn btn-primary" type="submit">Speichern</button>
</form>
