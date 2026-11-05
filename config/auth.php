<?php
require_once __DIR__ . '/env.php';

function secure_session_start()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);

    session_start();
}

function require_login_page($loginPath = 'login.php')
{
    secure_session_start();

    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . $loginPath);
        exit;
    }
}

function require_admin_page($loginPath = '../login.php')
{
    secure_session_start();

    if (($_SESSION['role'] ?? '') !== 'admin') {
        header('Location: ' . $loginPath);
        exit;
    }
}

function json_fail($code, $message)
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'status' => 'error', 'message' => $message]);
    exit;
}

function api_require_post()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        json_fail(405, 'Invalid request method');
    }
}

function api_require_login()
{
    secure_session_start();

    if (!isset($_SESSION['user_id'])) {
        json_fail(401, 'Login required');
    }
}

function api_require_admin()
{
    secure_session_start();

    if (!isset($_SESSION['user_id'])) {
        json_fail(401, 'Login required');
    }

    if (($_SESSION['role'] ?? '') !== 'admin') {
        json_fail(403, 'Admin access required');
    }
}

function csrf_token()
{
    secure_session_start();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES) . '">';
}

function csrf_valid()
{
    secure_session_start();
    $sent = $_POST['csrf_token'] ?? '';

    return is_string($sent) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $sent);
}
