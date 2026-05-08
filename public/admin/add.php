<?php
require_once __DIR__ . '/../includes/helpers.php';
$page_title    = 'إضافة مكان جديد';
$page_active   = 'dashboard';
$page_scripts  = ['js/validate.js'];

$errors = [];
$old = [
    'name_ar' => '', 'region_type' => '', 'tagline' => '', 'description' => '',
    'location' => '', 'area_km2' => '', 'founded' => '',
    'features' => '', 'activities' => '', 'landmarks' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (array_keys($old) as $k) $old[$k] = trim((string)($_POST[$k] ?? ''));

    if ($old['name_ar'] === '') $errors['name_ar'] = 'يرجى إدخال اسم المنطقة.';
    $valid_types = ['المنطقة الوسطى','المنطقة الغربية','المنطقة الشرقية','المنطقة الشمالية','المنطقة الجنوبية'];
    if (!in_array($old['region_type'], $valid_types, true)) $errors['region_type'] = 'يرجى اختيار التصنيف.';
    if (mb_strlen($old['description'], 'UTF-8') < 30) $errors['description'] = 'الوصف قصير جداً (٣٠ حرفاً على الأقل).';

    // Hero image is required on add
    try {
        $hero_path = handle_image_upload('hero_image');
    } catch (RuntimeException $ex) {
        $errors['hero_image'] = $ex->getMessage();
        $hero_path = null;
    }
    if (!isset($errors['hero_image']) && !$hero_path) {
        $errors['hero_image'] = 'يرجى اختيار صورة رئيسية.';
    }

    // Optional gallery images
    $gallery = [null, null, null];
    foreach ([1, 2, 3] as $i) {
        try {
            $gallery[$i - 1] = handle_image_upload("gallery_image_{$i}");
        } catch (RuntimeException $ex) {
            $errors["gallery_image_{$i}"] = $ex->getMessage();
        }
    }

    if (!$errors) {
        $stmt = $pdo->prepare(
            "INSERT INTO places
             (name_ar, region_type, tagline, description, location, area_km2, founded,
              features, activities, landmarks,
              hero_image, gallery_image_1, gallery_image_2, gallery_image_3)
             VALUES
             (:name_ar, :region_type, :tagline, :description, :location, :area_km2, :founded,
              :features, :activities, :landmarks,
              :hero_image, :g1, :g2, :g3)"
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
        ]);
        flash_set('success', 'تمت إضافة "' . $old['name_ar'] . '" بنجاح.');
        header('Location: ' . url('admin/dashboard.php'));
        exit;
    }
}

require_once __DIR__ . '/../includes/admin-header.php';
?>

<div class="admin-page-head">
  <p class="eyebrow" style="color: var(--ink-soft);"><a href="<?= url('admin/dashboard.php') ?>" class="link">إدارة المحتوى</a> · جديد</p>
  <h1>إضافة مكان جديد</h1>
  <p class="lede">عبّئ المعلومات بعناية. الحقول المعلّمة بنقطة حمراء إجبارية.</p>
</div>

<?php if ($errors): ?>
  <div class="flash flash-error" role="alert">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
    <span>تعذر حفظ النموذج. الرجاء مراجعة الحقول المظلّلة.</span>
  </div>
<?php endif; ?>

