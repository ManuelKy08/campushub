<?php
/**
 * CampusHub - Sidebar Navigasi
 * Menampilkan menu navigasi untuk desktop dan mobile
 * Hanya ditampilkan pada halaman yang membutuhkan sidebar
 */
if (!defined('IN_CAMPUSHUB')) {
    die('Akses dilarang');
}
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <a href="/dashboard.php" class="sidebar-brand">
            <span class="avatar">CH</span>
            <span>CampusHub</span>
        </a>
        <button class="sidebar-toggle" aria-label="Tutup sidebar">
            <i class='bx bx-x'></i>
        </button>
    </div>
    
    <nav class="sidebar-nav">
        <ul>
            <li class="nav-item <?= $page === 'dashboard' ? 'active' : '' ?>">
                <a href="/dashboard.php" class="nav-link">
                    <i class='bx bx-grid-alt nav-icon'></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item <?= $page === 'courses' ? 'active' : '' ?>">
                <a href="/courses.php" class="nav-link">
                    <i class='bx bx-book nav-icon'></i>
                    <span>Mata Kuliah</span>
                </a>
            </li>
            <li class="nav-item <?= $page === 'assignments' ? 'active' : '' ?>">
                <a href="/assignments.php" class="nav-link">
                    <i class='bx bx-briefcard nav-icon'></i>
                    <span>Tugas</span>
                </a>
            </li>
            <li class="nav-item <?= $page === 'calendar' ? 'active' : '' ?>">
                <a href="/calendar.php" class="nav-link">
                    <i class='bx bx-calendar nav-icon'></i>
                    <span>Kalender</span>
                </a>
            </li>
            <li class="nav-item <?= $page === 'notes' ? 'active' : '' ?>">
                <a href="/notes.php" class="nav-link">
                    <i class='bx bx-sticky nav-icon'></i>
                    <span>Catatan</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <div class="sidebar-footer">
        <a href="/logout.php" class="btn btn-block btn-outline w-100">
            <i class='bx bx-log-out me-2'></i>
            Keluar
        </a>
    </div>
</aside>
EOF