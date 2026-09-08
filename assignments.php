<?php
/**
 * CampusHub - Halaman Tugas
 * Menampilkan daftar tugas dengan filter berdasarkan status dan mata kuliah
 * Fitur: View assignments, add new assignment, filter by course/status
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

require_login();
$user = get_user();
?>

<div class="padding-x4">
    <!-- Header -->
    <header class="mb-6">
        <div class="d-flex justify-between align-items-center">
            <h1 class="text-3xl font-bold">Tugas</h1>
            <?php if (($user['role'] ?? '') === 'admin'): ?>
            <a href="/admin/assignments-add.php" class="btn btn-primary">
                <i class='bx bx-plus me-2'></i>Tambah Tugas Baru
            </a>
            <?php endif; ?>
        </div>
    </header>
    
    <!-- Filters -->
    <div class="card p-4 mb-4">
        <form action="/assignments.php" method="GET" class="row g-3">
            <div class="col-md-3">
                <select name="course_id" class="form-select">
                    <option value="">Semua Mata Kuliah</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['name']) ?> (<?= $course['code'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="late">Late</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="priority" class="form-select">
                    <option value="">Semua Prioritas</option>
                    <option value="urgent">Urgent</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
    
    <!-- Assignments Table -->
    <div class="card">
        <div class="card-header px-6 py-3 border-b">
            <h4 class="text font-medium">Daftar Tugas</h4>
        </div>
        <div class="card-body px-6 py-4">
            <?php if (!empty($assignments)): ?>
                <div class="table-responsive">
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
                            <?php foreach ($assignments as $assignment): ?>
                                <tr>
                                    <td class="font-medium">
                                        <a href="/assignments-detail.php?id=<?= $assignment['id'] ?>">
                                            <?= htmlspecialchars($assignment['title']) ?>
                                        </a>
                                    </td>
                                    <td class="text-muted small">
                                        <?= htmlspecialchars($assignment['course_name'] ?? 'Tidak diketahui') ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="text-sm"><?= date('d M Y H:i', strtotime($assignment['deadline'])) ?></span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $assignment['priority'] ?> small">
                                            <?= ucfirst($assignment['priority']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $assignment['status'] === 'completed' ? 'success' : 'warning' ?>">
                                            <?= ucfirst($assignment['status']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="/assignments-detail.php?id=<?= $assignment['id'] ?>" class="btn btn-sm btn-outline">Detail</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-muted">
                    <i class='bx bx-briefcard text-4xl mb-3'></i>
                    <p>Belum ada tugas terdaftar</p>
                    <?php if (($user['role'] ?? '') === 'admin'): ?>
                    <a href="/admin/assignments-add.php" class="btn btn-primary mt-3">Tambah Tugas Pertama</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/foot-starter.php'; ?>