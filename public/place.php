<?php
require_once __DIR__ . '/includes/helpers.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: ' . url('gallery.php'));
    exit;
}

$stmt = $pdo->prepare(
    "SELECT * FROM places WHERE id = :id LIMIT 1"
);
$stmt->execute(['id' => $id]);
$place = $stmt->fetch();

if (!$place) {
    http_response_code(404);
    $page_title = 'الصفحة غير موجودة';
    $page_active = 'gallery';
    require __DIR__ . '/includes/header.php';
    ?>
    <section class="section">
      <div class="container container-narrow center" style="padding-block: var(--s-9);">
        <p class="eyebrow">404</p>
        <h1 style="font-size: var(--fs-display); margin-block: var(--s-3);">المنطقة المطلوبة غير متوفرة.</h1>
        <p class="muted">قد يكون الرابط قديماً، أو أن السجل قد حُذف.</p>
        <p style="margin-top: var(--s-5);"><a class="btn btn-primary" href="<?= url('gallery.php') ?>">عُد إلى المعرض</a></p>
      </div>
    </section>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

// Adjacent places for prev/next navigation
$prev = $pdo->prepare("SELECT id, name_ar FROM places WHERE id < :id ORDER BY id DESC LIMIT 1");
$prev->execute(['id' => $id]);
$prev = $prev->fetch();

$next = $pdo->prepare("SELECT id, name_ar FROM places WHERE id > :id ORDER BY id ASC LIMIT 1");
$next->execute(['id' => $id]);
$next = $next->fetch();

$landmarks = parse_landmarks($place['landmarks'] ?? '');
$gallery_images = array_filter([
    $place['gallery_image_1'] ?? null,
    $place['gallery_image_2'] ?? null,
    $place['gallery_image_3'] ?? null,
]);

$page_title    = $place['name_ar'];
$page_active   = 'gallery';
$page_class    = 'page-place';
$page_scripts  = ['js/lightbox.js'];

require __DIR__ . '/includes/header.php';
?>

<section class="place-hero" aria-labelledby="place-title">
  <div class="place-hero-photo">
    <?php if (!empty($place['hero_image'])): ?>
      <img src="<?= e(image_url($place['hero_image'])) ?>" alt="<?= e($place['name_ar']) ?>" loading="eager" fetchpriority="high">
    <?php else: ?>
      <div class="placeholder-gradient" style="--ph-h: <?= ($id * 47) % 360 ?>; width:100%; height:100%;"></div>
    <?php endif; ?>
    <div class="place-hero-veil"></div>
  </div>
  <div class="container place-hero-content">
    <p class="place-hero-eyebrow"><?php
        $type = $place['region_type'];
        $name = $place['name_ar'];
        if (mb_strtolower($type, 'UTF-8') === mb_strtolower($name, 'UTF-8')) {
          echo 'المملكة العربية السعودية';
        } else {
          echo e($type) . ' · المملكة العربية السعودية';
        }
      ?></p>
    <h1 id="place-title" class="place-hero-title"><?= e($place['name_ar']) ?></h1>
    <?php if (!empty($place['tagline'])): ?>
      <p class="place-hero-tagline"><?= e($place['tagline']) ?></p>
    <?php endif; ?>
  </div>
</section>

<section class="section section-tight">
  <div class="container container-narrow place-lede">
    <?php
      $paragraphs = preg_split('/\r?\n\r?\n/', trim($place['description']));
      foreach ($paragraphs as $i => $para):
        $para = trim($para);
        if ($para === '') continue;
    ?>
      <p<?= $i === 0 ? ' class="place-lede-first"' : '' ?>><?= nl2br(e($para)) ?></p>
    <?php endforeach; ?>
  </div>
</section>

<section class="section-tight">
  <div class="container">
    <div class="fact-strip">
      <p class="eyebrow fact-strip-label">معلومات سريعة</p>
      <dl class="fact-grid">
        <?php if (!empty($place['cities'])): ?>
          <div><dt>المدن</dt><dd><?= e($place['cities']) ?></dd></div>
        <?php endif; ?>
        <?php if (!empty($place['location'])): ?>
          <div><dt>الموقع</dt><dd><?= e($place['location']) ?></dd></div>
        <?php endif; ?>
        <?php if (!empty($place['area_km2'])): ?>
          <div><dt>المساحة</dt><dd><?= e($place['area_km2']) ?></dd></div>
        <?php endif; ?>
        <?php if (!empty($place['founded'])): ?>
          <div><dt>التأسيس</dt><dd><?= e($place['founded']) ?></dd></div>
        <?php endif; ?>
        <?php if (!empty($place['features'])): ?>
          <div><dt>المميزات</dt><dd><?= e($place['features']) ?></dd></div>
        <?php endif; ?>
        <?php if (!empty($place['activities'])): ?>
          <div><dt>الأنشطة</dt><dd><?= e($place['activities']) ?></dd></div>
        <?php endif; ?>
      </dl>
    </div>
  </div>
</section>

<?php if (!empty($place['cities'])): ?>
<section class="section-tight">
  <div class="container container-narrow">
    <div class="cities-feature">
      <p class="eyebrow">أبرز المدن</p>
      <ul class="fact-cities-list">
        <?php foreach (array_map('trim', explode('،', $place['cities'])) as $city): ?>
          <?php if ($city !== ''): ?>
            <li><?= e($city) ?></li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if (!empty($landmarks)): ?>
<section class="section section-tight">
  <div class="container container-narrow">
    <div class="section-head">
      <p class="eyebrow">أبرز المعالم</p>
      <h2>ما لا يمكنك تفويته في <?= e($place['name_ar']) ?>.</h2>
    </div>
    <ul class="landmarks">
      <?php foreach ($landmarks as $i => $lm): ?>
        <li class="landmark">
          <span class="landmark-num"><?= str_pad((string)($i+1), 2, '0', STR_PAD_LEFT) ?></span>
          <div class="landmark-body">
            <h3 class="landmark-name"><?= e($lm['name']) ?></h3>
            <?php if ($lm['description']): ?>
              <p class="landmark-desc"><?= e($lm['description']) ?></p>
            <?php endif; ?>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
<?php endif; ?>

<?php if (!empty($gallery_images)): ?>
<section class="section section-tight">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow">معرض الصور</p>
      <h2>لمحات من المنطقة.</h2>
    </div>
    <div class="place-gallery" data-lightbox-group>
      <?php foreach ($gallery_images as $i => $img): ?>
        <button type="button" class="place-gallery-item place-gallery-item--<?= $i+1 ?>"
                data-lightbox-src="<?= e(image_url($img)) ?>"
                aria-label="عرض الصورة <?= $i+1 ?>">
          <img src="<?= e(image_url($img)) ?>" alt="" loading="lazy">
        </button>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="section-tight">
  <div class="container place-nav">
    <?php if ($prev): ?>
      <a class="place-nav-link" href="<?= url('place.php?id=' . (int)$prev['id']) ?>" rel="prev">
        <span class="place-nav-eyebrow">السابق</span>
        <span class="place-nav-name">→ <?= e($prev['name_ar']) ?></span>
      </a>
    <?php else: ?><span></span><?php endif; ?>

    <a class="place-nav-back" href="<?= url('gallery.php') ?>">↩ العودة إلى المعرض</a>

    <?php if ($next): ?>
      <a class="place-nav-link place-nav-link--next" href="<?= url('place.php?id=' . (int)$next['id']) ?>" rel="next">
        <span class="place-nav-eyebrow">التالي</span>
        <span class="place-nav-name"><?= e($next['name_ar']) ?> ←</span>
      </a>
    <?php else: ?><span></span><?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
