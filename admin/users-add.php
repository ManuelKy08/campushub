<?php
/**
 * CampusHub - Admin: Add User
 * Fungsi: Menambah user baru ke database
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Cek role admin saja
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $nim = trim($_POST['nim'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = $_POST['role'] ?? 'student';
    $program_study = trim($_POST['program_study'] ?? '');
    $semester = intval($_POST['semester'] ?? '1');
    
    // Validasi
    $errors = [];
    
    if (empty($full_name)) {
        $errors[] = 'Nama lengkap wajib diisi';
    }
    if (empty($nim)) {
        $errors[] = 'NIM wajib diisi';
    } elseif (strlen($nim) < 6) {
        $errors[] = 'NIM minimal 6 karakter';
    }
    if (empty($email)) {
        $errors[] = 'Email wajib diisi';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid';
    }
    if (empty($password)) {
        $errors[] = 'Password wajib diisi';
    } elseif (strlen($password) < 8) {
        $errors[] = 'Password minimal 8 karakter';
    }
    
    // Cek duplikat email/NIM
    if (empty($errors)) {
        try {
            global $pdo;
            
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = 'Email sudah terdaftar';
            }
            
            $stmt = $pdo->prepare('SELECT id FROM users WHERE nim = ?');
            $stmt->execute([$nim]);
            if ($stmt->fetch()) {
                $errors[] = 'NIM sudah terdaftar';
            }
        } catch (PDOException $e) {
            error_log('Check duplicate error: ' . $e->getMessage());
            $errors[] = 'Terjadi kesalahan sistem';
        }
    }
    
    // Simpan ke database
    if (empty($errors)) {
        try {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare(
                'INSERT INTO users (full_name, nim, email, password_hash, role, program_study, semester, is_active) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, 1)'
            );
            
            $result = $stmt->execute([
                $full_name,
                $nim,
                $email,
                $password_hash,
                $role,
                $program_study,
                $semester
            ]);
            
            if ($result) {
                header('Location: /admin/users.php');
                exit;
            }
        } catch (PDOException $e) {
            error_log('Add user error: ' . $e->getMessage());
            $errors[] = 'Gagal menyimpan user';
        }
    }
}
?>

<div class="padding-x4">
    <h1 class="text-3xl font-bold mb-4">Tambah User Baru</h1>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error mb-4">
            <i class='bx bx-error-circle me-2'></i>
            <ul class="mb-2 list-disc pl-3">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form action="" method="POST" class="max-w-md">
        <div class="grid grid-cols-2 gap-4 mb-4">
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
        <div class="grid grid-cols-2 gap-4 mb-4">
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
        <div class="grid grid-cols-2 gap-4 mb-4">
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
        <button type="submit" name="add_user"
                class="w-full btn btn-primary py-3 rounded font-medium mt-4">
            Simpan User
        </button>
    </form>
</div>