<?php
require_once __DIR__ . '/helpers.php';

function is_admin(): bool {
    return !empty($_SESSION['admin_id']);
}

function require_admin(): void {
    if (!is_admin()) {
        header('Location: ' . url('admin/login.php'));
        exit;
    }
}

function admin_login(int $id, string $username): void {
    session_regenerate_id(true);
    $_SESSION['admin_id']       = $id;
    $_SESSION['admin_username'] = $username;
}

function admin_logout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}
