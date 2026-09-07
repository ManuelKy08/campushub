<?php
/**
 * CampusHub - Sistem Autentikasi dan Session Management
 * Fitur: Login, Register, Logout, Remember Me, Forgot Password
 * Keamanan: PDO Prepared Statements, bcrypt, Session Regenerasi
 */
declare(strict_types=1);

session_start();

// Prevent session fixation
if (isset($_SESSION['user_id'])) {
    session_regenerate_id(true, true);
}

// Konfigurasi session
$session_name = 'campushub_user';
$session_lifetime = 1800; // 30 menit
ini_set('session.gc_maxlifetime', $session_lifetime);
session_set_cookie_params([
    'lifetime' => $session_lifetime,
    'path' => '/',
    'domain' => '',
    'secure' => false, // Set true jika HTTPS
    'httponly' => true,
    'samesite' => 'Lax',
]);

// Inisialisasi session nama kustom
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pastikan nama session konsisten
if (session_name($session_name) !== $session_name) {
    session_name($session_name);
    session_start();
}

// Helper: Cek apakah user sudah login
function is_logged_in(): bool {
    return !empty($_SESSION[$session_name]);
}

// Helper: Ambil data user login
function get_user(): ?array {
    return $_SESSION[$session_name] ?? null;
}

// Helper: Login user
function login_user(int $user_id, string $email, string $name, string $role): void {
    $_SESSION[$session_name] = [
        'user_id'    => $user_id,
        'email'      => $email,
        'name'       => $name,
        'role'       => $role,
        'logged_in_at' => time(),
    ];
}

// Helper: Logout user
function logout_user(): void {
    // Hapus semua session data
    $_SESSION = [];
    
    // Hapus cookie session
    if (isset($_COokie[$session_name])) {
        setcookie(
            $session_name,
            '',
            time() - 86400, // 1 day in the past
            '/',
            '',
            false,
            true // httponly
        );
    }
    
    // Hancurkan session
    session_destroy();
}

// Middleware: Cek otentikasi
function require_login(): void {
    if (!is_logged_in()) {
        // Simpan URL yang diminta untuk redirect kembali setelah login
        $_SESSION['target_url'] = $_SERVER['REQUEST_URI'];
        header('Location: /login.php?expired=1');
        exit;
    }
}

// Middleware: Cek role
function require_role(string $allowed_role): void {
    if (!is_logged_in()) {
        require_login();
    }
    
    $user = get_user();
    if ($user['role'] !== $allowed_role) {
        // Redirect ke dashboard mahasiswa untuk role salah
        header('Location: /dashboard.php');
        exit;
    }
}