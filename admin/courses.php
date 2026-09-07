<?php
/**
 * CampusHub - Admin: Courses List
 * Menampilkan daftar mata kuliah dengan fitur CRUD
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

// Cek role admin
require_role('admin');

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$per_page = 10;

// Build query
$where = '';
$params = [];

if ($search) {
    $where = ' WHERE code LIKE ? OR name LIKE ?';
    $params = ['%' . $search . '%', '%' . $search . '%'];
}

try {
    global $pdo;
    
    // Count total
    $count_stmt = $pdo->prepare("SELECT COUNT(*) as total FROM courses $where");
    $count_stmt->execute($params);
    $total = $count_stmt->fetch()['total'];
    $total_pages = ceil($total / $per_page);
    
    // Get courses
    $offset = ($page - 1) * $per_page;
    $stmt = $pdo->prepare("SELECT * FROM courses $where ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->execute(array_merge($params, [$per_page, $offset]));
    $courses = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Admin courses error: ' . $e->getMessage());
    $courses = [];
    $total_pages = 1;
}
?>
<div class="padding-x4">
    <!-- Header -->
    <header class="mb-6">
        <div class="d-flex justify-between align-items-center">
            <h1 class="text-3xl font-bold">Kelola Mata Kuliah</h1>
            <a href="/admin/courses.php?page=1" class="btn btn-primary">
                <i class='bx bx-plus me-2'></i>Tambah Mata Kuliah
            </a>
        </div>
    </header>
    
    <!-- Search -->
    <div class="card p-4 mb-4">
        <form action="/admin/courses.php" method="GET" class="row g-3">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Cari kode atau nama mata kuliah..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-3">
                <select name="page" class="form-select">
                    <option value="1">Halaman 1</option>
                    <?php for ($p = 2; $p <= max($total_pages, 1); $p++): ?>
                        <option value="<?= $p ?>" <?= $p == $page ? 'selected' : '' ?>><?= $p ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="/admin/courses-add.php" class="btn btn-sm btn-outline w-100">Tambah Baru</a>
            </div>
        </form>
    </div>
    
    <!-- Courses Table -->
    <div class="card">
        <div class="card-header px-6 py-3 border-b">
            <h4 class="text font-medium">Daftar Mata Kuliah <?= $total ?> total</h4>
        </div>
        <div class="card-body px-6 py-4">
            <?php if (!empty($courses)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-left">Kode</th>
                                <th class="text-left">Nama</th>
                                <th class="text-left">Prodi</th>
                                <th class="text-center">SKS</th>
                                <th class="text-left">Dosen</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td class="font-medium"><?= htmlspecialchars($course['code']) ?></td>
                                    <td><?= htmlspecialchars($course['name']) ?></td>
                                    <td class="text-muted small"><?= htmlspecialchars($course['program_study'] ?? '-') ?></td>
                                    <td class="text-center small"><?= $course['credits'] ?></td>
                                    <td class="text-muted small"><?= htmlspecialchars($course['professor'] ?? '-') ?></td>
                                    <td class="text-end">
                                        <a href="/admin/courses-detail.php?id=<?= $course['id'] ?>" class="btn btn-sm btn-outline">Detail</a>
                                        <a href="/admin/courses-edit.php?id=<?= $course['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <form method="POST" style="display:inline" onreturn="return confirm('Yakin menghapus?');">
                                            <button type="submit" name="delete_course" value="<?= $course['id'] ?>" class="btn btn-sm btn-error">Hapus</button>
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
                    <p>Belum ada mata kuliah terdaftar</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-6">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="/admin/courses.php?page=<?= max(1, $page - 1)" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($p = max(1, $page - 2); $p <= min($total_pages, $page + 2); $p++): ?>
                    <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                        <a class="page-link" href="/admin/courses.php?page=<?= $p ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="/admin/courses.php?page=<?= min($total_pages, $page + 1)" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/foot-starter.php'; ?>