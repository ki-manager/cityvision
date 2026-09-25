<h1>Dashboard</h1>
<div class="stat-grid">
  <div class="stat-card"><span class="stat-number"><?= (int) $stats['news'] ?></span><span>Aktuelle Meldungen</span></div>
  <div class="stat-card"><span class="stat-number"><?= (int) $stats['news_active'] ?></span><span>davon aktiv</span></div>
  <div class="stat-card"><span class="stat-number"><?= (int) $stats['contact_new'] ?></span><span>Neue Kontaktanfragen</span></div>
  <div class="stat-card"><span class="stat-number"><?= (int) $stats['team'] ?></span><span>Aktive Teammitglieder</span></div>
</div>
<p class="admin-actions"><a class="btn btn-primary" href="<?= Url::to('admin/news/new') ?>">Neue Meldung</a>
<a class="btn btn-outline" href="<?= Url::to('admin/system-check') ?>">System-Check</a></p>
