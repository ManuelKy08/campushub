<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
$params = [];

if ($search) {
    $where .= ($where ? ' AND ' : ' WHERE ') . ' name LIKE ?';
    $params[] = '%' . $search . '%';
}

try {
    global $pdo;
    $count_stmt = $pdo->prepare("SELECT COUNT(*) as total FROM courses $where");
    $count_stmt->execute($params);
    $total = $count_stmt->fetch()['total'];
    $total_pages = ceil($total / 10);
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $offset = ($page - 1) * 10;
    $stmt = $pdo->prepare("SELECT * FROM courses $where ORDER BY name ASC LIMIT ? OFFSET ?");
    $stmt->execute(array_merge($params, [10, $offset]));
    $courses = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Admin courses error: ' . $e->getMessage());
    $courses = [];
    $total_pages = 1;
}
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Mata Kuliah - CampusHub</title>
    <link rel="stylesheet" href="/assets/css/global.css">
</head>
<body data-theme="light">
<div class="padding-x4">
    <h1 class="text-3xl font-bold mb-4">Kelola Mata Kuliah</h1>
    <a href="/admin/courses-add.php" class="btn btn-primary mb-4">Tambah Mata Kuliah</a>
    <form action="/admin/courses.php" method="GET" class="mb-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <input type="text" name="search" class="w-full px-4 py-3 rounded border" placeholder="Cari nama mata kuliah..." value="<?= htmlspecialchars($search) ?>">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th class="text-left">Nama</th>
                <th class="text-left">Kode</th>
                <th class="text-center">SKS</th>
                <th class="text-end">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courses as $c): ?>
            <tr>
                <td class="font-medium"><?= htmlspecialchars($c['name']) ?></td>
                <td><?= htmlspecialchars($c['code'] ?? '') ?></td>
                <td class="text-center"><?= htmlspecialchars($c['sks'] ?? '') ?></td>
                <td class="text-end">
                    <a href="/admin/courses-detail.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline">Detail</a>
                    <a href="/admin/courses-edit.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-outline">Edit</a>
                    <form method="POST" style="display:inline" onsubmit="return confirm('Yakin menghapus?');">
                        <button type="submit" name="delete_course" value="<?= $c['id'] ?>" class="btn btn-sm btn-error">Hapus</button>
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
                    <a class="page-link" href="/admin/courses.php?page=<?= $page - 1 ?>" aria-label="Previous">&laquo;</a>
                </li>
                <?php for ($p = max(1, $page - 2); $p <= min($total_pages, $page + 2); $p++): ?>
                    <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                        <a class="page-link" href="/admin/courses.php?page=<?= $p ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="/admin/courses.php?page=<?= $page + 1 ?>" aria-label="Next">&raquo;</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>
</body>
</html>
