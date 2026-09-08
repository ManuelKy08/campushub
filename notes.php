<?php
/**
 * CampusHub - Halaman Catatan
 * Menampilkan dan mengelola catatan mahasiswa per mata kuliah
 * Fitur: View notes, add new note, edit, delete, favorite
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

require_login();
$user = get_user();
$user_id = $user['id'];
?>

<div class="padding-x4">
    <!-- Header -->
    <header class="mb-6">
        <div class="d-flex justify-between align-items-center">
            <h1 class="text-3xl font-bold">Catatan Saya</h1>
            <?php if (($user['role'] ?? '') === 'admin'): ?>
            <a href="/admin/notes-add.php" class="btn btn-primary">
                <i class='bx bx-plus me-2'></i>Catatan Baru
            </a>
            <?php endif; ?>
        </div>
    </header>
    
    <!-- Filters -->
    <div class="card p-4 mb-4">
        <form action="/notes.php" method="GET" class="row g-3">
            <div class="col-md-3">
                <select name="course_id" class="form-select">
                    <option value="">Semua Mata Kuliah</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select">
                    <option value="">Semua Kategori</option>
                    <option value="lecture">Mengajar</option>
                    <option value="study">Belajar</option>
                    <option value="reference">Referensi</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="favorite" class="form-select">
                    <option value="">Semua</option>
                    <option value="1">Hanya Favorit</option>
                    <option value="0">Semua</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
    
    <!-- Notes Table -->
    <div class="card">
        <div class="card-header px-6 py-3 border-b">
            <h4 class="text font-medium">Catatan Saya</h4>
        </div>
        <div class="card-body px-6 py-4">
            <?php if (!empty($notes)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-left">Judul</th>
                                <th class="text-left">Mata Kuliah</th>
                                <th class="text-center">Kategori</th>
                                <th class="text-center">Favorit</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notes as $note): ?>
                                <tr>
                                    <td class="font-medium">
                                        <a href="/notes-detail.php?id=<?= $note['id'] ?>">
                                            <?= htmlspecialchars($note['title']) ?>
                                        </a>
                                    </td>
                                    <td class="text-muted small">
                                        <?= htmlspecialchars($note['course_name'] ?? '') ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-<?= $note['category'] ?> small">
                                            <?= ucfirst($note['category']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($note['is_favorite']): ?>
                                            <span class="badge badge-success">⭐</span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="/notes-detail.php?id=<?= $note['id'] ?>" class="btn btn-sm btn-outline">Lihat</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-muted">
                    <i class='bx bx-sticky text-4xl mb-3'></i>
                    <p>Belum ada catatan</p>
                    <?php if (($user['role'] ?? '') === 'admin'): ?>
                    <a href="/admin/notes-add.php" class="btn btn-primary mt-3">Tambah Catatan Pertama</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/foot-starter.php'; ?>