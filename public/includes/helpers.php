<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';

function e(?string $s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function url(string $path = ''): string {
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

function asset(string $path): string {
    return url('assets/' . ltrim($path, '/'));
}

function flash_set(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function flash_pop(): ?array {
    if (empty($_SESSION['flash'])) return null;
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $f;
}

function truncate(string $s, int $n = 80): string {
    $s = trim($s);
    if (mb_strlen($s, 'UTF-8') <= $n) return $s;
    return mb_substr($s, 0, $n - 1, 'UTF-8') . '…';
}

function parse_landmarks(?string $raw): array {
    if (!$raw) return [];
    $out = [];
    foreach (preg_split('/\r?\n/', $raw) as $line) {
        $line = trim($line);
        if ($line === '') continue;
        if (str_contains($line, '|')) {
            [$name, $desc] = array_map('trim', explode('|', $line, 2));
            $out[] = ['name' => $name, 'description' => $desc];
        } else {
            $out[] = ['name' => $line, 'description' => ''];
        }
    }
    return $out;
}

function handle_image_upload(string $field, array $allowed = ['image/jpeg','image/png','image/webp']): ?string {
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('حدث خطأ أثناء رفع الصورة.');
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($f['tmp_name']);
    if (!in_array($mime, $allowed, true)) {
        throw new RuntimeException('نوع الصورة غير مسموح به.');
    }
    $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime];
    $name = bin2hex(random_bytes(8)) . '.' . $ext;
    $dest = __DIR__ . '/../uploads/' . $name;
    if (!move_uploaded_file($f['tmp_name'], $dest)) {
        throw new RuntimeException('تعذر حفظ الصورة على الخادم.');
    }
    return 'uploads/' . $name;
}

function image_url(?string $stored): string {
    if (!$stored) return asset('images/placeholder.svg');
    if (str_starts_with($stored, 'http://') || str_starts_with($stored, 'https://')) {
        return $stored;
    }
    return url($stored);
}
