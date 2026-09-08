<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$errors = [];
$courses = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_note'])) {
    $title = trim($_POST['note_title'] ?? '');
    $description = trim($_POST['note_description'] ?? '');
    $course_id = intval($_POST['course_id'] ?? 0);
    $priority = $_POST['priority'] ?? 'medium';
    
    if (empty($title)) {
        $errors[] = 'Judul catatan wajib diisi';
    }
    if (empty($course_id) || $course_id <= 0) {
        $errors[] = 'Mata kuliah wajib dipilih';
    }
    
    $priority_options = ['low', 'medium', 'high'];
    if (!in_array($priority, $priority_options, true)) {
        $errors[] = 'Prioritas tidak valid';
    }
    
    if (empty($errors)) {
        try {
            global $pdo;
            $stmt = $pdo->prepare("INSERT INTO notes (course_id, title, description, priority) VALUES (?, ?, ?, ?)");
            $stmt->execute([$course_id, $title, $description, $priority]);
            header('Location: /admin/notes.php');
            exit;
        } catch (PDOException $e) {
            error_log('Add note error: ' . $e->getMessage());
            $errors[] = 'Gagal menambah catatan';
        }
    }
}

try {
    global $pdo;
    $cstmt = $pdo->prepare('SELECT id, name FROM courses ORDER BY name');
    $cstmt->execute();
    $courses = $cstmt->fetchAll();
} catch (Exception $e) {
    $courses = [];
}
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Catatan - CampusHub</title>
    <link rel="stylesheet" href="/assets/css/global.css">
</head>
<body data-theme="light">
<div class="padding-x4">
    <h1 class="text-3xl font-bold mb-4">Tambah Catatan Baru</h1>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error mb-4">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form action="" method="POST" class="max-w-2xl">
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-2">Judul Catatan</label>
                <input type="text" name="note_title" required class="w-full px-4 py-3 rounded border">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Mata Kuliah</label>
                <select name="course_id" required class="w-full px-4 py-3 rounded border">
                    <option value="">-- Pilih Mata Kuliah --</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Deskripsi</label>
            <textarea name="note_description" rows="3" class="w-full px-4 py-3 rounded border"></textarea>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Prioritas</label>
            <select name="priority" required class="w-full px-4 py-3 rounded border">
                <option value="low">Low</option>
                <option value="medium" selected>Medium</option>
                <option value="high">High</option>
            </select>
        </div>
        <button type="submit" name="add_note" class="w-full btn btn-primary py-3 rounded font-medium">Simpan Catatan</button>
    </form>
</div>
</body>
</html>
