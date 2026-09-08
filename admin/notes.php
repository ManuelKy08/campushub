<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$course_filter = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

$where = '';
$params = [];

if ($course_filter) {
    $where .= ' AND course_id = ?';
    $params[] = $course_filter;
}
if ($search) {
    $where .= ($where ? ' AND ' : ' WHERE ') . ' title LIKE ?';
    $params[] = '%' . $search . '%';
}

try {
    global $pdo;
    $count_stmt = $pdo->prepare("SELECT COUNT(*) as total FROM notes $where");
    $count_stmt->execute($params);
    $total = $count_stmt->fetch()['total'];
    $total_pages = ceil($total / 10);
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $offset = ($page - 1) * 10;
    $stmt = $pdo->prepare("SELECT n.*, c.name as course_name FROM notes n LEFT JOIN courses c ON n.course_id = c.id $where ORDER BY n.priority ASC, n.created_at DESC LIMIT ? OFFSET ?");
    $stmt->execute(array_merge($params, [10, $offset]));
    $notes = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Admin notes error: ' . $e->getMessage());
    $notes = [];
    $total_pages = 1;
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
    <title>Kelola Catatan - CampusHub</title>
    <link rel="stylesheet" href="/assets/css/global.css">
</head>
<body data-theme="light">
<div class="padding-x4">
    <h1 class="text-3xl font-bold mb-4">Kelola Catatan</h1>
    <a href="/admin/notes-add.php" class="btn btn-primary mb-4">Tambah Catatan</a>
    <form action="/admin/notes.php" method="GET" class="mb-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <select name="course_id" class="w-full px-4 py-3 rounded border">
                    <option value="">Semua Mata Kuliah</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $course_filter == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <input type="text" name="search" class="w-full px-4 py-3 rounded border" placeholder="Cari judul catatan..." value="<?= htmlspecialchars($search) ?>">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th class="text-left">Judul</th>
                <th class="text-left">Mata Kuliah</th>
                <th class="text-center">Prioritas</th>
                <th class="text-end">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($notes as $n): ?>
            <tr>
                <td class="font-medium"><?= htmlspecialchars($n['title']) ?></td>
                <td class="text-muted small"><?= htmlspecialchars($n['course_name'] ?? '') ?></td>
                <td class="text-center"><span class="badge badge-<?= $n['priority'] ?> small"><?= ucfirst($n['priority']) ?></span></td>
                <td class="text-end">
                    <a href="/admin/notes-edit.php?id=<?= $n['id'] ?>" class="btn btn-sm btn-outline">Edit</a>
                    <form method="POST" style="display:inline" onsubmit="return confirm('Yakin menghapus?');">
                        <button type="submit" name="delete_note" value="<?= $n['id'] ?>" class="btn btn-sm btn-error">Hapus</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-6">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="/admin/notes.php?page=<?= $page - 1 ?>" aria-label="Previous">&laquo;</a>
                </li>
                <?php for ($p = max(1, $page - 2); $p <= min($total_pages, $page + 2); $p++): ?>
                    <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                        <a class="page-link" href="/admin/notes.php?page=<?= $p ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="/admin/notes.php?page=<?= $page + 1 ?>" aria-label="Next">&raquo;</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>
</body>
</html>
