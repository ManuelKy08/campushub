<?php
/**
 * CampusHub - Halaman Utama
 * Redirect cerdas: pengguna yang sudah login -> dashboard, tamu -> halaman login.
 */
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    header('Location: /dashboard.php');
} else {
    header('Location: /login.php');
}
exit;