<?php $base = '/' . (App::locale() !== 'de' ? App::locale() . '/' : ''); ?>
<header class="page-header">
  <div class="container">
    <h1>Unsere Praxis</h1>
    <p class="page-lead">Kinder- und Jugendmedizin mit Erfahrung, Zeit und Herz in Königslutter am Elm.</p>
  </div>
</header>
<div class="container prose">
  <?= render_safe_text($page['content'] ?? null) ?>
</div>
<section class="section">
  <div class="container">
    <h2 class="section-title">Wir sprechen</h2>
    <ul class="chip-list">
      <?php foreach (explode(',', Setting::get('languages_spoken', 'Deutsch, Englisch, Polnisch, Arabisch, Russisch')) as $lang): ?>
        <li><?= Security::e(trim($lang)) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
