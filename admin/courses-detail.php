<?php
/**
 * CampusHub - Admin: Courses Detail
 * Menampilkan detail mata kuliah dan opsi edit
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

require_role('admin');

$course_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($course_id <= 0) {
    header('Location: /admin/courses.php');
    exit;
}

try {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM courses WHERE id = ?');
    $stmt->execute([$course_id]);
    $course = $stmt->fetch();
    
    if (!$course) {
        header('Location: /admin/courses.php');
        exit;
    }
} catch (PDOException $e) {
    error_log('Course detail error: ' . $e->getMessage());
    $course = null;
}
?>

<div class="padding-x4">
    <!-- Header -->
    <header class="mb-6">
        <div class="d-flex justify-between align-items-center">
            <h1 class="text-3xl font-bold">Detail Mata Kuliah</h1>
        </div>
    </header>
    
    <!-- Course Details -->
    <div class="card">
        <div class="card-body px-6 py-4">
            <form action="/admin/courses-edit.php?id=<?= $course['id'] ?>" method="POST" class="space-y-4">
                <input type="hidden" name="original_code" value="<?= htmlspecialchars($course['code']) ?>">
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Kode</label>
                        <input type="text" name="code" value="<?= htmlspecialchars($course['code']) ?>"
                               class="w-full px-4 py-3 rounded border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">SKS</label>
                        <input type="number" name="credits" value="<?= $course['credits'] ?>" min="1"
                               class="w-full px-4 py-3 rounded border">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Nama</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($course['name']) ?>"
                               class="w-full px-4 py-3 rounded border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Semester</label>
                        <select name="semester" class="w-full px-4 py-3 rounded border">
                            <?php for ($i = 1; $i <= 8; $i++): ?>
                                <option value="<?= $i ?>" <?= ($i == $course['semester']) ? 'selected' : '' ?>>
                                    Semester <?= $i ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Deskripsi</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-3 rounded border"><?= htmlspecialchars($course['description'] ?? '') ?></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Dosen</label>
                        <input type="text" name="professor" value="<?= htmlspecialchars($course['professor'] ?? '') ?>"
                               class="w-full px-4 py-3 rounded border">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Ruang</label>
                        <input type="text" name="room" value="<?= htmlspecialchars($course['room'] ?? '') ?>"
                               class="w-full px-4 py-3 rounded border">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Kode Warna</label>
                    <input type="color" name="color_code" value="<?= htmlspecialchars($course['color_code'] ?? '#3b82f6') ?>"
                           class="w-20 h-6 rounded">
                </div>
                <div class="text-end">
                    <button type="submit" name="update_course"
                            class="w-auto btn btn-primary px-6">Simpan Perubahan</button>
                    <a href="/admin/courses.php" class="w-auto btn btn-outline px-6 ml-4">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/foot-starter.php'; ?>