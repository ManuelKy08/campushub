<?php
/**
 * CampusHub - Halaman Profile
 * Menampilkan profil mahasiswa dan informasi akun
 * Fitur: Edit profil, change password, avatar
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
            <h1 class="text-3xl font-bold">Profil Saya</h1>
        </div>
    </header>
    
    <!-- Profile Card -->
    <div class="card mb-6">
        <div class="card-body p-6">
            <div class="text-center mb-6">
                <!-- Avatar -->
                <div class="avatar text-6xl mb-4">
                    <?= htmlspecialchars(substr($user['full_name'] ?? 'U', 0, 2)) ?>
                </div>
                <h2 class="text-2xl font-bold"><?= htmlspecialchars($user['full_name'] ?? 'User') ?></h2>
                <p class="text-muted">NIM: <?= htmlspecialchars($user['nim'] ?? '') ?></p>
                <p class="text-muted">Program Studi: <?= htmlspecialchars($user['program_study'] ?? '') ?></p>
                <p class="text-muted">Semester: <?= $user['semester'] ?? 1 ?></p>
            </div>
            
            <!-- Profile Stats -->
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card p-4 border-left-primary">
                        <div class=" h-12 w-12 rounded-lg bg-primary flex items-center justify-center text-white">
                            <i class='bx bx-user me-2'></i>
                            <span><?= /* courses count */ 12 ?></span>
                        </div>
                        <h3 class="mt-3 text-xs font-bold">Mata Kuliah</h3>
                        <p class="text-muted small">12 kursus terdaftar</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card p-4 border-left-success">
                        <div class=" h-12 w-12 rounded-lg bg-success flex items-center justify-center text-white">
                            <i class='bx bx-check-circle me-2'></i>
                            <span><?= /* completed tasks */ 8 ?></span>
                        </div>
                        <h3 class="mt-3 text-xs font-bold">Tugas Selesai</h3>
                        <p class="text-muted small">8 tugas telah selesai</p>
                    </div>
                </div>
            </div>
            
            <!-- Account Actions -->
            <div class="mt-6 pt-6 border-t">
                <h4 class="text-sm font-medium mb-4">Akun Saya</h4>
                <div class="space-y-3">
                    <button type="button" class="w-full text-left text-primary hover:underline pb-2 border-b">
                        <i class='bx bx-edit me-2'></i>Edit Profil
                    </button>
                    <button type="button" class="w-full text-left text-primary hover:underline pb-2 border-b">
                        <i class='bx bx-lock me-2'></i>Ubah Password
                    </button>
                    <button type="button" class="w-full text-left text-danger hover:underline pb-2 border-b">
                        <i class='bx bx-trash me-2'></i>Hapus Akun
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Change Password Form -->
    <div class="card">
        <div class="card-header px-6 py-3 border-b">
            <h4 class="text font-medium">Ubah Password</h4>
        </div>
        <div class="card-body px-6 py-4">
            <form action="/profile.php" method="POST" class="space-y-4">
                <div>
                    <label for="current_password" class="block text-sm font-medium mb-2">Password Saat Ini</label>
                    <input type="password" id="current_password" name="current_password" required
                           class="w-full px-4 py-3 rounded border">
                </div>
                
                <div>
                    <label for="new_password" class="block text-sm font-medium mb-2">Password Baru</label>
                    <input type="password" id="new_password" name="new_password" required
                           class="w-full px-4 py-3 rounded border"
                           placeholder="minimal 8 karakter">
                    <small class="text-muted">Kosongkan jika tidak ingin mengubah</small>
                </div>
                
                <div>
                    <label for="confirm_password" class="block text-sm font-medium mb-2">Konfirmasi Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required
                           class="w-full px-4 py-3 rounded border">
                    <small class="text-muted">Harus sama dengan password baru</small>
                </div>
                
                <button type="submit" name="change_password"
                        class="w-full btn btn-primary py-3 rounded font-medium">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/foot-starter.php'; ?>