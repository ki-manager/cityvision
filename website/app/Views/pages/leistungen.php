<header class="page-header">
  <div class="container">
    <h1>Unsere Leistungen</h1>
    <p class="page-lead">Unsere Serviceleistungen für die Gesundheit Ihres Kindes.</p>
  </div>
</section>
<section class="section">
  <div class="container">
    <?php foreach (Service::GROUPS as $groupKey => $groupLabel): ?>
      <?php if (empty($groups[$groupKey])) continue; ?>
      <h2 class="section-title"><?= Security::e($groupLabel) ?></h2>
      <div class="card-grid">
        <?php foreach ($groups[$groupKey] as $service): ?>
          <article class="card">
            <span class="card-icon icon-<?= Security::e($service['icon']) ?>" aria-hidden="true"></span>
            <h3><?= Security::e($service['title']) ?></h3>
            <p><?= Security::e($service['description']) ?></p>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
    <div class="notice">
      <p><strong>Hinweis:</strong> Einige der Leistungen werden nicht von den Krankenkassen erstattet, wie: Schul- und Kindergartenbescheinigungen, Atteste z. B. für Gerichtsverfahren etc., längere schriftliche Stellungnahmen, spezielle Impfungen.</p>
    </div>
  </div>
</section>
