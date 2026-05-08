<?php
require_once __DIR__ . '/auth.php';
require_admin();

$page_title  = $page_title  ?? 'لوحة التحكم';
$page_active = $page_active ?? 'dashboard';

$flash = flash_pop();
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#f6f3ec" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#0f1d22" media="(prefers-color-scheme: dark)">
<title><?= e($page_title) ?> — لوحة التحكم</title>

<link rel="stylesheet" href="<?= asset('css/tokens.css') ?>">
<link rel="stylesheet" href="<?= asset('css/base.css') ?>">
<link rel="stylesheet" href="<?= asset('css/admin.css') ?>">

<script>
  (function () {
    try {
      var t = localStorage.getItem('ds-theme');
      if (t === 'dark' || t === 'light') document.documentElement.setAttribute('data-theme', t);
      else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) document.documentElement.setAttribute('data-theme', 'dark');
    } catch (e) {}
  })();
</script>
<link rel="icon" type="image/svg+xml" href="<?= asset('images/brand-mark.svg') ?>">
</head>
<body class="admin">

<aside class="admin-side" aria-label="قائمة التحكم">
  <a class="admin-brand" href="<?= url('admin/dashboard.php') ?>">
    <span class="nav-brand-mark" aria-hidden="true"></span>
    <span><strong>لوحة المشرف</strong><span class="admin-brand-sub">اكتشف السعودية</span></span>
  </a>

  <nav class="admin-nav" aria-label="الأقسام">
    <a class="admin-nav-link <?= $page_active === 'dashboard' ? 'is-active' : '' ?>" href="<?= url('admin/dashboard.php') ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>
      <span>إدارة المحتوى</span>
    </a>
  </nav>

  <div class="admin-side-bottom">
    <a class="admin-side-link" href="<?= url('index.php') ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M5 12l7-7 7 7"/><path d="M5 12v8h14v-8"/></svg>
      <span>زيارة الموقع</span>
    </a>
    <form action="<?= url('admin/logout.php') ?>" method="post" class="admin-side-logout">
      <button type="submit" class="admin-side-link admin-side-link--danger">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/></svg>
        <span>تسجيل الخروج</span>
      </button>
    </form>
  </div>
</aside>

<main class="admin-main">
  <?php if ($flash): ?>
    <div class="flash flash-<?= e($flash['type']) ?>" role="status">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <?php if ($flash['type'] === 'success'): ?>
          <path d="M5 13l4 4L19 7"/>
        <?php elseif ($flash['type'] === 'error'): ?>
          <circle cx="12" cy="12" r="9"/><path d="M12 8v4"/><path d="M12 16h.01"/>
        <?php else: ?>
          <circle cx="12" cy="12" r="9"/><path d="M12 16v-5"/><path d="M12 8h.01"/>
        <?php endif; ?>
      </svg>
      <span><?= e($flash['msg']) ?></span>
      <button type="button" class="flash-close" aria-label="إغلاق" onclick="this.parentElement.remove()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M6 18 18 6"/></svg>
      </button>
    </div>
  <?php endif; ?>
