<?php
require_once __DIR__ . '/helpers.php';

$page_title  = $page_title  ?? 'اكتشف السعودية';
$page_active = $page_active ?? 'home';
$page_class  = $page_class  ?? '';
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#f6f3ec" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#0f1d22" media="(prefers-color-scheme: dark)">
<title><?= e($page_title) ?> — اكتشف السعودية</title>

<meta name="description" content="رحلة بصرية في ثلاث عشرة منطقة من المملكة العربية السعودية: تاريخها، ثقافتها، ومعالمها.">

<link rel="stylesheet" href="<?= asset('css/tokens.css') ?>">
<link rel="stylesheet" href="<?= asset('css/base.css') ?>">
<link rel="stylesheet" href="<?= asset('css/layout.css') ?>">
<link rel="stylesheet" href="<?= asset('css/pages.css') ?>">

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
<body class="<?= e($page_class) ?>">

<header class="nav" data-nav>
  <div class="container nav-inner">
    <a href="<?= url('index.php') ?>" class="nav-brand" aria-label="اكتشف السعودية">
      <span class="nav-brand-mark" aria-hidden="true"></span>
      <span>اكتشف السعودية</span>
    </a>

    <button class="nav-toggle" type="button" data-nav-toggle aria-expanded="false" aria-label="فتح القائمة">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
    </button>

    <ul class="nav-links">
      <li><a class="nav-link <?= $page_active === 'home' ? 'is-active' : '' ?>" href="<?= url('index.php') ?>">الرئيسية</a></li>
      <li><a class="nav-link <?= $page_active === 'gallery' ? 'is-active' : '' ?>" href="<?= url('gallery.php') ?>">معرض المناطق</a></li>
      <li><a class="nav-link-cta" href="<?= url('admin/login.php') ?>">دخول المشرف</a></li>
      <li>
        <button class="theme-toggle" type="button" data-theme-toggle aria-label="تبديل الوضع الليلي" title="تبديل الوضع الليلي">
          <svg class="icon-sun"  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
          <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
        </button>
      </li>
    </ul>
  </div>
</header>

<main>
