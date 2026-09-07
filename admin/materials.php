<?php
/**
 * CampusHub - Admin: Materials
 * Mengelola data materi belajar (CRUD)
 * Fitur: Upload file, link external, kategori
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

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
    
    // Count
    $count_stmt = $pdo->prepare("SELECT COUNT(*) as total FROM materials $where");
    $count_stmt->execute($params);
    $total = $count_stmt->fetch()['total'];
    $total_pages = ceil($total / 10);
    
    // Get data
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $offset = ($page - 1) * 10;
    
    $stmt = $pdo->prepare("SELECT m.*, c.name as course_name FROM materials m LEFT JOIN courses c ON m.course_id = c.id $where ORDER BY m.sort_order ASC, m.created_at DESC LIMIT ? OFFSET ?");
    $stmt->execute(array_merge($params, [10, $offset]));
    $materials = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Admin materials error: ' . $e->getMessage());
    $materials = [];
    $total_pages = 1;
}
?>
<div class="padding-x4">
    <!-- Header -->
    <header class="mb-6">
        <div class="d-flex justify-between align-items-center">
            <h1 class="text-3xl font-bold">Kelola Materi</h1>
            <a href="/admin/materials-add.php" class="btn btn-primary">
                <i class='bx bx-plus me-2'></i>Tambah Materi
            </a>
        </div>
    </header>
    
    <!-- Filters -->
    <div class="card p-4 mb-4">
        <form action="/admin/materials.php" method="GET" class="row g-3">
            <div class="col-md-4">
                <select name="course_id" class="form-select">
                    <option value="">Semua Mata Kuliah</option>
                    <?php try {
                        $cstmt = $pdo->prepare('SELECT id, name FROM courses ORDER BY name');
                        $cstmt->execute();
                        $courses = $cstmt->fetchAll();
                        foreach ($courses as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $course_filter == $c['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    } catch (Exception $e) {} ?>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari judul materi...">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
    
    <!-- Materials Table -->
    <div class="card">
        <div class="card-header px-6 py-3 border-b">
            <h4 class="text font-medium">Daftar Materi <?= $total ?> total</h4>
        </div>
        <div class="card-body px-6 py-4">
            <?php if (!empty($materials)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-left">Judul</th>
                                <th class="text-left">Tipe</th>
                                <th class="text-left">Mata Kuliah</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materials as $m): ?>
                                <tr>
                                    <td class="font-medium">
                                        <a href="/materials-detail.php?id=<?= $m['id'] ?>">
                                            <?= htmlspecialchars($m['title']) ?>
                                        </a>
                                    </td>
                                    <td class="text-muted small">
                                        <?= $m['file_type'] ?>
                                    </td>
                                    <td class="text-muted small">
                                        <?= htmlspecialchars($m['course_name'] ?? '') ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="/admin/materials-edit.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline">Edit</a>
                                        <form method="POST" style="display:inline" onsubmit="return confirm('Yakin menghapus?');">
                                            <button type="submit" name="delete_material" value="<?= $m['id'] ?>" class="btn btn-sm btn-error">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-muted">
                    <i class='bx bx-folder text-4xl mb-3'></i>
                    <p>Belum ada materi terdaftar</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-6">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="/admin/materials.php?page=<?= max(1, $page - 1)" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($p = max(1, $page - 2); $p <= min($total_pages, $page + 2); $p++): ?>
                    <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                        <a class="page-link" href="/admin/materials.php?page=<?= $p ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="/admin/materials.php?page=<?= min($total_pages, $page + 1)" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/foot-starter.php'; ?>