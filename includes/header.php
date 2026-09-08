<?php
/**
 * CampusHub - Header Halaman
 * Menyertakan meta tag, CSS, dan title page
 */
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title ?? 'CampusHub - Portal Mahasiswa'); ?></title>
    
    <!-- Fonts Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons Boxicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/boxicons.min.css">
    
    <!-- CSS Custom -->
    <link rel="stylesheet" href="/assets/css/global.css">
    <link rel="stylesheet" href="/assets/css/<?php echo htmlspecialchars($body_class ?? 'dashboard'); ?>.css">
    
    <style>
        :root {
            --bg-light: #f8f9fa;
            --bg-dark: #111827;
            --surface: #ffffff;
            --surface-dark: #1f2937;
            --text-light: #212529;
            --text-dark: #f9fafb;
            --muted: #6b7280;
            --border: #e5e7eb;
            --border-dark: #374151;
            --accent: #3b82f6;
            --accent-dark: #60a5fa;
            --success: #10b981;
            --warning: #f59e0b;
            --error: #ef4444;
        }
        
        [data-theme="dark"] {
            --bg-light: #111827;
            --bg-dark: #1f2937;
            --surface: #1f2937;
            --text-light: #f9fafb;
            --text-dark: #111827;
            --muted: #9ca3af;
            --border: #374151;
            --accent: #60a5fa;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            background-color: var(--bg-light);
            color: var(--text-light);
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        [data-theme="dark"] body {
            background-color: var(--bg-dark);
        }
        
        a {
            text-decoration: none;
            color: inherit;
            transition: color 0.2s ease;
        }
        
        ul {
            list-style: none;
        }
        
        img {
            max-width: 100%;
            height: auto;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }
        
        .btn-primary {
            background-color: var(--accent);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--accent-dark);
            transform: translateY(-1px);
        }
        
        .btn-outline {
            background: transparent;
            color: var(--accent);
            border: 1px solid var(--accent);
        }
        
        .btn-outline:hover {
            background-color: var(--accent);
            color: white;
        }
        
        .btn-small {
            padding: 6px 12px;
            font-size: 0.75rem;
        }
        
        .alert {
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 0.875rem;
        }
        
        .alert-success {
            background-color: #d1fae5;
            color: #059669;
            border: 1px solid #a3e635;
        }
        
        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            background-color: var(--accent);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-success {
            background-color: #d1fae5;
            color: #059669;
        }
        
        .badge-warning {
            background-color: #f59e0b;
            color: #1e293b;
        }
        
        .badge-error {
            background-color: #ef4444;
            color: white;
        }
    </style>
</head>
<body data-theme="light">
    <!-- Navbar Fixed -->
    <nav class="navbar">
        <div class="container">
            <a href="/index.php" class="navbar-logo">
                <span class="avatar">CH</span>
                <span class="navbar-text hidden-md-up">CampusHub</span>
            </a>
            
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="/index.php" class="nav-link">Beranda</a>
                </li>
                <li class="nav-item">
                    <a href="/courses.php" class="nav-link">Mata Kuliah</a>
                </li>
                <li class="nav-item">
                    <a href="/assignments.php" class="nav-link">Tugas</a>
                </li>
            </ul>
            
            <ul class="navbar-actions">
                <?php if (is_logged_in()): ?>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" aria-haspopup="true" aria-expanded="false">
                            <span class="avatar"><?= htmlspecialchars($_SESSION['user']['name'] ?? 'User') ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="/profile.php">Profil</a></li>
                            <li><a href="/settings.php">Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a href="/logout.php">Keluar</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a href="/login.php" class="btn btn-outline">Masuk</a>
                    </li>
                    <li class="nav-item">
                        <a href="/register.php" class="btn btn-secondary">Daftar</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    
    <!-- Mobile Navbar -->
    <nav class="mobile-nav">
        <a href="/index.php" class="mobile-logo">CH</a>
        <a href="/courses.php" class="mobile-link">Mata Kuliah</a>
        <a href="/assignments.php" class="mobile-link">Tugas</a>
        <a href="/calendar.php" class="mobile-link">Kalender</a>
        <a href="/profile.php" class="mobile-link">Profil</a>
    </nav>
    
    <main class="main-content">