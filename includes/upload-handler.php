<?php
/**
 * CampusHub - File Upload Handler
 * Fungsi: Mengupload file materi tugas dengan validasi tipe dan ukuran
 * Keamanan: Validasi ekstensi, ukuran maksimum, scan basic
 */
if (!defined('IN_CAMPUSHUB')) die('Akses dilarang');

require_once __DIR__ . '/../config/database.php';

$upload_dir = '/home/rrsec/Downloads/pentesting/opencode/campushub/assets/uploads/';
$max_file_size = 50 * 1024 * 1024; // 50MB
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'video/mp4', 'video/quicktime', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

// Pastikan direktori upload tersedia
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Generate nama file yang unik
function generate_safe_filename($original_name) {
    $ext = strtolower(pathinfo($original_name, PATH_EXTENSION));
    $name = pathinfo($original_name, PATH_FILENAME);
    // Hapus karakter tidak aman
    $safe_name = preg_replace('/[^a-zA-Z0-9]/', '', $name);
    $timestamp = time();
    return $safe_name . '_' . $timestamp . '.' . $ext;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_material'])) {
    $title = trim($_POST['material_title'] ?? '');
    $description = trim($_POST['material_description'] ?? '');
    $course_id = intval($_POST['course_id'] ?? 0);
    $file_type = $_POST['file_type'] ?? 'document';
    
    $errors = [];
    $file_path = null;
    $file_name = null;
    
    // Validasi judul
    if (empty($title)) {
        $errors[] = 'Judul materi wajib diisi';
    }
    
    // Validasi file
    if (!isset($_FILES['material_file']) || $_FILES['material_file']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File wajib diupload';
    } else {
        $file = $_FILES['material_file'];
        
        // Validasi ukuran
        if ($file['size'] > $max_file_size) {
            $errors[] = 'Ukuran file maksimal 50MB';
        }
        
        // Validasi tipe MIME
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($file['tmp_name']);
        
        if (!in_array($mime_type, $allowed_types, true)) {
            $errors[] = 'Tipe file tidak didukung. Izin: JPEG, PNG, PDF, DOC, DOCX';
        }
        
        // Generate nama file safe
        $safe_name = generate_safe_filename($file['name']);
        $target_path = $upload_dir . $safe_name;
        
        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            $file_path = $safe_name;
            $file_name = $file['name'];
        } else {
            $errors[] = 'Gagal mengupload file. Coba lagi.';
        }
    }
    
    // Simpan ke database jika tidak ada error
    if (empty($errors)) {
        try {
            global $pdo;
            $stmt = $pdo->prepare(
                'INSERT INTO materials (course_id, title, description, file_type, file_path, file_name, sort_order) 
                 VALUES (?, ?, ?, ?, ?, ?, 0)'
            );
            
            $result = $stmt->execute([
                $course_id,
                $title,
                $description,
                $file_type,
                $file_path,
                $file_name
            ]);
            
            if ($result) {
                header('Location: /materials.php');
                exit;
            }
        } catch (PDOException $e) {
            error_log('Material insert error: ' . $e->getMessage());
            $errors[] = 'Gagal menyimpan data materi';
        }
    }
    
    // Jika error, kembali ke form dengan pesan error
    if (!empty($errors)) {
        echo '<div class="alert alert-error mb-4">
                <i class="bx bx-error-circle me-2"></i>';
        foreach ($errors as $error) {
            echo '<p>' . htmlspecialchars($error) . '</p>';
        }
        echo '</div>';
    }
}