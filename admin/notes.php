<?php
/**
 * CampusHub - Admin: Notes
 * Mengelola data catatan mahasiswa (CRUD)
 * Fitur: Kategori, favorit, filter per mata kuliah
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

require_role('admin');

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$course_filter = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

$where = '';
$params = [];

if ($course_filter) {
    $where .= ' AND course_id = ?';
    $params[] = $course_filter;
}

if ($category_filter) {
    $where .= ($where ? ' AND ' : ' WHERE ') . ' category = ?';
    $params[] = $category_filter;
}

if ($search) {
    $where .= ($where ? ' AND ' : ' WHERE ') . ' title LIKE ?';
    $params[] = '%' . $search . '%';
}

try {
    global $pdo;
    
    // Count
    $count_stmt = $pdo->prepare("SELECT COUNT(*) as total FROM notes $where");
    $count_stmt->execute($params);
    $total = $count_stmt->fetch()['total'];
    $total_pages = ceil($total / 10);
    
    // Get data
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $offset = ($page - 1) * 10;
    
    $stmt = $pdo->prepare("SELECT n.*, u.full_name as student_name, c.name as course_name FROM notes n LEFT JOIN users u ON n.user_id = u.id LEFT JOIN courses c ON n.course_id = c.id $where ORDER BY n.is_favorite DESC, n.created_at DESC LIMIT ? OFFSET ?");
    $stmt->execute(array_merge($params, [10, $offset]));
    $notes = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Admin notes error: ' . $e->getMessage());
    $notes = [];
    $total_pages = 1;
}
?>
<div class="padding-x4">
    <!-- Header -->
    <header class="mb-6">
        <div class="d-flex justify-between align-items-center">
            <h1 class="text-3xl font-bold">Kelola Catatan</h1>
            <a href="/admin/notes-add.php" class="btn btn-primary">
                <i class='bx bx-plus me-2'></i>Tambah Catatan
            </a>
        </div>
    </header>
    
    <!-- Filters -->
    <div class="card p-4 mb-4">
        <form action="/admin/notes.php" method="GET" class="row g-3">
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
                <select name="category" class="form-select">
                    <option value="">Semua Kategori</option>
                    <option value="lecture" <?= ($category_filter == 'lecture') ? 'selected' : '' ?>>Mengajar</option>
                    <option value="study" <?= ($category_filter == 'study') ? 'selected' : '' ?>>Belajar</option>
                    <option value="reference" <?= ($category_filter == 'reference') ? 'selected' : '' ?>>Referensi</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Cari judul catatan...">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
    
    <!-- Notes Table -->
    <div class="card">
        <div class="card-header px-6 py-3 border-b">
            <h4 class="text font-medium">Daftar Catatan <?= $total ?> total</h4>
        </div>
        <div class="card-body px-6 py-4">
            <?php if (!empty($notes)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-left">Judul</th>
                                <th class="text-left">Mahasiswa</th>
                                <th class="text-left">Mata Kuliah</th>
                                <th class="text-center">Kategori</th>
                                <th class="text-center">Favorit</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notes as $n): ?>
                                <tr>
                                    <td class="font-medium">
                                        <a href="/notes-detail.php?id=<?= $n['id'] ?>">
                                            <?= htmlspecialchars($n['title']) ?>
                                        </a>
                                    </td>
                                    <td class="text-muted small">
                                        <?= htmlspecialchars($n['student_name'] ?? '') ?>
                                    </td>
                                    <td class="text-muted small">
                                        <?= htmlspecialchars($n['course_name'] ?? '') ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-<?= $n['category'] ?> small">
                                            <?= ucfirst($n['category']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($n['is_favorite']): ?>
                                            <span class="badge badge-success">⭐</span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
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
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-muted">
                    <i class='bx bx-sticky text-4xl mb-3'></i>
                    <p>Belum ada catatan terdaftar</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav aria-label="Page navigation" class="mt-6">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="/admin/notes.php?page=<?= max(1, $page - 1)" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($p = max(1, $page - 2); $p <= min($total_pages, $page + 2); $p++): ?>
                    <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                        <a class="page-link" href="/admin/notes.php?page=<?= $p ?>"><?= $p ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                    <a class="page-link" href="/admin/notes.php?page=<?= min($total_pages, $page + 1)" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/foot-starter.php'; ?>