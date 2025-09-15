<?php
// helpers/auth.php
session_start();

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: /lms/public/login.php');
        exit;
    }
}

function require_role($role) {
    require_login();
    if ($_SESSION['role'] !== $role) {
        // simple access denied
        http_response_code(403);
        echo "403 Forbidden - requires role: $role";
        exit;
    }
}
