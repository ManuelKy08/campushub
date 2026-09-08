<?php
/**
 * CampusHub - Assignments Detail
 * Menampilkan detail tugas beserta file upload
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

require_login();
$user = get_user();

// Ambil ID tugas dari URL
$assignment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($assignment_id <= 0) {
    header('Location: /assignments.php');
    exit;
}

try {
    global $pdo;
    $stmt = $pdo->prepare("SELECT a.*, c.name as course_name FROM assignments a LEFT JOIN courses c ON a.course_id = c.id WHERE a.id = ?");
    $stmt->execute([$assignment_id]);
    $assignment = $stmt->fetch();
    
    if (!$assignment) {
        header('Location: /assignments.php');
        exit;
    }
} catch (PDOException $e) {
    error_log('Assignment detail error: ' . $e->getMessage());
    $assignment = null;
}
?>

    <div class="padding-x4">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/dashboard.php">Beranda</a></li>
                <li class="breadcrumb-item"><a href="/assignments.php">Tugas</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($assignment['title'] ?? '') ?></li>
            </ol>
        </nav>
        
        <!-- Assignment Detail -->
        <div class="card mb-6">
            <div class="card-body px-6 py-4">
                <div class="d-flex justify-between align-items-start">
                    <div>
                        <h1 class="text-2xl font-bold"><?= htmlspecialchars($assignment['title'] ?? '') ?></h1>
                        <p class="text-muted small">Mata Kuliah: <?= htmlspecialchars($assignment['course_name'] ?? '') ?></p>
                    </div>
                    <div class="text-end">
                        <span class="badge badge-<?= $assignment['priority'] ?> small">
                            <?= ucfirst($assignment['priority']) ?>
                        </span>
                        <span class="badge badge-<?= ($assignment['status'] ?? 'pending') === 'completed' ? 'success' : 'warning' ?> small ms-2">
                            <?= ucfirst($assignment['status'] ?? 'pending') ?>
                        </span>
                    </div>
                </div>
                
                <!-- Details -->
                <div class="mt-6 pt-6 border-t">
                    <p class="text-muted small">Deadline: <strong><?= date('d F Y H:i', strtotime($assignment['deadline'])) ?></strong></p>
                    <?php if ($assignment['description']): ?>
                        <p class="mt-4 text-muted small"><?= htmlspecialchars($assignment['description']) ?></p>
                    <?php endif; ?>
                    
                    <!-- File Upload Section -->
                    <?php if ($assignment['file_path']): ?>
                        <div class="mt-6 pt-6 border-t">
                            <h5 class="text-sm font-medium mb-3">File Tugas</h5>
                            <a href="/download.php?file=<?= $assignment['file_path'] ?>" 
                               class="btn btn-primary">
                                <i class='bx bx-download me-2'></i>Download File
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="mt-6 pt-6 border-t">
                            <h5 class="text-sm font-medium mb-3">Kirim Tugas</h3>
                            <p class="text-muted small">Upload file tugas Anda di sini</p>
                            <form enctype="multipart/form-data">
                                <div class="mb-3">
                                    <input type="file" name="tugas_file" class="form-control">
                                    <input type="hidden" name="assignment_id" value="<?= $assignment_id ?>">
                                    <button type="submit" name="submit_tugas"
                                            class="btn btn-primary mt-3">Kirim Tugas</button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sidebar kanan untuk info tambahan -->
    <div class="card mt-4">
        <div class="card-body p-4">
            <h5 class="text-sm font-medium mb-3">Info Tugas</h5>
            <ul class="list-disc pl-4 text-sm text-muted">
                <li>Total Poin: 100</li>
                <li>Dosen: <?= htmlspecialchars($assignment['professor'] ?? 'Tidak diketahui') ?></li>
                <li>Diberikan: <?= date('d F Y', strtotime($assignment['created_at'] ?? date('now'))) ?></li>
            </ul>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/foot-starter.php'; ?>