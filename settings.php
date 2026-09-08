<?php
/**
 * CampusHub - Halaman Settings
 * Pengaturan akun: theme, password, preferensi
 * Fitur: Light/Dark mode switch, password change, account preferences
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
            <h1 class="text-3xl font-bold">Settings</h1>
        </div>
    </header>
    
    <!-- Theme Settings -->
    <div class="card mb-6">
        <div class="card-header px-6 py-3 border-b">
            <h4 class="text font-medium">Tema</h4>
        </div>
        <div class="card-body px-6 py-4">
            <p class="text-muted mb-4">Pilih tampilan yang nyaman untuk mata Anda</p>
            
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="theme" id="theme-light" value="light" <?= (!isset($_COOKIE['theme']) || $_COOKIE['theme'] !== 'dark') ? 'checked' : '' ?>>
                <label class="form-check-label" for="theme-light">
                    <div class="w-11 h-6 rounded-full bg-gray-200 position-relative">
                        <span class="bg-white rounded-half position-absolute top-0 start-0 w-3 h-3 shadow" id="light-mode-handle"></span>
                        <span class="bg-white rounded-half position-absolute bottom-0 start-0 w-3 h-3 shadow" id="dark-mode-handle"></span>
                    </div>
                    <span class="mx-2">Light</span>
                </label>
            </div>
            
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="theme" id="theme-dark" value="dark" <?= isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'checked' : '' ?>>
                <label class="form-check-label" for="theme-dark">
                    <div class="w-11 h-6 rounded-bg bg-gray-800 position-relative">
                        <span class="bg-white rounded-hall position-absolute top-0 start-0 w-3 h-3 shadow" id="light-mode-handle2"></span>
                        <span class="bg-white rounded-hall position-absolute bottom-0 start-0 w-3 h-3 shadow" id="dark-mode-handle2"></span>
                    </div>
                    <span class="mx-2">Dark</span>
                </label>
            </div>
            
            <small class="text-muted mt-3">Pilihan akan disimpan di localStorage</small>
        </div>
    </div>
    
    <!-- Password Change -->
    <div class="card">
        <div class="card-header px-6 py-3 border-b">
            <h4 class="text font-medium">Ubah Password</h4>
        </div>
        <div class="card-body px-6 py-4">
            <form action="/settings.php" method="POST" class="space-y-4">
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
    
    <!-- Account Information -->
    <div class="mt-6 pt-6 border-t">
        <h4 class="text-sm font-medium mb-4">Informasi Akun</h4>
        <div class="space-y-3 text-sm">
            <div>
                <span class="font-medium">Email:</span> <?= htmlspecialchars($user['email']) ?>
            </div>
            <div>
                <span class="font-medium">NIM:</span> <?= htmlspecialchars($user['nim']) ?>
            </div>
            <div>
                <span class="font-medium">Program Studi:</span> <?= htmlspecialchars($user['program_study'] ?? '-') ?>
            </div>
            <div>
                <span class="font-medium">Semester:</span> <?= $user['semester'] ?? 1 ?>
            </div>
            <div>
                <span class="font-medium">Login Terakhir:</span> <?= $user['last_login'] ? date('d M Y H:i', strtotime($user['last_login'])) : 'Belum pernah' ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/foot-starter.php'; ?>