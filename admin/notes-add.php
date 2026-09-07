<?php
/**
 * CampusHub - Admin: Notes Add
 * Menambah catatan mahasiswa baru
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_note'])) {
    $title = trim($_POST['note_title'] ?? '');
    $content = trim($_POST['note_content'] ?? '');
    $course_id = intval($_POST['course_id'] ?? 0);
    $category = $_POST['category'] ?? 'general';
    $is_favorite = isset($_POST['is_favorite']) ? 1 : 0;
    
    $errors = [];
    
    if (empty($title)) {
        $errors[] = 'Judul catatan wajib diisi';
    }
    if (empty($course_id) || $course_id <= 0) {
        $errors[] = 'Mata kuliah wajib dipilih';
    }
    
    if (empty($errors)) {
        try {
            global $pdo;
            $stmt = $pdo->prepare(
                'INSERT INTO notes (user_id, course_id, title, content, category, is_favorite) 
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            
            // User ID adalah admin yang login, kita paksa user_id = 1 (admin) atau bisa di-set berdasarkan session
            // Untuk admin panel, kita simpan dengan user_id tertentu atau kosongkan untuk general
            $admin_user = 1; // Admin user ID
            
            $result = $stmt->execute([
                $admin_user,
                $course_id,
                $title,
                $content,
                $category,
                $is_favorite
            ]);
            
            if ($result) {
                header('Location: /admin/notes.php');
                exit;
            }
        } catch (PDOException $e) {
            error_log('Add note error: ' . $e->getMessage());
            $errors[] = 'Gagal menyimpan catatan';
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
    <h1 class="text-3xl font-bold mb-4">Tambah Catatan Baru</h1>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error mb-4">
            <?php foreach ($errors as $error): ?><p><?= htmlspecialchars($error) ?></p><?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form action="" method="POST" class="max-w-2xl">
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-2">Judul Catatan</label>
                <input type="text" name="note_title" required
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
            <label class="block text-sm font-medium mb-2">Isi Catatan</label>
            <textarea name="note_content" rows="5" class="w-full px-4 py-3 rounded border"></textarea>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-2">Kategori</label>
                <select name="category" required class="w-full px-4 py-3 rounded border">
                    <option value="lecture">Mengajar</option>
                    <option value="study" selected>Belajar</option>
                    <option value="reference">Referensi</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Favorit</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="is_favorite" id="fav-yes" value="1" <?= isset($is_favorite) && $is_favorite ? 'checked' : '' ?>>
                    <label class="form-check-label" for="fav-yes">Ya</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="is_favorite" id="fav-no" value="0" <?= !isset($is_favorite) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="fav-no">Tidak</label>
                </div>
            </div>
        </div>
        <button type="submit" name="add_note"
                class="w-full btn btn-primary py-3 rounded font-medium mt-4">
            Simpan Catatan
        </button>
    </form>
</div>