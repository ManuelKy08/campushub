<?php
/**
 * CampusHub - Admin: Materials Add
 * Menambah materi belajar baru
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_material'])) {
    $title = trim($_POST['material_title'] ?? '');
    $description = trim($_POST['material_description'] ?? '');
    $course_id = intval($_POST['course_id'] ?? 0);
    $file_type = $_POST['file_type'] ?? 'pdf';
    
    $errors = [];
    
    if (empty($title)) {
        $errors[] = 'Judul materi wajib diisi';
    }
    if (empty($course_id) || $course_id <= 0) {
        $errors[] = 'Mata kuliah wajib dipilih';
    }
    
    $file_path = null;
    $file_name = null;
    
    // Proses upload file
    if (!isset($_FILES['material_file']) || $_FILES['material_file']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File wajib diupload';
    } else {
        $file = $_FILES['material_file'];
        
        $max_file_size = 50 * 1024 * 1024;
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 
                          'video/mp4', 'video/quicktime', 'application/msword',
                          'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        
        if ($file['size'] > $max_file_size) {
            $errors[] = 'Ukuran file maksimal 50MB';
        }
        
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($file['tmp_name']);
        
        if (!in_array($mime_type, $allowed_types, true)) {
            $errors[] = 'Tipe file tidak didukung';
        }
        
        if (empty($errors)) {
            $upload_dir = '/home/rrsec/Downloads/pentesting/opencode/campushub/assets/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $safe_name = generate_safe_filename($file['name']);
            $target_path = $upload_dir . $safe_name;
            
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                $file_path = $safe_name;
                $file_name = $file['name'];
            } else {
                $errors[] = 'Gagal mengupload file';
            }
        }
    }
    
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
                header('Location: /admin/materials.php');
                exit;
            }
        } catch (PDOException $e) {
            error_log('Add material error: ' . $e->getMessage());
            $errors[] = 'Gagal menyimpan materi';
        }
    }
    
    if (!empty($errors)) {
        echo '<div class="alert alert-error mb-4">
                <i class="bx bx-error-circle me-2"></i>';
        foreach ($errors as $error) {
            echo '<p>' . htmlspecialchars($error) . '</p>';
        }
        echo '</div>';
    }
}
?>

<div class="padding-x4">
    <h1 class="text-3xl font-bold mb-4">Tambah Materi Baru</h1>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error mb-4">
            <?php foreach ($errors as $error): ?><p><?= htmlspecialchars($error) ?></p><?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form action="" method="POST" class="max-w-2xl">
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-2">Judul Materi</label>
                <input type="text" name="material_title" required
                       class="w-full px-4 py-3 rounded border">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Mata Kuliah</label>
                <select name="course_id" required class="w-full px-4 py-3 rounded border">
                    <option value="">-- Pilih Mata Kuliah --</option>
                    <?php try {
                        $cstmt = $pdo->prepare('SELECT id, name FROM courses ORDER BY name');
                        $cstmt->execute();
                        $courses = $cstmt->fetchAll();
                        foreach ($courses as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    } catch (Exception $e) {} ?>
                </select>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">Deskripsi</label>
            <textarea name="material_description" rows="3" class="w-full px-4 py-3 rounded border"></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium mb-2">Tipe File</label>
            <select name="file_type" required class="w-full px-4 py-3 rounded border">
                <option value="pdf">PDF</option>
                <option value="video">Video</option>
                <option value="document">Dokumen</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">File Materi</label>
            <input type="file" name="material_file" required
                   class="w-full px-4 py-3 rounded border">
            <small class="text-muted">Tipe yang diizinkan: PDF, JPEG, PNG, DOC, DOCX, MP4</small>
        </div>
        <button type="submit" name="add_material"
                class="w-full btn btn-primary py-3 rounded font-medium mt-4">
            Simpan Materi
        </button>
    </form>
</div>