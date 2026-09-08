<?php
/**
 * CampusHub - Halaman Mata Kuliah
 * Menampilkan daftar mata kuliah yang terdaftar
 * Fitur: View courses, add new course
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
            <h1 class="text-3xl font-bold">Mata Kuliah</h1>
            <?php if (($user['role'] ?? '') === 'admin'): ?>
            <a href="/admin/courses-add.php" class="btn btn-primary">
                <i class='bx bx-plus me-2'></i>Tambah Mata Kuliah
            </a>
            <?php endif; ?>
        </div>
    </header>
    
    <!-- Search & Filter -->
    <div class="card p-4 mb-4">
        <form action="/courses.php" method="GET" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Cari mata kuliah...">
            </div>
            <div class="col-md-4">
                <select name="semester" class="form-select">
                    <option value="">Semester Semua</option>
                    <?php for ($i = 1; $i <= 8; $i++): ?>
                        <option value="<?= $i ?>">Semester <?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
    
    <!-- Courses Table -->
    <div class="card">
        <div class="card-header px-6 py-3 border-b">
            <h4 class="text font-medium">Daftar Mata Kuliah</h4>
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
                                    <td class="text-muted"><?= htmlspecialchars($course['program_study'] ?? '-') ?></td>
                                    <td class="text-center"><?= $course['credits'] ?></td>
                                    <td class="text-muted small"><?= htmlspecialchars($course['professor'] ?? '-') ?></td>
                                    <td class="text-end">
                                        <a href="/courses-detail.php?id=<?= $course['id'] ?>" class="btn btn-sm btn-outline">Detail</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-muted">
                    <i class='bx bx-folder-open text-4xl mb-3'></i>
                    <p>Belum ada mata kuliah terdaftar</p>
                    <?php if (($user['role'] ?? '') === 'admin'): ?>
                    <a href="/admin/courses-add.php" class="btn btn-primary mt-3">Tambah Pertama</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/foot-starter.php'; ?>