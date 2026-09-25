<h1>Kontaktanfragen</h1>
<nav class="filter-nav" aria-label="Statusfilter">
  <a href="<?= Url::to('admin/contact-requests') ?>" class="<?= $statusFilter === '' ? 'active' : '' ?>">Alle</a>
  <a href="<?= Url::to('admin/contact-requests?status=neu') ?>" class="<?= $statusFilter === 'neu' ? 'active' : '' ?>">Neu</a>
  <a href="<?= Url::to('admin/contact-requests?status=in_bearbeitung') ?>" class="<?= $statusFilter === 'in_bearbeitung' ? 'active' : '' ?>">In Bearbeitung</a>
  <a href="<?= Url::to('admin/contact-requests?status=erledigt') ?>" class="<?= $statusFilter === 'erledigt' ? 'active' : '' ?>">Erledigt</a>
</nav>
<?php if ($requests === []): ?><p class="muted">Keine Anfragen vorhanden.</p><?php endif; ?>
<?php foreach ($requests as $req): ?>
  <article class="contact-request-card">
    <header>
      <strong><?= Security::e($req['firstname'] . ' ' . $req['lastname']) ?></strong>
      <span class="badge"><?= Security::e($req['status']) ?></span>
      <time datetime="<?= Security::e($req['created_at']) ?>"><?= date('d.m.Y H:i', strtotime((string) $req['created_at'])) ?></time>
    </header>
    <p><strong>Betreff:</strong> <?= Security::e($req['subject']) ?></p>
    <p><?= nl2br_esc($req['message']) ?></p>
    <p><a href="mailto:<?= Security::e($req['email']) ?>"><?= Security::e($req['email']) ?></a>
    <?= $req['phone'] !== '' ? ' · ' . Security::e($req['phone']) : '' ?></p>
    <div class="admin-row-actions">
      <?php foreach (['neu', 'in_bearbeitung', 'erledigt'] as $status): if ($status === $req['status']) continue; ?>
        <form method="post" action="<?= Url::to('admin/contact-requests/status') ?>"><?= Security::csrfField() ?>
          <input type="hidden" name="id" value="<?= (int) $req['id'] ?>"><input type="hidden" name="status" value="<?= Security::e($status) ?>">
          <button type="submit">→ <?= Security::e($status) ?></button>
        </form>
      <?php endforeach; ?>
      <form method="post" action="<?= Url::to('admin/contact-requests/delete') ?>" data-confirm="Anfrage wirklich löschen?"><?= Security::csrfField() ?>
        <input type="hidden" name="id" value="<?= (int) $req['id'] ?>"><button type="submit" class="danger">Löschen</button>
      </form>
    </div>
  </article>
<?php endforeach; ?>
