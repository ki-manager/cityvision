<header class="page-header">
  <div class="container">
    <h1>Impfungen</h1>
    <p class="page-lead">Impfberatung, STIKO-Impfungen für Kinder und Jugendliche sowie Reiseimpfungen.</p>
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
    <div class="notice">
      <p>Die individuelle medizinische Beratung erfolgt in der Praxis.</p>
    </div>
  </div>
</section>
