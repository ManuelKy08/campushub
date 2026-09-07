<?php
/**
 * CampusHub - Admin: Materials Edit
 * Mengedit materi yang sudah ada
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$material_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($material_id <= 0) {
    header('Location: /admin/materials.php');
    exit;
}

try {
    global $pdo;
    $stmt = $pdo->prepare("SELECT m.*, c.name as course_name FROM materials m LEFT JOIN courses c ON m.course_id = c.id WHERE m.id = ?");
    $stmt->execute([$material_id]);
    $material = $stmt->fetch();
    
    if (!$material) {
        header('Location: /admin/materials.php');
        exit;
    }
} catch (PDOException $e) {
    error_log('Material edit load error: ' . $e->getMessage());
    $material = null;
}
?>

<div class="padding-x4">
    <h1 class="text-3xl font-bold mb-4">Edit Materi</h1>
    
    <?php if (!$material): ?>
        <div class="alert alert-error">Materi tidak ditemukan</div>
    <?php else: ?>
        <form action="/admin/materials-edit-process.php?id=<?= $material['id'] ?>" method="POST" enctype="multipart/form-data" class="max-w-2xl">
            <input type="hidden" name="original_file_path" value="<?= htmlspecialchars($material['file_path'] ?? '') ?>">
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Judul</label>
                    <input type="text" name="material_title" value="<?= htmlspecialchars($material['title']) ?>"
                           class="w-full px-4 py-3 rounded border">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Mata Kuliah</label>
                    <select name="course_id" class="w-full px-4 py-3 rounded border">
                        <option value="<?= $material['course_id'] ?>"><?= htmlspecialchars($material['course_name'] ?? '') ?></option>
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
                <textarea name="material_description" rows="3" class="w-full px-4 py-3 rounded border"><?= htmlspecialchars($material['description'] ?? '') ?></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Tipe File</label>
                <select name="file_type" class="w-full px-4 py-3 rounded border">
                    <option value="pdf" <?= ($material['file_type'] ?? 'pdf') == 'pdf' ? 'selected' : '' ?>>PDF</option>
                    <option value="video" <?= ($material['file_type'] ?? 'pdf') == 'video' ? 'selected' : '' ?>>Video</option>
                    <option value="document" <?= ($material['file_type'] ?? 'pdf') == 'document' ? 'selected' : '' ?>>Dokumen</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">File Materi Lama</label>
                <?php if ($material['file_path']): ?>
                    <div class="mb-2">
                        <a href="/download.php?file=<?= $material['file_path'] ?>" target="_blank"
                           class="text-primary small">
                            <?= htmlspecialchars($material['file_name'] ?? 'File') ?>
                        </a>
                    </div>
                <?php else: ?>
                    <span class="text-muted small">Tidak ada file lama</span>
                <?php endif; ?>
                <input type="file" name="material_file" class="w-full px-4 py-3 rounded border mt-2">
                <small class="text-muted">Kosongkan jika tidak ingin mengganti file</small>
            </div>
            <div class="text-end">
                <button type="submit" name="update_material"
                        class="w-auto btn btn-primary px-6">Simpan Perubahan</button>
                <a href="/admin/materials.php" class="w-auto btn btn-outline px-6 ml-4">Batal</a>
            </div>
        </form>
    <?php endif; ?>
</div>