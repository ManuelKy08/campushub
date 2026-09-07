<?php
/**
 * CampusHub - Halaman Login
 * Fitur: Login dengan email/nim, lupa password, register link
 * Validasi: PHP native, PDO Prepared Statements
 */
?>
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    // Validasi sederhana
    if (empty($email) || empty($password)) {
        $error = 'Email dan password wajib diisi';
    } else {
        try {
            global $pdo;
            // Cari user berdasarkan email
            $stmt = $pdo->prepare('SELECT id, full_name, email, password_hash, role FROM users WHERE email = ? AND is_active = 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                login_user($user['id'], $user['email'], $user['full_name'], $user['role']);
                // Redirect ke dashboard atau target URL
                $target = $_SESSION['target_url'] ?? '/dashboard.php';
                header('Location: ' . $target);
                exit;
            } else {
                $error = 'Email atau password salah';
            }
        } catch (PDOException $e) {
            error_log('Login error: ' . $e->getMessage());
            $error = 'Terjadi kesalahan sistem. Silakan coba lagi.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - CampusHub</title>
    <?php require_once __DIR__ . '/../includes/head-starter.php'; ?>
</head>
<body data-theme="light">
    <!-- Hero Section -->
    <section class="min-vh-100 d-flex align-items-center justify-center">
        <div class="container w-100 max-w-md">
            <div class="card p-6 max-w-md">
                <div class="text-center mb-6">
                    <span class="avatar text-3xl">CH</span>
                    <h1 class="mt-4 text-2xl font-bold">Selamat Datang</h1>
                    <p class="mt-2 text-muted">Portal Pembelajaran Mahasiswa</p>
                </div>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-error mt-4">
                        <i class='bx bx-error-circle me-2'></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <form action="" method="POST" class="mt-6">
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium mb-2">Email / NIM</label>
                        <input type="email" id="email" name="email" required 
                               class="w-full px-4 py-3 rounded border focus:outline-none focus:ring-2 focus:ring-primary"
                               placeholder="masukkan email atau NIM">
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium mb-2">Password</label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-4 py-3 rounded border focus:outline-none focus:ring-2 focus:ring-primary"
                               placeholder="masukkan password">
                    </div>
                    
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <input type="checkbox" id="remember" name="remember" class="h-4 w-4 rounded border focus:ring-primary">
                            <label for="remember" class="ml-2 text-sm">Remember Me</label>
                        </div>
                        <a href="/forgot-password.php" class="text-sm text-primary hover:underline">
                            Lupa Password?
                        </a>
                    </div>
                    
                    <button type="submit" name="login"
                            class="w-full btn btn-primary py-3 rounded font-medium">
                        Masuk
                    </button>
                </form>
                
                <p class="mt-6 text-center text-sm">
                    Belum memiliki akun? <a href="/register.php" class="font-medium text-primary hover:underline">Daftar sekarang</a>
                </p>
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