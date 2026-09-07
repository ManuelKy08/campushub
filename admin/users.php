<?php
/**
 * CampusHub - Admin Panel: Users
 * Mengelola data pengguna (mahasiswa dan admin)
 * Fitur: Lihat daftar user, aktivasi/nonaktifkan, hapus, filter role
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

// Cek role admin hanya
require_role('admin');

$role_filter = isset($_GET['role']) ? $_GET['role'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
?>

<div class="padding-x4">
    <!-- Header -->
    <header class="mb-6">
        <div class="d-flex justify-between align-items-center">
            <h1 class="text-3xl font-bold">Kelola Pengguna</h1>
            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class='bx bx-plus me-2'></i>Tambah User
            </a>
        </div>
    </header>
    
    <!-- Filter -->
    <div class="card p-4 mb-4">
        <form action="/admin/users.php" method="GET" class="row g-3">
            <div class="col-md-3">
                <select name="role" class="form-select">
                    <option value="all">Semua Role</option>
                    <option value="admin">Admin</option>
                    <option value="student">Mahasiswa</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari NIM/Email/Nama..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
    
    <!-- Users Table -->
    <div class="card">
        <div class="card-header px-6 py-3 border-b">
            <h4 class="text font-medium">Daftar Pengguna</h4>
        </div>
        <div class="card-body px-6 py-4">
            <?php 
            global $pdo;
            $where = '';
            $params = [];
            
            if ($role_filter !== 'all') {
                $where .= ' AND role = ?';
                $params[] = $role_filter;
            }
            if ($search) {
                $where .= ' AND (nim LIKE ? OR email LIKE ? OR full_name LIKE ?)';
                $params[] = '%' . $search . '%';
                $params[] = '%' . $search . '%';
                $params[] = '%' . $search . '%';
            }
            
            $stmt = $pdo->prepare("SELECT id, full_name, nim, email, role, program_study, semester, is_active, last_login, created_at FROM users WHERE 1 $where");
            $stmt->execute($params);
            $users = $stmt->fetchAll();
            ?>
            
            <?php if (!empty($users)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-left">Nama</th>
                                <th class="text-left">NIM / Email</th>
                                <th class="text-left">Role</th>
                                <th class="text-left">Program Studi</th>
                                <th class="text-center">Semester</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><?= htmlspecialchars($u['full_name']) ?></td>
                                    <td>
                                        <div class="text-sm">
                                            <span class="font-medium"><?= htmlspecialchars($u['nim']) ?></span>
                                            <br>
                                            <small class="text-muted"><?= htmlspecialchars($u['email']) ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $u['role'] === 'admin' ? 'error' : 'success' ?>">
                                            <?= ucfirst($u['role']) ?>
                                        </span>
                                    </td>
                                    <td class="text-muted small"><?= htmlspecialchars($u['program_study'] ?? '-') ?></td>
                                    <td class="text-center small"><?= $u['semester'] ?></td>
                                    <td class="text-center">
                                        <?php if ($u['is_active']): ?>
                                            <span class="badge badge-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge badge-error">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="/admin/users-detail.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline">Detail</a>
                                        <?php if ($u['role'] === 'student'): ?>
                                            <a href="/admin/users-toggle.php?id=<?= $u['id'] ?>" class="btn btn-sm <?= $u['is_active'] ? 'btn-outline' : 'btn-primary' ?>" 
                                               onclick="return confirm('Yakin mengubah status?')">
                                                <?= $u['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-muted">
                    <i class='bx bx-user-x text-4xl mb-3'></i>
                    <p>Tidak ada pengguna ditemukan</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="/admin/users-add.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah User Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Nama Lengkap</label>
                            <input type="text" name="full_name" required
                                   class="w-full px-4 py-3 rounded border">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">NIM</label>
                            <input type="text" name="nim" required
                                   class="w-full px-4 py-3 rounded border">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Email</label>
                            <input type="email" name="email" required
                                   class="w-full px-4 py-3 rounded border">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Password</label>
                            <input type="password" name="password" required
                                   class="w-full px-4 py-3 rounded border"
                                   placeholder="minimal 8 karakter">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Role</label>
                            <select name="role" required class="w-full px-4 py-3 rounded border">
                                <option value="student">Mahasiswa</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Program Studi</label>
                            <input type="text" name="program_study"
                                   class="w-full px-4 py-3 rounded border">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Semester</label>
                        <select name="semester" required class="w-full px-4 py-3 rounded border">
                            <?php for ($i = 1; $i <= 8; $i++): ?>
                                <option value="<?= $i ?>">Semester <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/foot-starter.php'; ?>