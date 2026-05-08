<?php
require_once __DIR__ . '/../includes/helpers.php';
$page_active   = 'dashboard';
$page_scripts  = ['js/validate.js'];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: ' . url('admin/dashboard.php')); exit; }

$stmt = $pdo->prepare("SELECT * FROM places WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $id]);
$place = $stmt->fetch();
if (!$place) {
    flash_set('error', 'السجل غير موجود.');
    header('Location: ' . url('admin/dashboard.php'));
    exit;
}

$page_title = 'تحديث: ' . $place['name_ar'];

$errors = [];
$old = [
    'name_ar'     => $place['name_ar'],
    'region_type' => $place['region_type'],
    'tagline'     => $place['tagline'] ?? '',
    'description' => $place['description'],
    'location'    => $place['location'] ?? '',
    'area_km2'    => $place['area_km2'] ?? '',
    'founded'     => $place['founded']  ?? '',
    'features'    => $place['features'] ?? '',
    'activities'  => $place['activities'] ?? '',
    'landmarks'   => $place['landmarks'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($old) as $k) $old[$k] = trim((string)($_POST[$k] ?? ''));

    if ($old['name_ar'] === '') $errors['name_ar'] = 'يرجى إدخال اسم المنطقة.';
    $valid_types = ['المنطقة الوسطى','المنطقة الغربية','المنطقة الشرقية','المنطقة الشمالية','المنطقة الجنوبية'];
    if (!in_array($old['region_type'], $valid_types, true)) $errors['region_type'] = 'يرجى اختيار التصنيف.';
    if (mb_strlen($old['description'], 'UTF-8') < 30) $errors['description'] = 'الوصف قصير جداً (٣٠ حرفاً على الأقل).';

    // Image fields are optional on update — empty file = keep existing.
    $hero_path = $place['hero_image'];
    try {
        $new_hero = handle_image_upload('hero_image');
        if ($new_hero) $hero_path = $new_hero;
    } catch (RuntimeException $ex) {
        $errors['hero_image'] = $ex->getMessage();
    }

    $gallery = [
        $place['gallery_image_1'],
        $place['gallery_image_2'],
        $place['gallery_image_3'],
    ];
    foreach ([1, 2, 3] as $i) {
        try {
            $new_g = handle_image_upload("gallery_image_{$i}");
            if ($new_g) $gallery[$i - 1] = $new_g;
        } catch (RuntimeException $ex) {
            $errors["gallery_image_{$i}"] = $ex->getMessage();
        }
    }

    if (!$errors) {
        $stmt = $pdo->prepare(
            "UPDATE places SET
                name_ar = :name_ar,
                region_type = :region_type,
                tagline = :tagline,
                description = :description,
                location = :location,
                area_km2 = :area_km2,
                founded = :founded,
                features = :features,
                activities = :activities,
                landmarks = :landmarks,
                hero_image = :hero_image,
                gallery_image_1 = :g1,
                gallery_image_2 = :g2,
                gallery_image_3 = :g3
             WHERE id = :id"
        );
        $stmt->execute([
            'name_ar'     => $old['name_ar'],
            'region_type' => $old['region_type'],
            'tagline'     => $old['tagline'] ?: null,
            'description' => $old['description'],
            'location'    => $old['location'] ?: null,
            'area_km2'    => $old['area_km2'] ?: null,
            'founded'     => $old['founded']  ?: null,
            'features'    => $old['features'] ?: null,
            'activities'  => $old['activities'] ?: null,
            'landmarks'   => $old['landmarks'] ?: null,
            'hero_image'  => $hero_path,
            'g1'          => $gallery[0],
            'g2'          => $gallery[1],
            'g3'          => $gallery[2],
            'id'          => $id,
        ]);
        flash_set('success', 'تم تحديث "' . $old['name_ar'] . '" بنجاح.');
        header('Location: ' . url('admin/dashboard.php'));
        exit;
    }
}

require_once __DIR__ . '/../includes/admin-header.php';
?>

<div class="admin-page-head">
  <p class="eyebrow" style="color: var(--ink-soft);">
    <a href="<?= url('admin/dashboard.php') ?>" class="link">إدارة المحتوى</a> · تحديث
  </p>
  <h1>تحديث: <?= e($place['name_ar']) ?></h1>
  <p class="lede">عدّل البيانات أدناه. اتركْ حقول الصور فارغة للإبقاء على الصور الحالية.</p>
</div>

<?php if ($errors): ?>
  <div class="flash flash-error" role="alert">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
    <span>تعذر حفظ التعديلات. الرجاء مراجعة الحقول المظلّلة.</span>
  </div>
<?php endif; ?>

<form id="place-form" method="post" enctype="multipart/form-data" novalidate class="admin-form">
  <div>
    <section class="admin-form-section">
      <h2>بيانات أساسية</h2>

      <div class="field <?= isset($errors['name_ar']) ? 'has-error' : '' ?>">
        <label for="name_ar">اسم المنطقة <span class="required" aria-hidden="true"></span></label>
        <input id="name_ar" name="name_ar" type="text" required value="<?= e($old['name_ar']) ?>">
        <p class="field-error" data-error="name_ar"><?= e($errors['name_ar'] ?? 'يرجى إدخال اسم المنطقة.') ?></p>
      </div>

      <div class="field <?= isset($errors['region_type']) ? 'has-error' : '' ?>">
        <label for="region_type">التصنيف <span class="required" aria-hidden="true"></span></label>
        <select id="region_type" name="region_type" required>
          <?php foreach (['المنطقة الوسطى','المنطقة الغربية','المنطقة الشرقية','المنطقة الشمالية','المنطقة الجنوبية'] as $t): ?>
            <option value="<?= $t ?>" <?= $old['region_type'] === $t ? 'selected' : '' ?>><?= $t ?></option>
          <?php endforeach; ?>
        </select>
        <p class="field-error" data-error="region_type"><?= e($errors['region_type'] ?? 'يرجى اختيار التصنيف.') ?></p>
      </div>

      <div class="field">
        <label for="tagline">سطر تعريفي قصير</label>
        <input id="tagline" name="tagline" type="text" maxlength="160" value="<?= e($old['tagline']) ?>">
      </div>

      <div class="field">
        <label for="location">الموقع</label>
        <input id="location" name="location" type="text" value="<?= e($old['location']) ?>">
      </div>

      <div class="field" style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--s-3);">
        <div>
          <label for="area_km2">المساحة</label>
          <input id="area_km2" name="area_km2" type="text" value="<?= e($old['area_km2']) ?>">
        </div>
        <div>
          <label for="founded">التأسيس</label>
          <input id="founded" name="founded" type="text" value="<?= e($old['founded']) ?>">
        </div>
      </div>
    </section>

    <section class="admin-form-section">
      <h2>محتوى التعريف</h2>

      <div class="field <?= isset($errors['description']) ? 'has-error' : '' ?>">
        <label for="description">الوصف <span class="required" aria-hidden="true"></span></label>
        <textarea id="description" name="description" required minlength="30" rows="6"><?= e($old['description']) ?></textarea>
        <p class="field-error" data-error="description"><?= e($errors['description'] ?? 'الوصف قصير جداً (٣٠ حرفاً على الأقل).') ?></p>
      </div>

      <div class="field">
        <label for="features">المميزات</label>
        <input id="features" name="features" type="text" value="<?= e($old['features']) ?>">
      </div>

      <div class="field">
        <label for="activities">الأنشطة</label>
        <input id="activities" name="activities" type="text" value="<?= e($old['activities']) ?>">
      </div>

      <div class="field">
        <label for="landmarks">أبرز المعالم</label>
        <textarea id="landmarks" name="landmarks" rows="6"><?= e($old['landmarks']) ?></textarea>
        <p class="field-help">سطر لكل معلم. الصيغة: <code>اسم المعلم | وصف قصير</code>.</p>
      </div>
    </section>

    <section class="admin-form-section">
      <h2>الصور</h2>

      <div class="field <?= isset($errors['hero_image']) ? 'has-error' : '' ?>">
        <label for="hero_image">الصورة الرئيسية</label>
        <input id="hero_image" name="hero_image" type="file" accept="image/jpeg,image/png,image/webp"
               data-preview-target="#hero-preview">
        <p class="field-help">اتركْ هذا الحقل فارغاً للإبقاء على الصورة الحالية.</p>
        <?php if (isset($errors['hero_image'])): ?>
          <p class="field-error" style="display: block;"><?= e($errors['hero_image']) ?></p>
        <?php endif; ?>
      </div>

      <?php for ($i = 1; $i <= 3; $i++): ?>
        <div class="field">
          <label for="gallery_image_<?= $i ?>">صورة المعرض <?= ['الأولى','الثانية','الثالثة'][$i-1] ?></label>
          <input id="gallery_image_<?= $i ?>" name="gallery_image_<?= $i ?>" type="file" accept="image/jpeg,image/png,image/webp">
        </div>
      <?php endfor; ?>
    </section>

    <div class="admin-form-actions">
      <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
      <a href="<?= url('admin/dashboard.php') ?>" class="btn btn-ghost">إلغاء</a>
    </div>
  </div>

  <aside class="admin-form-aside">
    <h3>الصورة الرئيسية الحالية</h3>
    <div class="admin-preview" id="hero-preview">
      <?php if (!empty($place['hero_image'])): ?>
        <img src="<?= e(image_url($place['hero_image'])) ?>" alt="<?= e($place['name_ar']) ?>">
      <?php else: ?>
        <span>لا توجد صورة حالياً.</span>
      <?php endif; ?>
    </div>

    <?php
      $current_gallery = array_filter([
          $place['gallery_image_1'] ?? null,
          $place['gallery_image_2'] ?? null,
          $place['gallery_image_3'] ?? null,
      ]);
    ?>
    <?php if ($current_gallery): ?>
      <h3 style="margin-top: var(--s-5);">صور المعرض الحالية</h3>
      <div class="current-images">
        <?php foreach ($current_gallery as $img): ?>
          <div class="thumb"><img src="<?= e(image_url($img)) ?>" alt=""></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <p class="field-help" style="margin-top: var(--s-3);">
      ارفع صورة جديدة في أي من الحقول لاستبدال الصورة الحالية، أو اتركها فارغة للإبقاء عليها.
    </p>
  </aside>
</form>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>
