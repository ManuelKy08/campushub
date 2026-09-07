<?php
/**
 * CampusHub - Admin: Courses
 * Mengelola data mata kuliah (CRUD)
 * Fitur: Tambah, edit, hapus mata kuliah
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course'])) {
    $code = trim($_POST['code'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $credits = intval($_POST['credits'] ?? '3');
    $semester = intval($_POST['semester'] ?? '1');
    $description = trim($_POST['description'] ?? '');
    $professor = trim($_POST['professor'] ?? '');
    $schedule = trim($_POST['schedule'] ?? '');
    $room = trim($_POST['room'] ?? '');
    $color_code = $_POST['color_code'] ?? '#3b82f6';
    
    $errors = [];
    
    if (empty($code)) {
        $errors[] = 'Kode mata kuliah wajib diisi';
    }
    if (empty($name)) {
        $errors[] = 'Nama mata kuliah wajib diisi';
    }
    
    if (empty($errors)) {
        try {
            global $pdo;
            $stmt = $pdo->prepare(
                'INSERT INTO courses (code, name, credits, semester, description, professor, schedule, room, color_code) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $result = $stmt->execute([$code, $name, $credits, $semester, $description, $professor, $schedule, $room, $color_code]);
            
            if ($result) {
                header('Location: /admin/courses.php');
                exit;
            }
        } catch (PDOException $e) {
            error_log('Add course error: ' . $e->getMessage());
            $errors[] = 'Gagal menambah mata kuliah';
        }
    }
}
?>

<div class="padding-x4">
    <h1 class="text-3xl font-bold mb-4">Tambah Mata Kuliah</h1>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error mb-4">
            <?php foreach ($errors as $error): ?><p><?= htmlspecialchars($error) ?></p><?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form action="" method="POST" class="max-w-lg">
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-2">Kode</label>
                <input type="text" name="code" required
                       class="w-full px-4 py-3 rounded border">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">SKS</label>
                <input type="number" name="credits" min="1" value="3" required
                       class="w-full px-4 py-3 rounded border">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-2">Nama</label>
                <input type="text" name="name" required
                       class="w-full px-4 py-3 rounded border">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Semester</label>
                <select name="semester" required class="w-full px-4 py-3 rounded border">
                    <?php for ($i = 1; $i <= 8; $i++): ?>
                        <option value="<?= $i ?>">Semester <?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Deskripsi</label>
            <textarea name="description" rows="3" class="w-full px-4 py-3 rounded border"></textarea>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-2">Dosen</label>
                <input type="text" name="professor"
                       class="w-full px-4 py-3 rounded border">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Ruang</label>
                <input type="text" name="room"
                       class="w-full px-4 py-3 rounded border">
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Kode Warna</label>
            <input type="color" name="color_code" value="#3b82f6" class="w-20 h-6 rounded">
        </div>
        <button type="submit" name="add_course"
                class="w-full btn btn-primary py-3 rounded font-medium mt-4">
            Simpan Mata Kuliah
        </button>
    </form>
</div>