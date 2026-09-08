<?php
/**
 * CampusHub - Halaman Materi
 * Menampilkan daftar materi belajar per mata kuliah
 * Fitur: View materials, add new material, file upload support
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
            <h1 class="text-3xl font-bold">Materi Belajar</h1>
            <a href="/materials-add.php" class="btn btn-primary">
                <i class='bx bx-plus me-2'></i>Tambah Materi
            </a>
        </div>
    </header>
    
    <!-- Filters -->
    <div class="card p-4 mb-4">
        <form action="/materials.php" method="GET" class="row g-3">
            <div class="col-md-4">
                <select name="course_id" class="form-select">
                    <option value="">Semua Mata Kuliah</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="file_type" class="form-select">
                    <option value="">Semua Tipe</option>
                    <option value="pdf">PDF</option>
                    <option value="video">Video</option>
                    <option value="document">Dokumen</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
    
    <!-- Materials Table -->
    <div class="card">
        <div class="card-header px-6 py-3 border-b">
            <h4 class="text font-medium">Daftar Materi</h4>
        </div>
        <div class="card-body px-6 py-4">
            <?php if (!empty($materials)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-left">Judul</th>
                                <th class="text-left">Tipe</th>
                                <th class="text-center">Mata Kuliah</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materials as $material): ?>
                                <tr>
                                    <td class="font-medium">
                                        <a href="/materials-detail.php?id=<?= $material['id'] ?>">
                                            <?= htmlspecialchars($material['title']) ?>
                                        </a>
                                    </td>
                                    <td class="text-muted small">
                                        <?= $material['file_type'] ?>
                                    </td>
                                    <td class="text-muted small">
                                        <?= htmlspecialchars($material['course_name'] ?? '') ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($material['file_path']): ?>
                                            <a href="/download.php?file=<?= $material['file_path'] ?>" class="btn btn-sm btn-outline">Download</a>
                                        <?php elseif ($material['url_link']): ?>
                                            <a href="<?= htmlspecialchars($material['url_link']) ?>" target="_blank" class="btn btn-sm btn-outline">Lihat</a>
                                        <?php else: ?>
                                            <span class="text-xs">Tidak ada file</span>
                                        <?php endif; ?>
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
                    <a href="/materials-add.php" class="btn btn-primary mt-3">Tambah Materi Pertama</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/foot-starter.php'; ?>