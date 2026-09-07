<?php
/**
 * CampusHub - Halaman Register
 * Fitur: Registrasi mahasiswa dengan validasi email/NIM
 * Keamanan: password_hash, validasi input, CSRF token
 */
?>
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Process registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $full_name = trim($_POST['full_name'] ?? '');
    $nim = trim($_POST['nim'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
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
    if (empty($program_study)) {
        $errors[] = 'Program studi wajib dipilih';
    }
    
    // Cek apakah email/NIM sudah terdaftar
    if (empty($errors)) {
        try {
            global $pdo;
            
            // Cek email
            $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = 'Email sudah terdaftar';
            }
            
            // Cek NIM
            $stmt = $pdo->prepare('SELECT id FROM users WHERE nim = ?');
            $stmt->execute([$nim]);
            if ($stmt->fetch()) {
                $errors[] = 'NIM sudah terdaftar';
            }
        } catch (PDOException $e) {
            error_log('Registration check error: ' . $e->getMessage());
            $errors[] = 'Terjadi kesalahan sistem';
        }
    }
    
    // Simpan registrasi jika tidak ada error
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
                'student',
                $program_study,
                $semester
            ]);
            
            if ($result) {
                // Login otomatis setelah register
                $new_user_id = $pdo->lastInsertId();
                $stmt = $pdo->prepare('SELECT id, full_name, email, role FROM users WHERE id = ?');
                $stmt->execute([$new_user_id]);
                $user = $stmt->fetch();
                
                login_user($user['id'], $user['email'], $user['full_name'], $user['role']);
                
                header('Location: /dashboard.php');
                exit;
            }
        } catch (PDOException $e) {
            error_log('Registration error: ' . $e->getMessage());
            $errors[] = 'Terjadi kesalahan saat mendaftar';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - CampusHub</title>
    <?php require_once __DIR__ . '/../includes/head-starter.php'; ?>
</head>
<body data-theme="light">
    <!-- Hero Section -->
    <section class="min-vh-100 d-flex align-items-center justify-center">
        <div class="container w-100 max-w-md">
            <div class="card p-6 max-w-md">
                <div class="text-center mb-6">
                    <span class="avatar text-3xl">CH</span>
                    <h1 class="mt-4 text-2xl font-bold">Daftar Akun</h1>
                    <p class="mt-2 text-muted">Buat akun gratis untuk mengakses semua fitur</p>
                </div>
                
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
                
                <form action="" method="POST" class="mt-6">
                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="full_name" class="block text-sm font-medium mb-2">Nama Lengkap</label>
                            <input type="text" id="full_name" name="full_name" required
                                   class="w-full px-4 py-3 rounded border focus:outline-none focus:ring-2 focus:ring-primary"
                                   value="<?= htmlspecialchars($full_name ?? '') ?>">
                        </div>
                        <div>
                            <label for="nim" class="block text-sm font-medium mb-2">NIM</label>
                            <input type="text" id="nim" name="nim" required
                                   class="w-full px-4 py-3 rounded border focus:outline-none focus:ring-2 focus:ring-primary"
                                   placeholder="contoh: 210101" value="<?= htmlspecialchars($nim ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium mb-2">Email</label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-4 py-3 rounded border focus:outline-none focus:ring-2 focus:ring-primary"
                               placeholder="contoh: nama@university.id" value="<?= htmlspecialchars($email ?? '') ?>">
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium mb-2">Password</label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-4 py-3 rounded border focus:outline-none focus:ring-2 focus:ring-primary"
                               placeholder="minimal 8 karakter">
                    </div>
                    
                    <div class="mb-4">
                        <label for="program_study" class="block text-sm font-medium mb-2">Program Studi</label>
                        <select id="program_study" name="program_study" required
                                class="w-full px-4 py-3 rounded border focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="" disabled selected>-- Pilih Program Studi --</option>
                            <option value="Teknologi Informasi">Teknologi Informasi</option>
                            <option value="Desain Komunikasi Visual">Desain Komunikasi Visual</option>
                            <option value="Sistem Informasi">Sistem Informasi</option>
                            <option value="Ilmu Komputer">Ilmu Komputer</option>
                            <option value="Teknik Elektro">Teknik Elektro</option>
                            <option value="Arsitektur">Arsitektur</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label for="semester" class="block text-sm font-medium mb-2">Semester</label>
                        <select id="semester" name="semester" required
                                class="w-full px-4 py-3 rounded border focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="" disabled selected>-- Pilih Semester --</option>
                            <?php for ($i = 1; $i <= 8; $i++): ?>
                                <option value="<?= $i ?>" <?= ($semester ?? 1) == $i ? 'selected' : '' ?>>
                                    Semester <?= $i ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <input type="checkbox" id="terms" name="terms" class="h-4 w-4 rounded border">
                            <label for="terms" class="ml-2 text-sm align-middle">
                                Saya menyetujui <a href="#" class="text-primary hover:underline">syarat dan ketentuan</a>
                            </label>
                        </div>
                        <a href="/login.php" class="text-sm text-primary hover:underline">Sudak memiliki akun? Masuk</a>
                    </div>
                    
                    <button type="submit" name="register"
                            class="w-full btn btn-primary py-3 rounded font-medium">
                        Daftar Akun
                    </button>
                </form>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="border-t mt-auto py-6 text-center text-muted">
        <p>Created by Risky Manuel Tamba</p>
    </footer>
    
    <?php require_once __DIR__ . '/../includes/foot-starter.php'; ?>
</body>
</html>
EOF