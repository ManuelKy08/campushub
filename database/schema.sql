-- Database: campushub
-- DROP DATABASE IF EXISTS campushub;
CREATE DATABASE IF NOT EXISTS campushub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE campushub;

-- ------------------------------------------------------------
-- Tabel: users
-- ------------------------------------------------------------
CREATE TABLE users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    nim VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('student', 'admin') DEFAULT 'student',
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

-- ------------------------------------------------------------
-- Tabel: courses (mata kuliah)
-- ------------------------------------------------------------
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

-- ------------------------------------------------------------
-- Tabel: assignments (tugas)
-- ------------------------------------------------------------
CREATE TABLE assignments (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    course_id INT UNSIGNED NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NULL,
    deadline DATETIME NOT NULL,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('pending', 'in_progress', 'completed', 'late') DEFAULT 'pending',
    submitted_by INT UNSIGNED NULL,
    file_path VARCHAR(255) NULL,
    file_name VARCHAR(255) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE CASCADE,
    INDEX idx_course_id (course_id),
    INDEX idx_status (status),
    INDEX idx_deadline (deadline)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: materials (materi belajar)
-- ------------------------------------------------------------
CREATE TABLE materials (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    course_id INT UNSIGNED NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NULL,
    file_path VARCHAR(255) NULL,
    file_type ENUM('pdf', 'video', 'link', 'document') DEFAULT 'pdf',
    url_link TEXT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE CASCADE,
    INDEX idx_course_id (course_id),
    INDEX idx_file_type (file_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: notes (catatan mahasiswa)
-- ------------------------------------------------------------
CREATE TABLE notes (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    course_id INT UNSIGNED NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NULL,
    category VARCHAR(50) DEFAULT 'general',
    is_favorite TINYINT(1) DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_course_id (course_id),
    INDEX idx_favorite (is_favorite)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: events (kalender akademik)
-- ------------------------------------------------------------
CREATE TABLE events (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NULL,
    start_time DATETIME NOT NULL,
    end_time DATETIME NULL,
    type ENUM('deadline', 'lecture', 'event', 'milestone') DEFAULT 'deadline',
    course_id INT UNSIGNED NULL,
    created_by INT UNSIGNED NULL,
    is_all_day TINYINT(1) DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses (id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL,
    INDEX idx_start_time (start_time),
    INDEX idx_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: notifications (notifikasi)
-- ------------------------------------------------------------
CREATE TABLE notifications (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NULL,
    type ENUM('task', 'deadline', 'material', 'system') DEFAULT 'task',
    is_read TINYINT(1) DEFAULT 0,
    related_id INT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Tabel: password_resets (reset password)
-- ------------------------------------------------------------
CREATE TABLE password_resets (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    token VARCHAR(100) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- INSERT DATA AWAL (Sample Data)
-- ------------------------------------------------------------
--
-- Insert admin user
--
INSERT INTO users (full_name, nim, email, password_hash, role, program_study, semester, is_active)
VALUES (
    'Admin Sistem',
    '0000001',
    'admin@campushub.id',
    -- Password: admin123 (hash bcrypt)
    '$2y$12$PYTB0WawXgYLDBuaFgUaxu1AGKBPbljeViOiex5mKgnrdt51BEc4y',
    'admin',
    'Teknologi Informasi',
    1,
    1
);

-- Insert sample mahasiswa
INSERT INTO users (full_name, nim, email, password_hash, role, program_study, semester, is_active) VALUES
('Budi Santoso', '210101', 'budi@university.id', '$2y$12$PYTB0WawXgYLDBuaFgUaxu1AGKBPbljeViOiex5mKgnrdt51BEc4y', 'student', 'Teknologi Informasi', 3, 1),
('Aisha Roisha', '210102', 'aisha@university.id', '$2y$12$PYTB0WawXgYLDBuaFgUaxu1AGKBPbljeViOiex5mKgnrdt51BEc4y', 'student', 'Desain Komunikasi Visual', 2, 1),
('Putra Amin', '210103', 'putra@university.id', '$2y$12$PYTB0WawXgYLDBuaFgUaxu1AGKBPbljeViOiex5mKgnrdt51BEc4y', 'student', 'Sistem Informasi', 4, 1),

-- Insert sample mata kuliah
INSERT INTO courses (code, name, credits, semester, description, professor, schedule, color_code) VALUES
('IF-231', 'Pemrograman Web', 3, 2, 'Pembuatan aplikasi web modern', 'Dr. Sarah', 'Senin-Rabu 08.00-10.00', '#3b82f6'),
('IF-241', 'Basis Data', 3, 3, 'Desain dan implementasi basis data', 'Dr. Budi', 'Selasa-Kamis 10.00-12.00', '#ef4444'),
('IF-321', 'Jaringan Komputer', 3, 5, 'Protokol dan arsitekom jaringan', 'Ir. Andi', 'Jumat 13.00-15.00', '#8b5cf6'),
('IF-331', 'Sistem Operasi', 3, 3, 'Konsep dan implementasi sistem operasi', 'Pak Joko', 'Senin-Kamis 14.00-16.00', '#10b981'),
('IF-341', 'Pemrograman Mobil', 3, 6, 'Pengembangan aplikasi untuk mobil', 'Siti', 'Selasa-Rabu 16.00-18.00', '#f97316'),

-- Insert sample tugas
INSERT INTO assignments (course_id, title, description, deadline, priority, status) VALUES
(1, 'Projek Akhir Web', 'Buat aplikasi CRUD dengan PHP', '2026-09-15 23:59:00', 'high', 'pending'),
(1, 'Tugas Mingguan 1', 'Setup environment development', '2026-09-08 23:59:00', 'medium', 'pending'),
(2, 'Desain ERD', 'Buat desain ERD untuk kasus pedagang', '2026-09-12 23:59:00', 'high', 'pending'),
(3, 'Laporan Latihan', 'Latihan soal jaringan Komputer', '2026-09-18 23:59:00', 'low', 'pending'),
(3, 'Tugas Rumah 3', 'Konfigurasi ruter dasar', '2026-09-20 23:59:00', 'medium', 'pending'),

-- Insert sample materi
INSERT INTO materials (course_id, title, description, file_type, url_link) VALUES
(1, 'Pendahuluan PHP', 'Pengenalan dasar PHP', 'pdf', NULL),
(1, 'Video: Setup XAMPP', 'Cara install dan konfigurasi XAMPP', 'video', 'https://www.youtube.com/watch?v=XAMPP-setup'),
(2, 'Bab 1: Normalisasi', 'Bab 1 dari buku Basis Data', 'pdf', NULL),
(2, 'Video: Tabel Relasi', 'Cara merelasi tabel', 'video', 'https://www.youtube.com/watch?v=DB-relations'),
(3, 'Video: Konfigurasi Router', 'Cara mengkonfigurasi router Cisco', 'video', 'https://www.youtube.com/watch?v=Router-config'),

-- Insert sample notes
INSERT INTO notes (user_id, course_id, title, content, category) VALUES
(2, 1, 'Catatan Pertemuan 1', 'Pengenalan PHP dan environment setup', 'lecture'),
(2, 1, 'Catatan Pertemuan 2', 'Variabel dan tipe data', 'lecture'),
(3, 2, 'Catahan Database', 'Konsep Normalisasi 1NF, 2NF, 3NF', 'study'),

-- Insert sample events
INSERT INTO events (title, description, start_time, end_time, type) VALUES
('Deadline: Projek Akhir Web', 'Membuat aplikasi CRUD dengan PHP', '2026-09-15 23:59:00', '2026-09-15 23:59:00', 'deadline'),
('Deadline: Desain ERD', 'Buat desain ERD untuk kasus pedagang', '2026-09-12 23:59:00', '2026-09-12 23:59:00', 'deadline'),
('Hari Ulang Tahun Kampus', 'Festival seni dan budaya', '2026-09-20 00:00:00', '2026-09-20 23:59:00', 'event'),

-- Insert sample notifications
(1, 'Tugas Baru: Projek Akhir Web', 'Membuat aplikasi CRUD dengan PHP deadline 15 September 2026', 'task'),
(2, 'Tugas Telat: Tugas Mingguan 1', 'Tugas Mingguan 1 sudah lewat', 'task'),
(3, 'Materi Baru: Pendahuluan PHP', 'Material baru untuk Pembelajaran Web ditambahkan', 'material');

-- ------------------------------------------------------------
-- Indexes tambahan
-- ---------------------------------------------------------ALTER TABLE users ADD FULLTEXT INDEX fulltext_name (full_name);
-- ALTER TABLE courses ADD FULLTEXT INDEX fulltext_name (name);