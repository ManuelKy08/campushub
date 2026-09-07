<?php
/**
 * CampusHub - Konfigurasi Koneksi Database PDO
 * Menggunakan PDO dengan parameter khusus untuk MariaDB/Aurora
 */
declare(strict_types=1);

$dsn = 'mysql:unix_socket=/home/rrsec/mysql-data/mysql.sock;dbname=campushub;charset=utf8mb4';
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES   => false,
    // Untuk MariaDB: Menonaktifkan strict mode untuk kompatibilitas
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, 'root', '', $options);
    // Set hostname untuk referensi di seluruh aplikasi
    // Tidak perlu setProperty karena sudah menggunakan unix socket
} catch (PDOException $e) {
    // Log error ke file untuk debugging (jangan tampilkan ke user)
    error_log('Database connection failed: ' . $e->getMessage());
    die('Koneksi database gagal. Silakan hubungi administrator.');
}