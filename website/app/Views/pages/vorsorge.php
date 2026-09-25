<header class="page-header">
  <div class="container">
    <h1>Vorsorge</h1>
    <p class="page-lead">Alle Vorsorgeuntersuchungen vom Neugeborenen (U2) bis zum Jugendlichen (J2).</p>
  </div>
</header>
<section class="section">
  <div class="container">
    <div class="card-grid">
      <?php foreach ($services as $service): ?>
        <article class="card">
          <span class="card-icon icon-<?= Security::e($service['icon']) ?>" aria-hidden="true"></span>
          <h3><?= Security::e($service['title']) ?></h3>
          <p><?= Security::e($service['description']) ?></p>
        </article>
      <?php endforeach; ?>
    </div>
    <p class="muted">Die individuelle Durchführung und Beratung der Vorsorgeuntersuchungen erfolgt in der Praxis.</p>
  </div>
</section>
