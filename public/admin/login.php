<?php
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

if (is_admin()) {
    header('Location: ' . url('admin/dashboard.php'));
    exit;
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = (string)($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'يرجى تعبئة جميع الحقول.';
    } else {
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM admins WHERE username = :u LIMIT 1");
        $stmt->execute(['u' => $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            admin_login((int)$admin['id'], $admin['username']);
            header('Location: ' . url('admin/dashboard.php'));
            exit;
        }
        $error = 'بيانات الدخول غير صحيحة.';
    }
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="theme-color" content="#f6f3ec" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#0f1d22" media="(prefers-color-scheme: dark)">
<title>تسجيل دخول المشرف — اكتشف السعودية</title>

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
<body class="admin admin-login-body">

<div class="admin-login-shell">
  <a class="admin-login-brand" href="<?= url('index.php') ?>">
    <span class="nav-brand-mark" aria-hidden="true"></span>
    <span>اكتشف السعودية</span>
  </a>

  <div class="admin-login-card">
    <p class="eyebrow" style="color: var(--ink-soft); margin-bottom: var(--s-2);">منطقة المشرف</p>
    <h1 class="admin-login-title">تسجيل دخول المشرف</h1>
    <p class="admin-login-sub muted">للوصول إلى لوحة التحكم وإدارة محتوى المناطق.</p>

    <form method="post" novalidate id="login-form" autocomplete="off">
      <div class="field<?= $error ? ' has-error' : '' ?>">
        <label for="username">اسم المستخدم</label>
        <input id="username" name="username" type="text" required
               value="<?= e($username) ?>"
               aria-describedby="username-help">
        <p id="username-help" class="field-help">المستخدم الافتراضي: <code>admin</code></p>
        <p class="field-error" data-error="username">يرجى إدخال اسم المستخدم.</p>
      </div>

      <div class="field<?= $error ? ' has-error' : '' ?>">
        <label for="password">كلمة المرور</label>
        <input id="password" name="password" type="password" required
               aria-describedby="password-help">
        <p id="password-help" class="field-help">كلمة المرور الافتراضية: <code>admin123</code></p>
        <p class="field-error" data-error="password">
          <?= $error ? e($error) : 'يرجى إدخال كلمة المرور.' ?>
        </p>
      </div>

      <button type="submit" class="btn btn-primary btn-block">ادخل لوحة التحكم</button>
    </form>

    <p class="admin-login-back">
      <a class="link" href="<?= url('index.php') ?>">↩ العودة إلى الموقع</a>
    </p>
  </div>
</div>

<script>
(function () {
  const form = document.getElementById('login-form');
  if (!form) return;
  form.addEventListener('submit', (e) => {
    let ok = true;
    form.querySelectorAll('input[required]').forEach((input) => {
      const field = input.closest('.field');
      if (!input.value.trim()) {
        field.classList.add('has-error');
        ok = false;
      } else {
        field.classList.remove('has-error');
      }
    });
    if (!ok) e.preventDefault();
  });
})();
</script>

</body>
</html>
