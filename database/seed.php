<?php
if (!defined('IN_CAMPUSHUB')) die('Akses dilarang');

$host = 'localhost';
$db   = 'campushub';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:unix_socket=/home/rrsec/mysql-data/mysqld.sock;host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "=== Starting Database Seeding ===\n\n";
    
    // 1. Insert sample users
    echo "1. Inserting sample users...\n";
    $admin_hash = '$2y$12$PYTB0WawXgYLDBuaFgUaxu1AGKBPbljeViOiex5mKgnrdt51BEc4y';
    $pdo->prepare("
        INSERT INTO users (full_name, nim, email, password_hash, role, program_study, semester, is_active) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 1)
    ")->execute(['Admin Sistem', '0000001', 'admin@campushub.id', $admin_hash, 'admin', 'Teknologi Informasi', 1]);
    
    $students = [
        ['Budi Santoso', '210101', 'budi@university.id', 'Sistem Informasi', 4],
        ['Aisha Roisha', '210102', 'aisha@university.id', 'Desain Komunikasi Visual', 2],
        ['Putra Amin', '210103', 'putra@university.id', 'Teknologi Informasi', 3],
        ['Citra Permata', '210104', 'citra@university.id', 'Ilmu Komputer', 5],
        ['Raka Pratama', '210105', 'raka@university.id', 'Sistem Informasi', 2],
    ];
    
    foreach ($students as $student) {
        $pdo->prepare("
            INSERT INTO users (full_name, nim, email, password_hash, role, program_study, semester, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 1)
        ")->execute([$student[0], $student[1], $student[2], $admin_hash, 'student', $student[3], $student[4], 1]);
    }
    echo "   - " . (count($students) + 1) . " users inserted\n";
    
    // 2. Insert sample courses
    echo "2. Inserting sample courses...\n";
    $courses = [
        ['IF-231', 'Pemrograman Web', 3, 2, 'Pembuatan aplikasi web modern', 'Dr. Sarah', 'Senin-Rabu 08.00-10.00'],
        ['IF-241', 'Basis Data', 3, 3, 'Desain dan implementasi basis data', 'Dr. Budi', 'Selasa-Kamis 10.00-12.00'],
        ['IF-321', 'Jaringan Komputer', 3, 5, 'Protokol dan arsitekom jaringan', 'Ir. Andi', 'Jumat 13.00-15.00'],
        ['IF-331', 'Sistem Operasi', 3, 3, 'Konsep dan implementasi sistem operasi', 'Pak Joko', 'Senin-Kamis 14.00-16.00'],
        ['IF-341', 'Pemrograman Mobil', 3, 6, 'Pengembangan aplikasi untuk mobil', 'Siti', 'Selasa-Rabu 16.00-18.00'],
    ];
    
    foreach ($courses as $course) {
        $pdo->prepare("
            INSERT INTO courses (code, name, credits, semester, description, professor, schedule, color_code) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ")->execute($course);
    }
    echo "   - " . count($courses) . " courses inserted\n";
    
    // 3. Insert sample assignments
    echo "3. Inserting sample assignments...\n";
    $assignments = [
        [1, 'Projek Akhir Web', 'Buat aplikasi CRUD dengan PHP', '2026-09-15 23:59:00', 'high', 'pending'],
        [1, 'Tugas Mingguan 1', 'Setup environment development', '2026-09-08 23:59:00', 'medium', 'pending'],
        [2, 'Desain ERD', 'Buat desain ERD untuk kasus pedagang', '2026-09-12 23:59:00', 'high', 'pending'],
        [3, 'Laporan Latihan', 'Latihan soal jaringan Komputer', '2026-09-18 23:59:00', 'low', 'pending'],
        [3, 'Tugas Rumah 3', 'Konfigurasi ruter dasar', '2026-09-20 23:59:00', 'medium', 'pending'],
    ];
    
    foreach ($assignments as $assignment) {
        $pdo->prepare("
            INSERT INTO assignments (course_id, title, description, deadline, priority, status) 
            VALUES (?, ?, ?, ?, ?, ?)
        ")->execute($assignment);
    }
    echo "   - " . count($assignments) . " assignments inserted\n";
    
    // 4. Insert sample materials
    echo "4. Inserting sample materials...\n";
    $materials = [
        [1, 'Pendahuluan PHP', 'Pengenalan dasar PHP', 'pdf', null],
        [1, 'Video: Setup XAMPP', 'Cara install dan konfigurasi XAMPP', 'video', 'https://www.youtube.com/watch?v=XAMPP-setup'],
        [2, 'Bab 1: Normalisasi', 'Bab 1 dari buku Basis Data', 'pdf', null],
        [2, 'Video: Tabel Relasi', 'Cara merelasi tabel', 'video', 'https://www.youtube.com/watch?v=DB-relations'],
        [3, 'Video: Konfigurasi Router', 'Cara mengkonfigurasi router Cisco', 'video', 'https://www.youtube.com/watch?v=Router-config'],
    ];
    
    foreach ($materials as $material) {
        $pdo->prepare("
            INSERT INTO materials (course_id, title, description, file_type, url_link) 
            VALUES (?, ?, ?, ?, ?)
        ")->execute($material);
    }
    echo "   - " . count($materials) . " materials inserted\n";
    
    // 5. Insert sample notes
    echo "5. Inserting sample notes...\n";
    $notes = [
        [2, 1, 'Catatan Pertemuan 1', 'Pengenalan PHP dan environment setup', 'lecture'],
        [2, 1, 'Catatan Pertemuan 2', 'Variabel dan tipe data', 'lecture'],
        [3, 2, 'Catatan Database', 'Konsep Normalisasi 1NF, 2NF, 3NF', 'study'],
    ];
    
    foreach ($notes as $note) {
        $pdo->prepare("
            INSERT INTO notes (user_id, course_id, title, content, category) 
            VALUES (?, ?, ?, ?, ?)
        ")->execute($note);
    }
    echo "   - " . count($notes) . " notes inserted\n";
    
    // 6. Insert sample events
    echo "6. Inserting sample events...\n";
    $events = [
        ['Deadline: Projek Akhir Web', 'Membuat aplikasi CRUD dengan PHP', '2026-09-15 23:59:00', '2026-09-15 23:59:00', 'deadline'],
        ['Deadline: Desain ERD', 'Buat desain ERD untuk kasus pedagang', '2026-09-12 23:59:00', '2026-09-12 23:59:00', 'deadline'],
        ['Hari Ulang Tahun Kampus', 'Festival seni dan budaya', '2026-09-20 00:00:00', '2026-09-20 23:59:00', 'event'],
    ];
    
    foreach ($events as $event) {
        $pdo->prepare("
            INSERT INTO events (title, description, start_time, end_time, type) 
            VALUES (?, ?, ?, ?, ?)
        ")->execute($event);
    }
    echo "   - " . count($events) . " events inserted\n";
    
    // 7. Insert sample notifications
    echo "7. Inserting sample notifications...\n";
    $notifications = [
        [1, 'Tugas Baru: Projek Akhir Web', 'Membuat aplikasi CRUD dengan PHP deadline 15 September 2026', 'task'],
        [2, 'Tugas Telat: Tugas Mingguan 1', 'Tugas Mingguan 1 sudah lewat', 'task'],
        [3, 'Materi Baru: Pendahuluan PHP', 'Material baru untuk Pembelajaran Web ditambahkan', 'material'],
    ];
    
    foreach ($notifications as $notif) {
        $pdo->prepare("
            INSERT INTO notifications (user_id, title, message, type) 
            VALUES (?, ?, ?, ?)
        ")->execute($notif);
    }
    echo "   - " . count($notifications) . " notifications inserted\n";
    
    echo "\n=== Database Seeding Complete ===\n";
    echo "Total users: " . (count($students) + 1) . "\n";
    echo "Total courses: " . count($courses) . "\n";
    echo "Total assignments: " . count($assignments) . "\n";
    echo "Total materials: " . count($materials) . "\n";
    echo "Total notes: " . count($notes) . "\n";
    echo "Total events: " . count($events) . "\n";
    echo "Total notifications: " . count($notifications) . "\n";
    
} catch (PDOException $e) {
    error_log('Database seeding error: ' . $e->getMessage());
    die('Seeding failed: ' . $e->getMessage());
}
