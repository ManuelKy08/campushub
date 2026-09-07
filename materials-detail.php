<?php
/**
 * CampusHub - Materials Detail
 * Menampilkan detail materi beserta opsi download/lihat
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

require_login();
$user = get_user();

// Ambil ID materi dari URL
$material_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($material_id <= 0) {
    header('Location: /materials.php');
    exit;
}

try {
    global $pdo;
    $stmt = $pdo->prepare("SELECT m.*, c.name as course_name FROM materials m LEFT JOIN courses c ON m.course_id = c.id WHERE m.id = ?");
    $stmt->execute([$material_id]);
    $material = $stmt->fetch();
    
    if (!$material) {
        header('Location: /materials.php');
        exit;
    }
} catch (PDOException $e) {
    error_log('Material detail error: ' . $e->getMessage());
    $material = null;
}
?>

<!DOCTYPE>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($material['title'] ?? '') ?> - CampusHub</title>
    <?php require_once __DIR__ . '/../includes/head-starter.php'; ?>
</head>
<body data-theme="light">
    <!-- Header -->
    <?php require_once __DIR__ . '/../includes/header.php'; ?>
    
    <div class="padding-x4">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/dashboard.php">Beranda</a></li>
                <li class="breadcrumb-item"><a href="/materials.php">Materi</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($material['title'] ?? '') ?></li>
            </ol>
        </nav>
        
        <!-- Material Detail -->
        <div class="card mb-6">
            <div class="card-body px-6 py-4">
                <div class="d-flex justify-between align-items-start">
                    <div>
                        <h1 class="text-2xl font-bold"><?= htmlspecialchars($material['title'] ?? '') ?></h1>
                        <p class="text-muted small">Mata Kuliah: <?= htmlspecialchars($material['course_name'] ?? '') ?></p>
                    </div>
                    <div class="text-end">
                        <span class="badge badge-<?= $material['file_type'] ?> small">
                            <?= ucfirst($material['file_type']) ?>
                        </span>
                    </div>
                </div>
                
                <!-- Details -->
                <div class="mt-6 pt-6 border-t">
                    <?php if ($material['description']): ?>
                        <p class="text-muted small"><?= htmlspecialchars($material['description']) ?></p>
                    <?php endif; ?>
                    
                    <!-- File/Link Section -->
                    <div class="mt-6 pt-6 border-t">
                        <?php if ($material['file_path']): ?>
                            <div class="d-flex align-items-center mb-3">
                                <i class='bx bx-file text-primary me-3 fs-4'></i>
                                <div>
                                    <h5 class="mb-1">Download Materi</h5>
                                    <p class="mb-0 text-muted small">Klik di bawah untuk mengunduh file</p>
                                </div>
                            </div>
                            <a href="/download.php?file=<?= $material['file_path'] ?>" 
                               class="btn btn-primary">
                                <i class='bx bx-download me-2'></i>Unduh File
                            </a>
                        <?php elseif ($material['url_link']): ?>
                            <div class="d-flex align-items-center mb-3">
                                <i class='bx bx-link-external text-success me-3 fs-4'></i>
                                <div>
                                    <h5 class="mb-1">Lihat Online</h5>
                                    <p class="mb-0 text-muted small">Akses link sumber</p>
                                </div>
                            </div>
                            <a href="<?= htmlspecialchars($material['url_link']) ?>" target="_blank" 
                               class="btn btn-success">
                                <i class='bx bx-open-in-new me-2'></i>Buka Link
                            </a>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class='bx bx-error me-2'></i>
                                <span>Tidak ada file atau link tersedia untuk materi ini.</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php require_once __DIR__ . '/../includes/foot-starter.php'; ?>
</body>
</html>
EOF