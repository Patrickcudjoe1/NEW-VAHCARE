<?php
// Core functions for VAH Care

function start_secure_session() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function generate_csrf_token() {
    start_secure_session();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    start_secure_session();
    if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
        return true;
    }
    return false;
}

function is_admin_logged_in() {
    start_secure_session();
    return isset($_SESSION['admin_id']);
}

function redirect_if_not_admin() {
    if (!is_admin_logged_in()) {
        header("Location: login.php");
        exit;
    }
}
?>
