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
    $count_stmt = $pdo->prepare("SELECT COUNT(*) as total FROM assignments $where");
    $count_stmt->execute($params);
    $total = $count_stmt->fetch()['total'];
    $total_pages = ceil($total / 10);
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $offset = ($page - 1) * 10;
    $stmt = $pdo->prepare("SELECT a.*, c.name as course_name FROM assignments a LEFT JOIN courses c ON a.course_id = c.id $where ORDER BY a.deadline ASC LIMIT ? OFFSET ?");
    $stmt->execute(array_merge($params, [10, $offset]));
    $assignments = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Admin assignments error: ' . $e->getMessage());
    $assignments = [];
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
    <title>Kelola Tugas - CampusHub</title>
    <link rel="stylesheet" href="/assets/css/global.css">
</head>
<body data-theme="light">
<div class="padding-x4">
    <h1 class="text-3xl font-bold mb-4">Kelola Tugas</h1>
    <a href="/admin/assignments-add.php" class="btn btn-primary mb-4">Tambah Tugas</a>
    <form action="/admin/assignments.php" method="GET" class="mb-4">
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
                <input type="text" name="search" class="w-full px-4 py-3 rounded border" placeholder="Cari judul tugas..." value="<?= htmlspecialchars($search) ?>">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>
    <div class="card">
        <div class="card-body px-6 py-4">
            <?php if (!empty($assignments)): ?>
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="text-left">Judul</th>
                            <th class="text-left">Mata Kuliah</th>
                            <th class="text-center">Deadline</th>
                            <th class="text-left">Prioritas</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assignments as $a): ?>
                        <tr>
                            <td class="font-medium"><a href="/assignments-detail.php?id=<?= $a['id'] ?>"><?= htmlspecialchars($a['title']) ?></a></td>
                            <td class="text-muted small"><?= htmlspecialchars($a['course_name'] ?? '') ?></td>
                            <td class="text-center small"><?= date('d M Y H:i', strtotime($a['deadline'])) ?></td>
                            <td><span class="badge badge-<?= $a['priority'] ?> small"><?= ucfirst($a['priority']) ?></span></td>
                            <td><span class="badge badge-<?= $a['status'] === 'completed' ? 'success' : 'warning' ?>"><?= ucfirst($a['status']) ?></span></td>
                            <td class="text-end">
                                <a href="/admin/assignments-edit.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-outline">Edit</a>
                                <form method="POST" style="display:inline" onsubmit="return confirm('Yakin menghapus?');">
                                    <button type="submit" name="delete_assignment" value="<?= $a['id'] ?>" class="btn btn-sm btn-error">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="text-center py-8 text-muted"><p>Belum ada tugas terdaftar</p></div>
            <?php endif; ?>
        </div>
    </div>
    <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-6">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="/admin/assignments.php?page=<?= $page - 1 ?>" aria-label="Previous">&laquo;</a>
                </li>
                <?php for ($p = max(1, $page - 2); $p <= min($total_pages, $page + 2); $p++): ?>
                    <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                        <a class="page-link" href="/admin/assignments.php?page=<?= $p ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="/admin/assignments.php?page=<?= $page + 1 ?>" aria-label="Next">&raquo;</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>
</body>
</html>
