<?php
/**
 * CampusHub - Sistem Autentikasi dan Session Management
 * Fitur: Login, Register, Logout, Remember Me, Forgot Password
 * Keamanan: PDO Prepared Statements, bcrypt, Session Regenerasi
 */
declare(strict_types=1);

$session_name = 'campushub_user';
$session_lifetime = 1800;

ini_set('session.gc_maxlifetime', (string)$session_lifetime);
session_set_cookie_params([
    'lifetime' => $session_lifetime,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_name($session_name);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    session_regenerate_id(true);
}

function is_logged_in(): bool {
    return !empty($_SESSION[$GLOBALS['session_name']]);
}

function get_user(): ?array {
    return $_SESSION[$GLOBALS['session_name']] ?? null;
}

function login_user(int $user_id, string $email, string $name, string $role): void {
    $_SESSION[$GLOBALS['session_name']] = [
        'user_id'    => $user_id,
        'email'      => $email,
        'name'       => $name,
        'role'       => $role,
        'logged_in_at' => time(),
    ];
}

function logout_user(): void {
    $sn = $GLOBALS['session_name'];
    $_SESSION = [];
    if (isset($_COOKIE[$sn])) {
        setcookie($sn, '', time() - 86400, '/', '', false, true);
    }
    session_destroy();
}

function require_login(): void {
    if (!is_logged_in()) {
        $_SESSION['target_url'] = $_SERVER['REQUEST_URI'];
        header('Location: /login.php?expired=1');
        exit;
    }
}

function require_role(string $allowed_role): void {
    if (!is_logged_in()) {
        require_login();
    }
    $user = get_user();
    if ($user['role'] !== $allowed_role) {
        header('Location: /dashboard.php');
        exit;
    }
}
