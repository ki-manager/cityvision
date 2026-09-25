<header class="page-header">
  <div class="container">
    <h1><?= Security::e($title ?? 'Hinweis') ?></h1>
  </div>
</header>
<div class="container prose">
  <p><?= Security::e($message ?? '') ?></p>
</div>