<form id="place-form" method="post" enctype="multipart/form-data" novalidate class="admin-form">
  <div>
    <section class="admin-form-section">
      <h2>بيانات أساسية</h2>

      <div class="field <?= isset($errors['name_ar']) ? 'has-error' : '' ?>">
        <label for="name_ar">اسم المنطقة <span class="required" aria-hidden="true"></span></label>
        <input id="name_ar" name="name_ar" type="text" required
               value="<?= e($old['name_ar']) ?>"
               placeholder="مثال: الرياض">
        <p class="field-error" data-error="name_ar"><?= e($errors['name_ar'] ?? 'يرجى إدخال اسم المنطقة.') ?></p>
      </div>

      <div class="field <?= isset($errors['region_type']) ? 'has-error' : '' ?>">
        <label for="region_type">التصنيف <span class="required" aria-hidden="true"></span></label>
        <select id="region_type" name="region_type" required>
          <option value="">اختر التصنيف…</option>
          <?php foreach (['المنطقة الوسطى','المنطقة الغربية','المنطقة الشرقية','المنطقة الشمالية','المنطقة الجنوبية'] as $t): ?>
            <option value="<?= $t ?>" <?= $old['region_type'] === $t ? 'selected' : '' ?>><?= $t ?></option>
          <?php endforeach; ?>
        </select>
        <p class="field-error" data-error="region_type"><?= e($errors['region_type'] ?? 'يرجى اختيار التصنيف.') ?></p>
      </div>

      <div class="field">
        <label for="tagline">سطر تعريفي قصير</label>
        <input id="tagline" name="tagline" type="text" maxlength="160"
               value="<?= e($old['tagline']) ?>"
               placeholder="مثال: العاصمة، ومركز الحكاية">
        <p class="field-help">يظهر تحت اسم المنطقة في صفحة التفاصيل.</p>
      </div>

      <div class="field">
        <label for="location">الموقع</label>
        <input id="location" name="location" type="text" value="<?= e($old['location']) ?>"
               placeholder="مثال: وسط المملكة">
      </div>

      <div class="field" style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--s-3);">
        <div>
          <label for="area_km2">المساحة</label>
          <input id="area_km2" name="area_km2" type="text" value="<?= e($old['area_km2']) ?>"
                 placeholder="مثال: 380,000 كم²">
        </div>
        <div>
          <label for="founded">التأسيس</label>
          <input id="founded" name="founded" type="text" value="<?= e($old['founded']) ?>"
                 placeholder="مثال: 1727م">
        </div>
      </div>
    </section>

    <section class="admin-form-section">
      <h2>محتوى التعريف</h2>

      <div class="field <?= isset($errors['description']) ? 'has-error' : '' ?>">
        <label for="description">الوصف <span class="required" aria-hidden="true"></span></label>
        <textarea id="description" name="description" required minlength="30" rows="6"
                  placeholder="فقرة أو فقرتان تقدّمان المنطقة بسياقها الجغرافي والثقافي. افصل بين الفقرات بسطر فارغ."><?= e($old['description']) ?></textarea>
        <p class="field-help">سيظهر كنص افتتاحي في صفحة المنطقة (Lede). افصل بين الفقرات بسطر فارغ.</p>
        <p class="field-error" data-error="description"><?= e($errors['description'] ?? 'الوصف قصير جداً (٣٠ حرفاً على الأقل).') ?></p>
      </div>

      <div class="field">
        <label for="features">المميزات</label>
        <input id="features" name="features" type="text" value="<?= e($old['features']) ?>"
               placeholder="مثال: مواقع تاريخية، طبيعة جبلية، طقس معتدل">
      </div>

      <div class="field">
        <label for="activities">الأنشطة</label>
        <input id="activities" name="activities" type="text" value="<?= e($old['activities']) ?>"
               placeholder="مثال: رحلات صحراوية، تذوق التمر، زيارة المتاحف">
      </div>

      <div class="field">
        <label for="landmarks">أبرز المعالم</label>
        <textarea id="landmarks" name="landmarks" rows="6"
                  placeholder="ضع كل معلم في سطر منفصل، بصيغة:&#10;اسم المعلم | وصف قصير"><?= e($old['landmarks']) ?></textarea>
        <p class="field-help">سطر لكل معلم. الصيغة: <code>اسم المعلم | وصف قصير</code>.</p>
      </div>
    </section>

    <section class="admin-form-section">
      <h2>الصور</h2>

      <div class="field <?= isset($errors['hero_image']) ? 'has-error' : '' ?>">
        <label for="hero_image">الصورة الرئيسية <span class="required" aria-hidden="true"></span></label>
        <input id="hero_image" name="hero_image" type="file" accept="image/jpeg,image/png,image/webp" required
               data-preview-target="#hero-preview">
        <p class="field-help">يُفضّل صورة عريضة (16:9 أو أعرض).</p>
        <p class="field-error" data-error="hero_image"><?= e($errors['hero_image'] ?? 'يرجى اختيار صورة رئيسية.') ?></p>
      </div>

      <div class="field">
        <label for="gallery_image_1">صورة المعرض الأولى (اختياري)</label>
        <input id="gallery_image_1" name="gallery_image_1" type="file" accept="image/jpeg,image/png,image/webp">
      </div>
      <div class="field">
        <label for="gallery_image_2">صورة المعرض الثانية (اختياري)</label>
        <input id="gallery_image_2" name="gallery_image_2" type="file" accept="image/jpeg,image/png,image/webp">
      </div>
      <div class="field">
        <label for="gallery_image_3">صورة المعرض الثالثة (اختياري)</label>
        <input id="gallery_image_3" name="gallery_image_3" type="file" accept="image/jpeg,image/png,image/webp">
      </div>
    </section>

    <div class="admin-form-actions">
      <button type="submit" class="btn btn-primary">حفظ المكان</button>
      <a href="<?= url('admin/dashboard.php') ?>" class="btn btn-ghost">إلغاء</a>
    </div>
  </div>

  <aside class="admin-form-aside">
    <h3>معاينة الصورة الرئيسية</h3>
    <div class="admin-preview" id="hero-preview">
      <span>لم تُختر صورة بعد.</span>
    </div>
    <p class="field-help" style="margin-top: var(--s-3);">
      ستظهر هذه الصورة في الصفحة الرئيسية، صفحة المعرض، وفي رأس صفحة المنطقة.
    </p>
  </aside>
</form>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>
