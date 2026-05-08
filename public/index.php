<?php
require_once __DIR__ . '/includes/helpers.php';

$page_title  = 'الرئيسية';
$page_active = 'home';
$page_class  = 'page-home';

// Pull featured regions for the preview grid (first 4 by id, ascending).
$featured = $pdo->query(
    "SELECT id, name_ar, region_type, tagline, cities, hero_image
     FROM places
     ORDER BY id ASC
     LIMIT 4"
)->fetchAll();

// Total region count (for the inset stat strip)
$total_count = (int) $pdo->query("SELECT COUNT(*) FROM places")->fetchColumn();

// A small set of hero candidates — uses the first featured region's image as the hero.
// (When all 13 are seeded, this rotates by day-of-week so the home feels alive.)
$hero_idx = $featured ? (int)date('w') % count($featured) : 0;
$hero_place = $featured[$hero_idx] ?? null;

require __DIR__ . '/includes/header.php';
?>

<section class="hero" aria-labelledby="hero-title">
  <div class="hero-photo">
    <?php if ($hero_place && $hero_place['hero_image']): ?>
      <img src="<?= e(image_url($hero_place['hero_image'])) ?>" alt="" loading="eager" fetchpriority="high">
    <?php else: ?>
      <div class="placeholder-gradient" style="--ph-h: 60; width:100%; height:100%;"></div>
    <?php endif; ?>
    <div class="hero-photo-veil"></div>
  </div>

  <div class="container hero-content">
    <p class="hero-eyebrow">موقع تعريفي بمناطق المملكة العربية السعودية</p>
    <h1 id="hero-title" class="hero-title">السعودية،<br>منطقةً منطقة.</h1>
    <p class="hero-sub">ثلاث عشرة منطقة، ومئات المدن والقرى. هذه قراءة في خريطتها.</p>
    <div class="hero-actions">
      <a class="btn btn-primary" href="<?= url('gallery.php') ?>">ابدأ الاستكشاف</a>
      <a class="btn btn-ghost" href="#about">تعرف على المشروع</a>
    </div>
  </div>

  <a href="#about" class="hero-scroll" aria-label="انتقل إلى المحتوى">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M12 4v14M6 13l6 6 6-6"/></svg>
  </a>
</section>

<section id="about" class="section glance">
  <div class="container">
    <div class="glance-grid">
      <div>
        <p class="eyebrow">المملكة في لمحة</p>
        <h2 class="glance-title">ليست بلداً واحداً، بل ثلاثة عشر إقليماً.</h2>
      </div>
      <div class="glance-body">
        <p>تمتدّ المملكة على نحو 2.15 مليون كم²، تجمع جبالاً خضراء جنوباً، وصحاري رملية شمالاً، وشواطئ مرجانية على البحر الأحمر، وواجهات حضرية على الخليج العربي. كل منطقة فيها لهجة وعمارة وذاكرة مختلفة، ضمن فضاء ثقافي واحد.</p>
        <p>هذا الموقع قراءة بصرية وثقافية لهذه الفسيفساء، لا دليلاً سياحياً. نختار المشاهد بعناية، ونروي ما خلفها.</p>
      </div>
    </div>

    <div class="stat-strip" role="list">
      <div role="listitem"><span class="stat-num"><?= e((string)max($total_count, 13)) ?></span><span class="stat-label">منطقة إدارية</span></div>
      <div role="listitem"><span class="stat-num">٢</span><span class="stat-label">ساحلان: الأحمر والعربي</span></div>
      <div role="listitem"><span class="stat-num">+٦</span><span class="stat-label">مواقع للتراث العالمي</span></div>
      <div role="listitem"><span class="stat-num">2.15M</span><span class="stat-label">كم² من التنوع</span></div>
    </div>
  </div>
</section>

<section class="section regions-pick">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow">ابدأ من هنا</p>
      <h2>أربع وجهات أوّلية.</h2>
      <p class="lede">اعرض البقية في المعرض الكامل، حيث تجد المناطق الثلاث عشرة جميعها.</p>
    </div>

    <?php if (count($featured) > 0): ?>
      <div class="pick-grid">
        <?php foreach (array_slice($featured, 0, 4) as $i => $p): ?>
          <a class="pick-tile<?= $i === 0 ? ' pick-tile--hero' : '' ?>"
             href="<?= url('place.php?id=' . (int)$p['id']) ?>"
             aria-label="<?= e($p['name_ar']) ?>">
            <div class="pick-photo">
              <?php if (!empty($p['hero_image'])): ?>
                <img src="<?= e(image_url($p['hero_image'])) ?>" alt="" loading="lazy">
              <?php else: ?>
                <div class="placeholder-gradient" style="--ph-h: <?= 30 + ($i * 50) ?>; width:100%; height:100%;"></div>
              <?php endif; ?>
              <div class="pick-veil"></div>
            </div>
            <div class="pick-meta">
              <?php if (mb_strtolower($p['name_ar'], 'UTF-8') !== mb_strtolower($p['region_type'], 'UTF-8')): ?>
                <span class="pick-eyebrow"><?= e($p['region_type']) ?></span>
              <?php endif; ?>
              <h3 class="pick-name"><?= e($p['name_ar']) ?></h3>
              <?php if (!empty($p['cities'])): ?>
                <p class="pick-cities"><?= e($p['cities']) ?></p>
              <?php endif; ?>
              <?php if (!empty($p['tagline'])): ?>
                <p class="pick-tagline"><?= e($p['tagline']) ?></p>
              <?php endif; ?>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="empty-state">
        <p>لم تُضف مناطق بعد. سجّل دخول المشرف لإضافة محتوى.</p>
        <a class="btn btn-outline" href="<?= url('admin/login.php') ?>">دخول المشرف</a>
      </div>
    <?php endif; ?>

    <div class="pick-cta">
      <a class="link" href="<?= url('gallery.php') ?>">اعرض المناطق الثلاث عشرة ←</a>
    </div>
  </div>
</section>

<section class="section section-tight why">
  <div class="container">
    <div class="section-head">
      <p class="eyebrow">عن المشروع</p>
      <h2>موقع تعريفي عن مناطق المملكة وأبرز مدنها.</h2>
    </div>
    <div class="team">
      <p class="eyebrow team-label">أعضاء الفريق</p>
      <ul class="team-list">
        <li>فراس مدخلي</li>
        <li>تميم العصيمي</li>
      </ul>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
