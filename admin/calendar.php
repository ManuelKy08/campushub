<?php
/**
 * CampusHub - Admin: Calendar
 * Mengelola data acara akademik dan deadline (CRUD)
 * Fitur: Filter tipe, tanggal, mata kuliah
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

require_role('admin');

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$type_filter = isset($_GET['type']) ? $_GET['type'] : '';
$course_filter = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

$where = '';
$params = [];

if ($course_filter) {
    $where .= ' AND course_id = ?';
    $params[] = $course_filter;
}

if ($type_filter) {
    $where .= ($where ? ' AND ' : ' WHERE ') . ' type = ?';
    $params[] = $type_filter;
}

if ($search) {
    $where .= ($where ? ' AND ' : ' WHERE ') . ' title LIKE ?';
    $params[] = '%' . $search . '%';
}

try {
    global $pdo;
    
    // Count
    $count_stmt = $pdo->prepare("SELECT COUNT(*) as total FROM events $where");
    $count_stmt->execute($params);
    $total = $count_stmt->fetch()['total'];
    $total_pages = ceil($total / 10);
    
    // Get data
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $offset = ($page - 1) * 10;
    
    $stmt = $pdo->prepare("SELECT e.*, c.name as course_name FROM events e LEFT JOIN courses c ON e.course_id = c.id $where ORDER BY e.start_time ASC LIMIT ? OFFSET ?");
    $stmt->execute(array_merge($params, [10, $offset]));
    $events = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Admin calendar error: ' . $e->getMessage());
    $events = [];
    $total_pages = 1;
}
?>
<div class="padding-x4">
    <!-- Header -->
    <header class="mb-6">
        <div class="d-flex justify-between align-items-center">
            <h1 class="text-3xl font-bold">Kelola Kalender</h1>
            <a href="/admin/calendar-add.php" class="btn btn-primary">
                <i class='bx bx-plus me-2'></i>Tambah Acara
            </a>
        </div>
    </header>
    
    <!-- Filters -->
    <div class="card p-4 mb-4">
        <form action="/admin/calendar.php" method="GET" class="row g-3">
            <div class="col-md-3">
                <select name="type" class="form-select">
                    <option value="">Tipe Semua</option>
                    <option value="deadline" <?= ($type_filter == 'deadline') ? 'selected' : '' ?>>Deadline</option>
                    <option value="lecture" <?= ($type_filter == 'lecture') ? 'selected' : '' ?>>Lecture</option>
                    <option value="event" <?= ($type_filter == 'event') ? 'selected' : '' ?>>Acara</option>
                    <option value="milestone" <?= ($type_filter == 'milestone') ? 'selected' : '' ?>>Milenial</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="course_id" class="form-select">
                    <option value="">Semua Mata Kuliah</option>
                    <?php try {
                        $cstmt = $pdo->prepare('SELECT id, name FROM courses ORDER BY name');
                        $cstmt->execute();
                        $courses = $cstmt->fetchAll();
                        foreach ($courses as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($course_filter == $c['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    } catch (Exception $e) {} ?>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Cari judul acara...">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
    
    <!-- Events Table -->
    <div class="card">
        <div class="card-header px-6 py-3 border-b">
            <h4 class="text font-medium">Daftar Acara <?= $total ?> total</h4>
        </div>
        <div class="card-body px-6 py-4">
            <?php if (!empty($events)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-left">Judul</th>
                                <th class="text-left">Mata Kuliah</th>
                                <th class="text-center">Tipe</th>
                                <th class="text-center">Waktu</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $e): ?>
                                <tr>
                                    <td class="font-medium">
                                        <a href="/calendar-detail.php?id=<?= $e['id'] ?>">
                                            <?= htmlspecialchars($e['title']) ?>
                                        </a>
                                    </td>
                                    <td class="text-muted small">
                                        <?= htmlspecialchars($e['course_name'] ?? '') ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $e['type'] ?> small">
                                            <?= ucfirst($e['type']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center small">
                                        <?php if ($e['is_all_day']): ?>
                                            <?= date('d F Y', strtotime($e['start_time'])) ?>
                                        <?php else: ?>
                                            <?= date('d M H:i', strtotime($e['start_time'])) ?> -
                                            <?= $e['end_time'] ? date('H:i', strtotime($e['end_time'])) : '23:59' ?>
                                        <?php endif; ?>
                                    </td>
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
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-muted">
                    <i class='bx bx-calendar text-4xl mb-3'></i>
                    <p>Belum ada acara terdaftar</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-6">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="/admin/calendar.php?page=<?= max(1, $page - 1)" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($p = max(1, $page - 2); $p <= min($total_pages, $page + 2); $p++): ?>
                    <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                        <a class="page-link" href="/admin/calendar.php?page=<?= $p ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="/admin/calendar.php?page=<?= min($total_pages, $page + 1)" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/foot-starter.php'; ?>