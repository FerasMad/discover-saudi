<?php
require_once __DIR__ . '/../includes/helpers.php';
$page_title  = 'إدارة المحتوى';
$page_active = 'dashboard';

require_once __DIR__ . '/../includes/admin-header.php';

$places = $pdo->query(
    "SELECT id, name_ar, region_type, description, hero_image
     FROM places
     ORDER BY id ASC"
)->fetchAll();
$total = count($places);
$types = $pdo->query("SELECT DISTINCT region_type FROM places ORDER BY region_type ASC")->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="admin-page-head">
  <p class="eyebrow" style="color: var(--ink-soft);">لوحة التحكم</p>
  <h1>إدارة المحتوى</h1>
  <p class="lede">استعرض السجلات، أضف مكاناً جديداً، أو عدّل وامحو محتوى المناطق. كل تغيير ينعكس مباشرة على الموقع العام.</p>
</div>

<div class="admin-toolbar">
  <input type="search" id="t-search" placeholder="ابحث في السجلات…" aria-label="ابحث في السجلات">
  <select id="t-filter" aria-label="صفّ حسب النوع">
    <option value="">كل الأنواع</option>
    <?php foreach ($types as $t): ?>
      <option value="<?= e($t) ?>"><?= e($t) ?></option>
    <?php endforeach; ?>
  </select>
  <a class="btn btn-primary" href="<?= url('admin/add.php') ?>">
    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
    إضافة مكان جديد
  </a>
</div>

<div class="admin-table-wrap">
  <table class="admin-table">
    <thead>
      <tr>
        <th class="col-id">#</th>
        <th>المنطقة</th>
        <th>التصنيف</th>
        <th>الوصف</th>
        <th class="col-actions">الإجراءات</th>
      </tr>
    </thead>
    <tbody id="t-body">
      <?php if ($total === 0): ?>
        <tr>
          <td colspan="5" class="empty-table">
            <p>لم تُضف مناطق بعد.</p>
            <a class="btn btn-outline" href="<?= url('admin/add.php') ?>">إضافة أول مكان</a>
          </td>
        </tr>
      <?php else: ?>
        <?php foreach ($places as $p): ?>
          <tr data-name="<?= e(mb_strtolower($p['name_ar'], 'UTF-8')) ?>"
              data-type="<?= e($p['region_type']) ?>">
            <td class="col-id"><?= (int)$p['id'] ?></td>
            <td class="col-name"><?= e($p['name_ar']) ?></td>
            <td class="col-type"><?= e($p['region_type']) ?></td>
            <td class="col-desc"><?= e(truncate($p['description'], 90)) ?></td>
            <td class="col-actions">
              <a class="btn btn-outline btn-sm" href="<?= url('admin/update.php?id=' . (int)$p['id']) ?>">تعديل</a>
              <form method="post" action="<?= url('admin/delete.php') ?>"
                    onsubmit="return confirm('هل تريد حذف هذا السجل؟ لا يمكن التراجع عن هذا الإجراء.');">
                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                <button type="submit" class="btn btn-danger-outline btn-sm">حذف</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<p class="muted" style="margin-top: var(--s-4); font-size: var(--fs-meta);">
  إجمالي السجلات: <strong style="color: var(--ink);"><?= $total ?></strong>.
</p>

<script>
(function () {
  const search = document.getElementById('t-search');
  const filter = document.getElementById('t-filter');
  const rows = Array.from(document.querySelectorAll('#t-body tr[data-name]'));
  if (!search || !filter || rows.length === 0) return;

  function apply() {
    const q = (search.value || '').trim().toLowerCase();
    const t = filter.value;
    rows.forEach((row) => {
      const matches = (!q || row.dataset.name.includes(q)) && (!t || row.dataset.type === t);
      row.style.display = matches ? '' : 'none';
    });
  }
  search.addEventListener('input', apply);
  filter.addEventListener('change', apply);
})();
</script>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>
