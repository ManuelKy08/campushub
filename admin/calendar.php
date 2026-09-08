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
    $count_stmt = $pdo->prepare("SELECT COUNT(*) as total FROM events $where");
    $count_stmt->execute($params);
    $total = $count_stmt->fetch()['total'];
    $total_pages = ceil($total / 10);
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $offset = ($page - 1) * 10;
    $stmt = $pdo->prepare("SELECT * FROM events $where ORDER BY start_date ASC LIMIT ? OFFSET ?");
    $stmt->execute(array_merge($params, [10, $offset]));
    $events = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Admin calendar error: ' . $e->getMessage());
    $events = [];
    $total_pages = 1;
}
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kalender - CampusHub</title>
    <link rel="stylesheet" href="/assets/css/global.css">
</head>
<body data-theme="light">
<div class="padding-x4">
    <h1 class="text-3xl font-bold mb-4">Kelola Kalender</h1>
    <a href="/admin/calendar-add.php" class="btn btn-primary mb-4">Tambah Event</a>
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th class="text-left">Judul</th>
                <th class="text-left">Tanggal</th>
                <th class="text-center">Status</th>
                <th class="text-end">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $e): ?>
            <tr>
                <td class="font-medium"><?= htmlspecialchars($e['title']) ?></td>
                <td><?= date('d M Y', strtotime($e['start_date'])) ?></td>
                <td class="text-center"><span class="badge badge-<?= $e['status'] === 'active' ? 'success' : 'warning' ?>"><?= ucfirst($e['status']) ?></span></td>
                <td class="text-end">
                    <a href="/admin/calendar-edit.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-outline">Edit</a>
                    <form method="POST" style="display:inline" onsubmit="return confirm('Yakin menghapus?');">
                        <button type="submit" name="delete_event" value="<?= $e['id'] ?>" class="btn btn-sm btn-error">Hapus</button>
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
                    <a class="page-link" href="/admin/calendar.php?page=<?= $page - 1 ?>" aria-label="Previous">&laquo;</a>
                </li>
                <?php for ($p = max(1, $page - 2); $p <= min($total_pages, $page + 2); $p++): ?>
                    <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                        <a class="page-link" href="/admin/calendar.php?page=<?= $p ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="/admin/calendar.php?page=<?= $page + 1 ?>" aria-label="Next">&raquo;</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>
</body>
</html>
