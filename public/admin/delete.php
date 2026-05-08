<?php
require_once __DIR__ . '/../includes/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('admin/dashboard.php'));
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    flash_set('error', 'معرف السجل غير صالح.');
    header('Location: ' . url('admin/dashboard.php'));
    exit;
}

$stmt = $pdo->prepare("SELECT name_ar FROM places WHERE id = :id LIMIT 1");
$stmt->execute(['id' => $id]);
$row = $stmt->fetch();

if (!$row) {
    flash_set('error', 'السجل غير موجود — قد يكون قد حُذف بالفعل.');
    header('Location: ' . url('admin/dashboard.php'));
    exit;
}

$del = $pdo->prepare("DELETE FROM places WHERE id = :id");
$del->execute(['id' => $id]);

flash_set('success', 'تم حذف "' . $row['name_ar'] . '" بنجاح.');
header('Location: ' . url('admin/dashboard.php'));
exit;
