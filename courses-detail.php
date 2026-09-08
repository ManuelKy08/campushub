<?php
/**
 * CampusHub - Halaman Detail Mata Kuliah
 * Menampilkan detail mata kuliah beserta materi dan tugas
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

require_login();
$user = get_user();

// Ambil ID mata kuliah dari URL
$course_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($course_id <= 0) {
    header('Location: /courses.php');
    exit;
}

// Ambil data mata kuliah
try {
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM courses WHERE id = ?');
    $stmt->execute([$course_id]);
    $course = $stmt->fetch();
    
    if (!$course) {
        header('Location: /courses.php');
        exit;
    }
} catch (PDOException $e) {
    error_log('Course detail error: ' . $e->getMessage());
    $course = null;
}
?>

<!DOCTYPE>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($course['name'] ?? '') ?> - CampusHub</title>
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
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($course['name']) ?></li>
            </ol>
        </nav>
        
        <!-- Course Header -->
        <div class="card mb-6">
            <div class="card-body px-6 py-4">
                <div class="d-flex justify-between align-items-start">
                    <div>
                        <h1 class="text-2xl font-bold"><?= htmlspecialchars($course['name']) ?></h1>
                        <p class="text-muted small"><?= htmlspecialchars($course['code'] ?? '') ?></p>
                        <div class="mt-2">
                            <span class="badge badge-success me-2"><?= $course['credits'] ?> SKS</span>
                            <span class="badge badge-warning me-2">Semester <?= $course['semester'] ?></span>
                            <span class="badge badge-info">Dosen: <?= htmlspecialchars($course['professor'] ?? '-') ?></span>
                        </div>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">Diambil oleh: <?= /* student count */ '0' ?> mahasiswa</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabs: Materials & Assignments -->
        <div class="card">
            <ul class="nav nav-tabs" id="course-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="materials-tab" data-bs-toggle="tab" data-bs-target="#materials" type="tab">
                        <i class='bx bx-folder me-2'></i>Materi (<?= /* count */ 0 ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="assignments-tab" data-bs-toggle="tab" data-bs-target="#assignments" type="tab">
                        <i class='bx bx-briefcard me-2'></i>Tugas (<?= /* count */ 0 ?>)
                    </button>
                </li>
            </ul>
            <div class="tab-content" id="course-tabs-content">
                
                <!-- Materials Tab -->
                <div class="tab-pane fade show active" id="materials" role="tabpanel">
                    <div class="card-body px-6 py-4">
                        <?php if (!empty($materials)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-left">Judul</th>
                                            <th class="text-left">Tipe</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($materials as $material): ?>
                                            <tr>
                                                <td class="font-medium"><?= htmlspecialchars($material['title']) ?></td>
                                                <td class="text-muted small"><?= $material['file_type'] ?></td>
                                                <td class="text-end">
                                                    <?php if ($material['file_path']): ?>
                                                        <a href="/download.php?file=<?= $material['file_path'] ?>" class="btn btn-sm btn-outline">Download</a>
                                                    <?php elseif ($material['url_link']): ?>
                                                        <a href="<?= htmlspecialchars($material['url_link']) ?>" target="_blank" class="btn btn-sm btn-outline">Lihat</a>
                                                    <?php else: ?>
                                                        <span class="text-xs">Tidak ada file</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-8 text-muted">
                                <i class='bx bx-folder text-4xl mb-3'></i>
                                <p>Belum ada materi untuk mata kuliah ini</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Assignments Tab -->
                <div class="tab-pane fade" id="assignments" role="tabpanel">
                    <div class="card-body px-6 py-4">
                        <?php if (!empty($assignments)): ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-left">Judul</th>
                                            <th class="text-left">Deadline</th>
                                            <th class="text-left">Prioritas</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($assignments as $assignment): ?>
                                            <tr>
                                                <td class="font-medium">
                                                    <a href="/assignments-detail.php?id=<?= $assignment['id'] ?>">
                                                        <?= htmlspecialchars($assignment['title']) ?>
                                                    </a>
                                                </td>
                                                <td class="text-muted small"><?= date('d M Y', strtotime($assignment['deadline'])) ?></td>
                                                <td>
                                                    <span class="badge badge-<?= $assignment['priority'] ?>"><?= ucfirst($assignment['priority']) ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-<?= $assignment['status'] === 'completed' ? 'success' : 'warning' ?>">
                                                        <?= ucfirst($assignment['status']) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-8 text-muted">
                                <i class='bx bx-briefcard text-4xl mb-3'></i>
                                <p>Belum ada tugas untuk mata kuliah ini</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/foot-starter.php'; ?>
</body>
</html>
EOF