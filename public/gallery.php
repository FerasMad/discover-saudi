<?php
require_once __DIR__ . '/includes/helpers.php';

$page_title    = 'معرض المناطق';
$page_active   = 'gallery';
$page_class    = 'page-gallery';
$page_scripts  = ['js/gallery.js'];

// Pull all regions from DB.
$places = $pdo->query(
    "SELECT id, name_ar, region_type, tagline, description, cities, hero_image
     FROM places
     ORDER BY id ASC"
)->fetchAll();

// Distinct region types for the filter dropdown — pull from DB so admin-added types work.
$types = $pdo->query("SELECT DISTINCT region_type FROM places ORDER BY region_type ASC")->fetchAll(PDO::FETCH_COLUMN);

require __DIR__ . '/includes/header.php';
?>

<section class="section section-tight gallery-head">
  <div class="container">
    <p class="eyebrow">معرض المناطق</p>
    <h1 class="gallery-title">ابحث، صفّ، واختر وجهتك.</h1>
    <p class="gallery-lede">ثلاث عشرة منطقة، كل واحدة بحكاية مستقلة. اضغط على أي بطاقة للانتقال إلى صفحة التفاصيل.</p>

    <form class="gallery-tools" role="search" autocomplete="off" onsubmit="return false;">
      <div class="tool-search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
        <input
          id="g-search"
          type="search"
          placeholder="ابحث عن منطقة أو معلم…"
          aria-label="ابحث عن منطقة"
          data-gallery-search>
      </div>

      <label class="sr-only" for="g-filter">صفّ حسب النوع</label>
      <select id="g-filter" data-gallery-filter aria-label="صفّ حسب النوع">
        <option value="">كل الأنواع</option>
        <?php foreach ($types as $t): ?>
          <option value="<?= e($t) ?>"><?= e($t) ?></option>
        <?php endforeach; ?>
      </select>

      <p class="tool-count" aria-live="polite">
        عدد النتائج: <span data-gallery-count><?= count($places) ?></span>
      </p>
    </form>
  </div>
</section>

<section class="section-tight">
  <div class="container">
    <?php if (count($places) === 0): ?>
      <div class="empty-state">
        <p>لم تُضف مناطق بعد. سجّل دخول المشرف لإضافة محتوى.</p>
        <a class="btn btn-outline" href="<?= url('admin/login.php') ?>">دخول المشرف</a>
      </div>
    <?php else: ?>
      <div class="gallery-grid" data-gallery-grid>
        <?php foreach ($places as $i => $p):
          // Vary tile heights to break the grid rhythm — every 5th tile is "tall".
          $is_tall = ($i % 5 === 0);
          $ph_h = 30 + (($i * 47) % 280); // pseudo-random hue for placeholder
        ?>
          <a class="g-tile<?= $is_tall ? ' g-tile--tall' : '' ?>"
             href="<?= url('place.php?id=' . (int)$p['id']) ?>"
             data-name="<?= e(mb_strtolower($p['name_ar'], 'UTF-8')) ?>"
             data-tagline="<?= e(mb_strtolower($p['tagline'] ?? '', 'UTF-8')) ?>"
             data-type="<?= e($p['region_type']) ?>">
            <div class="g-photo">
              <?php if (!empty($p['hero_image'])): ?>
                <img src="<?= e(image_url($p['hero_image'])) ?>" alt="" loading="lazy">
              <?php else: ?>
                <div class="placeholder-gradient" style="--ph-h: <?= $ph_h ?>; width:100%; height:100%;"></div>
              <?php endif; ?>
              <div class="g-veil"></div>
            </div>
            <div class="g-meta">
              <?php if (mb_strtolower($p['name_ar'], 'UTF-8') !== mb_strtolower($p['region_type'], 'UTF-8')): ?>
                <span class="g-eyebrow"><?= e($p['region_type']) ?></span>
              <?php endif; ?>
              <h3 class="g-name"><?= e($p['name_ar']) ?></h3>
              <?php if (!empty($p['cities'])): ?>
                <p class="g-cities"><?= e($p['cities']) ?></p>
              <?php endif; ?>
              <?php if (!empty($p['tagline'])): ?>
                <p class="g-tagline"><?= e($p['tagline']) ?></p>
              <?php endif; ?>
            </div>
          </a>
        <?php endforeach; ?>
      </div>

      <div class="empty-state" data-gallery-empty hidden>
        <p>لا نتائج تطابق بحثك. جرّب إزالة عوامل التصفية.</p>
        <button class="btn btn-outline" type="button" data-gallery-reset>أعد ضبط البحث</button>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
