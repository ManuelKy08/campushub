CREATE DATABASE IF NOT EXISTS campushub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE campushub;

CREATE TABLE users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    nim VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('student','admin') DEFAULT 'student',
    program_study VARCHAR(100) DEFAULT '',
    semester INT DEFAULT 1,
    avatar VARCHAR(255) DEFAULT 'default-avatar.svg',
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_nim (nim)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE courses (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL,
    name VARCHAR(150) NOT NULL,
    credits INT DEFAULT 3,
    semester INT DEFAULT 1,
    description TEXT NULL,
    professor VARCHAR(100) DEFAULT '',
    schedule VARCHAR(100) DEFAULT '',
    room VARCHAR(50) DEFAULT '',
    color_code VARCHAR(7) DEFAULT '#3b82f6',
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE assignments (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    course_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    deadline DATETIME NOT NULL,
    priority ENUM('low','medium','high','urgent') DEFAULT 'medium',
    status ENUM('pending','in_progress','completed','late') DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_course (course_id),
    INDEX idx_status (status),
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE materials (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    course_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    file_type VARCHAR(20) DEFAULT 'pdf',
    url_link VARCHAR(500) DEFAULT '',
    priority ENUM('low','medium','high') DEFAULT 'medium',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_course (course_id),
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE notes (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    course_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NULL,
    category VARCHAR(50) DEFAULT 'lecture',
    is_favorite TINYINT(1) DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_course (course_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE events (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NOT NULL,
    type ENUM('event','deadline','exam') DEFAULT 'event',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_start (start_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE notifications (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NULL,
    type VARCHAR(50) DEFAULT 'task',
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE password_resets (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin user (password: admin123)
INSERT INTO users (full_name, nim, email, password_hash, role, program_study, semester, is_active) VALUES
('Admin Sistem', '0000001', 'admin@campushub.id', '$2y$12$PYTB0WawXgYLDBuaFgUaxu1AGKBPbljeViOiex5mKgnrdt51BEc4y', 'admin', 'Teknologi Informasi', 1, 1);

-- Sample mahasiswa
INSERT INTO users (full_name, nim, email, password_hash, role, program_study, semester, is_active) VALUES
('Budi Santoso', '210101', 'budi@university.id', '$2y$12$PYTB0WawXgYLDBuaFgUaxu1AGKBPbljeViOiex5mKgnrdt51BEc4y', 'student', 'Sistem Informasi', 4, 1),
('Aisha Roisha', '210102', 'aisha@university.id', '$2y$12$PYTB0WawXgYLDBuaFgUaxu1AGKBPbljeViOiex5mKgnrdt51BEc4y', 'student', 'Desain Komunikasi Visual', 2, 1),
('Putra Amin', '210103', 'putra@university.id', '$2y$12$PYTB0WawXgYLDBuaFgUaxu1AGKBPbljeViOiex5mKgnrdt51BEc4y', 'student', 'Sistem Informasi', 3, 1),
('Citra Permata', '210104', 'citra@university.id', '$2y$12$PYTB0WawXgYLDBuaFgUaxu1AGKBPbljeViOiex5mKgnrdt51BEc4y', 'student', 'Ilmu Komputer', 5, 1),
('Raka Pratama', '210105', 'raka@university.id', '$2y$12$PYTB0WawXgYLDBuaFgUaxu1AGKBPbljeViOiex5mKgnrdt51BEc4y', 'student', 'Sistem Informasi', 2, 1);

-- Sample courses
INSERT INTO courses (code, name, credits, semester, description, professor, schedule, color_code) VALUES
('IF-231', 'Pemrograman Web', 3, 2, 'Pembuatan aplikasi web modern', 'Dr. Sarah', 'Senin-Rabu 08.00-10.00', '#3b82f6'),
('IF-241', 'Basis Data', 3, 3, 'Desain dan implementasi basis data', 'Dr. Budi', 'Selasa-Kamis 10.00-12.00', '#ef4444'),
('IF-321', 'Jaringan Komputer', 3, 5, 'Protokol dan arsitekom jaringan', 'Ir. Andi', 'Jumat 13.00-15.00', '#8b5cf6'),
('IF-331', 'Sistem Operasi', 3, 3, 'Konsep dan implementasi sistem operasi', 'Pak Joko', 'Senin-Kamis 14.00-16.00', '#10b981'),
('IF-341', 'Pemrograman Mobil', 3, 6, 'Pengembangan aplikasi untuk mobil', 'Siti', 'Selasa-Rabu 16.00-18.00', '#f97316');

-- Sample assignments
INSERT INTO assignments (course_id, title, description, deadline, priority, status) VALUES
(1, 'Projek Akhir Web', 'Buat aplikasi CRUD dengan PHP', '2026-09-15 23:59:00', 'high', 'pending'),
(1, 'Tugas Mingguan 1', 'Setup environment development', '2026-09-08 23:59:00', 'medium', 'pending'),
(2, 'Desain ERD', 'Buat desain ERD untuk kasus pedagang', '2026-09-12 23:59:00', 'high', 'pending'),
(3, 'Laporan Latihan', 'Latihan soal jaringan Komputer', '2026-09-18 23:59:00', 'low', 'pending'),
(3, 'Tugas Rumah 3', 'Konfigurasi ruter dasar', '2026-09-20 23:59:00', 'medium', 'pending');

-- Sample materials
INSERT INTO materials (course_id, title, description, file_type, url_link, priority) VALUES
(1, 'Pendahuluan PHP', 'Pengenalan dasar PHP', 'pdf', '', 'low'),
(1, 'Video: Setup XAMPP', 'Cara install dan konfigurasi XAMPP', 'video', 'https://www.youtube.com/watch?v=XAMPP-setup', 'medium'),
(2, 'Bab 1: Normalisasi', 'Bab 1 dari buku Basis Data', 'pdf', '', 'low'),
(2, 'Video: Tabel Relasi', 'Cara merelasi tabel', 'video', 'https://www.youtube.com/watch?v=DB-relations', 'medium'),
(3, 'Video: Konfigurasi Router', 'Cara mengkonfigurasi router Cisco', 'video', 'https://www.youtube.com/watch?v=Router-config', 'high');

-- Sample notes
INSERT INTO notes (user_id, course_id, title, content, category) VALUES
(2, 1, 'Catatan Pertemuan 1', 'Pengenalan PHP dan environment setup', 'lecture'),
(2, 1, 'Catatan Pertemuan 2', 'Variabel dan tipe data', 'lecture'),
(3, 2, 'Catatan Database', 'Konsep Normalisasi 1NF, 2NF, 3NF', 'study');

-- Sample events
INSERT INTO events (title, description, start_time, end_time, type) VALUES
('Deadline: Projek Akhir Web', 'Membuat aplikasi CRUD dengan PHP', '2026-09-15 23:59:00', '2026-09-15 23:59:00', 'deadline'),
('Deadline: Desain ERD', 'Buat desain ERD untuk kasus pedagang', '2026-09-12 23:59:00', '2026-09-12 23:59:00', 'deadline'),
('Hari Ulang Tahun Kampus', 'Festival seni dan budaya', '2026-09-20 00:00:00', '2026-09-20 23:59:00', 'event');

-- Sample notifications
INSERT INTO notifications (user_id, title, message, type) VALUES
(1, 'Tugas Baru: Projek Akhir Web', 'Membuat aplikasi CRUD dengan PHP deadline 15 September 2026', 'task'),
(2, 'Tugas Telat: Tugas Mingguan 1', 'Tugas Mingguan 1 sudah lewat', 'task'),
(3, 'Materi Baru: Pendahuluan PHP', 'Material baru untuk Pembelajaran Web ditambahkan', 'material');
