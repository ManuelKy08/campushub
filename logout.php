<?php
/**
 * CampusHub - Halaman Logout
 * Menghancurkan session dan redirect ke login
 */
require_once __DIR__ . '/../includes/auth.php';

logout_user();

// Redirect ke login
header('Location: /login.php');
exit;