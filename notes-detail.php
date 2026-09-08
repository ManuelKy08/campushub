<?php
/**
 * CampusHub - Notes Detail
 * Menampilkan detail catatan mahasiswa
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

require_login();
$user = get_user();

// Ambil ID catatan dari URL
$note_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($note_id <= 0) {
    header('Location: /notes.php');
    exit;
}

try {
    global $pdo;
    $stmt = $pdo->prepare("SELECT n.*, u.full_name as student_name, c.name as course_name FROM notes n 
                           LEFT JOIN users u ON n.user_id = u.id 
                           LEFT JOIN courses c ON n.course_id = c.id 
                           WHERE n.id = ?");
    $stmt->execute([$note_id]);
    $note = $stmt->fetch();
    
    if (!$note) {
        header('Location: /notes.php');
        exit;
    }
} catch (PDOException $e) {
    error_log('Note detail error: ' . $e->getMessage());
    $note = null;
}
?>

<!DOCTYPE>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($note['title'] ?? '') ?> - CampusHub</title>
    <?php require_once __DIR__ . '/includes/head-starter.php'; ?>
</head>
<body data-theme="light">
    <!-- Header -->
    <?php require_once __DIR__ . '/includes/header.php'; ?>
    
    <div class="padding-x4">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/dashboard.php">Beranda</a></li>
                <li class="breadcrumb-item"><a href="/notes.php">Catatan</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($note['title'] ?? '') ?></li>
            </ol>
        </nav>
        
        <!-- Note Detail -->
        <div class="card mb-6">
            <div class="card-body px-6 py-4">
                <div class="d-flex justify-between align-items-start">
                    <div>
                        <h1 class="text-2xl font-bold"><?= htmlspecialchars($note['title'] ?? '') ?></h1>
                        <p class="text-muted small">
                            <?= htmlspecialchars($note['student_name'] ?? 'Saya') ?> | 
                            <?= htmlspecialchars($note['course_name'] ?? '') ?>
                        </p>
                    </div>
                    <div class="text-end">
                        <?php if ($note['is_favorite']): ?>
                            <span class="badge badge-success">⭐ Favorit</span>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="mt-6 pt-6 border-t">
                    <?php if ($note['content']): ?>
                        <p class="text-muted small"><?= nl2br(htmlspecialchars($note['content'])) ?></p>
                    <?php else: ?>
                        <p class="text-muted small">Catatan kosong</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php require_once __DIR__ . '/includes/foot-starter.php'; ?>
</body>
</html>
EOF