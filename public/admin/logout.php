<?php
require_once __DIR__ . '/../includes/auth.php';

// Accept both GET and POST so the sidebar form works either way.
admin_logout();
header('Location: ' . url('admin/login.php'));
exit;
