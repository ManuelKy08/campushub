<?php
/**
 * CampusHub - Dashboard Halaman Utama
 * Menampilkan widget-widget ringkasan: kursus, tugas, materi, progres
 * Hanya diakses jika sudah login
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

// Cek otentikasi
require_login();
$user = get_user();
$role = $user['role'];
?>

<div class="padding-x4">
    <!-- Header Dashboard -->
    <header class="mb-6">
        <h1 class="text-3xl font-bold">Dashboard</h1>
        <p class="text-muted">Selamat datang kembali, <?= htmlspecialchars($user['name']) ?></p>
    </header>
    
    <!-- Widget Grid -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
        
        <!-- Widget: Courses -->
        <div class="card h-100">
            <div class="card-header px-6 py-4 border-b">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-primary flex items-center justify-center">
                        <i class='bx bx-book me-2'></i>
                        <span class="text-xs font-bold text-white">12</span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">Mata Kuliah</p>
                        <p class="text-xs text-muted">12 kursus terdaftar</p>
                    </div>
                </div>
            </div>
            <div class="card-body px-6 py-4">
                <a href="/courses.php" class="text-primary text-sm hover:underline">
                    Lihat Semua >>
                </a>
            </div>
        </div>
        
        <!-- Widget: Assignments -->
        <div class="card h-100">
            <div class="card-header px-6 py-4 border-b">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-warning flex items-center justify-center">
                        <i class='bx bx-briefcard me-2'></i>
                        <span class="text-xs font-bold text-white">8</span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">Tugas</p>
                        <p class="text-xs text-muted">8 tugak aktif</p>
                    </div>
                </div>
            </div>
            <div class="card-body px-6 py-4">
                <a href="/assignments.php" class="text-warning text-sm hover:underline">
                    Lihat Semua >>
                </a>
            </div>
        </div>
        
        <!-- Widget: Materials -->
        <div class="card h-100">
            <div class="card-header px-6 py-4 border-b">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-success flex items-center justify-center">
                        <i class='bx bx-folder me-2'></i>
                        <span class="text-xs font-bold text-white">24</span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">Materi</p>
                        <p class="text-xs text-muted">24 materi tersedia</p>
                    </div>
                </div>
            </div>
            <div class="card-body px-6 py-4">
                <a href="/materials.php" class="text-success text-sm hover:underline">
                    Lihat Semua >>
                </a>
            </div>
        </div>
        
        <!-- Widget: Progress -->
        <div class="card h-100">
            <div class="card-header px-6 py-4 border-b">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-info flex items-center justify-center">
                        <i class='bx bx-bar-chart-2 me-2'></i>
                        <span class="text-xs font-bold text-white">75%</span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">Progress</p>
                        <p class="text-xs text-muted">75% menyelesaikan tugas</p>
                    </div>
                </div>
            </div>
            <div class="card-body px-6 py-4">
                <div class="h-4 w-full bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-primary rounded-full" style="width: 75%"></div>
                </div>
                <p class="text-xs text-muted mt-2">Tahap pemerolehan kredit</p>
            </div>
        </div>
        
    </div>
    
    <!-- Quick Stats Cards (lg:grid-cols-4) -->
    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
        
        <!-- Today's Deadline -->
        <div class="card">
            <div class="card-header px-6 py-3 border-b">
                <h4 class="text-sm font-medium">Tugas Hari Ini</h4>
            </div>
            <div class="card-body px-6 py-4">
                <div class="flex justify-between">
                    <span class="text-sm">Projek Akhir Web</span>
                    <span class="text-xs text-warning">Due Today</span>
                </div>
                <small class="text-muted d-block">15 September 2026</small>
            </div>
        </div>
        
        <!-- Unread Notifications -->
        <div class="card">
            <div class="card-header px-6 py-3 border-b">
                <h4 class="text-sm font-medium">Notifikasi</h4>
            </div>
            <div class="card-body px-6 py-4">
                <span class="text-sm">3 notifikasi baru</span>
            </div>
        </div>
        
        <!-- Upcoming Events -->
        <div class="card">
            <div class="card-header px-6 py-3 border-b">
                <h4 class="text-sm font-medium">Acara Mendatang</h4>
            </div>
            <div class="card-body px-6 py-4">
                <span class="text-sm">HUT Kampus - 20 Sept</span>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="card">
            <div class="card-header px-6 py-3 border-b">
                <h4 class="text-sm font-medium">Aktivitas Terbaru</h4>
            </div>
            <div class="card-body px-6 py-4">
                <span class="text-sm">Aktivitas terakhir</span>
            </div>
        </div>
        
    </div>
</div>

<?php require_once __DIR__ . '/../includes/foot-starter.php'; ?>