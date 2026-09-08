<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$material_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$material = null;
$errors = [];

if ($material_id <= 0) {
    header('Location: /admin/materials.php');
    exit;
}

try {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM materials WHERE id = ?");
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_material'])) {
    $title = trim($_POST['material_title'] ?? '');
    $course_id = intval($_POST['course_id'] ?? 0);
    $file_url = trim($_POST['file_url'] ?? '');
    $material_type = $_POST['material_type'] ?? 'pdf';
    $priority = $_POST['priority'] ?? 'medium';
    if (empty($title)) {
        $errors[] = 'Judul wajib diisi';
    }
    if (empty($errors)) {
        try {
            global $pdo;
            $stmt = $pdo->prepare("UPDATE materials SET course_id = ?, title = ?, file_url = ?, material_type = ?, priority = ? WHERE id = ?");
            $stmt->execute([$course_id, $title, $file_url, $material_type, $priority, $material_id]);
            header('Location: /admin/materials.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Gagal memperbarui materi';
        }
    }
}

$courses = [];
try {
    global $pdo;
    $cstmt = $pdo->prepare('SELECT id, name FROM courses ORDER BY name');
    $cstmt->execute();
    $courses = $cstmt->fetchAll();
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Materi - CampusHub</title>
    <link rel="stylesheet" href="/assets/css/global.css">
</head>
<body data-theme="light">
<div class="padding-x4">
    <h1 class="text-3xl font-bold mb-4">Edit Materi</h1>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error mb-4">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if ($material): ?>
    <form action="/admin/materials-edit-process.php?id=<?= $material['id'] ?>" method="POST" class="max-w-2xl">
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-2">Judul</label>
                <input type="text" name="material_title" value="<?= htmlspecialchars($material['title']) ?>" class="w-full px-4 py-3 rounded border">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Mata Kuliah</label>
                <select name="course_id" class="w-full px-4 py-3 rounded border">
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $material['course_id'] == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Deskripsi</label>
            <textarea name="material_description" rows="3" class="w-full px-4 py-3 rounded border"><?= htmlspecialchars($material['description'] ?? '') ?></textarea>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-2">Tipe</label>
                <select name="material_type" class="w-full px-4 py-3 rounded border">
                    <option value="pdf" <?= $material['material_type'] == 'pdf' ? 'selected' : '' ?>>PDF</option>
                    <option value="video" <?= $material['material_type'] == 'video' ? 'selected' : '' ?>>Video</option>
                    <option value="document" <?= $material['material_type'] == 'document' ? 'selected' : '' ?>>Document</option>
                    <option value="link" <?= $material['material_type'] == 'link' ? 'selected' : '' ?>>Link</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Prioritas</label>
                <select name="priority" class="w-full px-4 py-3 rounded border">
                    <option value="low" <?= $material['priority'] == 'low' ? 'selected' : '' ?>>Low</option>
                    <option value="medium" <?= $material['priority'] == 'medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="high" <?= $material['priority'] == 'high' ? 'selected' : '' ?>>High</option>
                </select>
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">URL File</label>
            <input type="url" name="file_url" value="<?= htmlspecialchars($material['file_url'] ?? '') ?>" class="w-full px-4 py-3 rounded border">
        </div>
        <div class="text-end">
            <button type="submit" name="update_material" class="btn btn-primary px-6">Simpan Perubahan</button>
            <a href="/admin/materials.php" class="btn btn-outline px-6 ml-4">Batal</a>
        </div>
    </form>
    <?php endif; ?>
</div>
</body>
</html>
