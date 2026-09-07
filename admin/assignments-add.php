<?php
/**
 * CampusHub - Admin: Assignments Add
 * Menambah tugas baru
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_assignment'])) {
    $title = trim($_POST['assignment_title'] ?? '');
    $description = trim($_POST['assignment_description'] ?? '');
    $course_id = intval($_POST['course_id'] ?? 0);
    $deadline = trim($_POST['deadline'] ?? '');
    $priority = $_POST['priority'] ?? 'medium';
    $status = $_POST['status'] ?? 'pending';
    
    $errors = [];
    
    if (empty($title)) {
        $errors[] = 'Judul tugas wajib diisi';
    }
    if (empty($course_id) || $course_id <= 0) {
        $errors[] = 'Mata kuliah wajib dipilih';
    }
    if (empty($deadline)) {
        $errors[] = 'Deadline wajib diisi';
    } else {
        // Validasi format tanggal
        if (!strtotime($deadline)) {
            $errors[] = 'Format deadline tidak valid';
        }
    }
    
    $priority_options = ['low', 'medium', 'high', 'urgent'];
    if (!in_array($priority, $priority_options)) {
        $errors[] = 'Prioritas tidak valid';
    }
    
    $status_options = ['pending', 'in_progress', 'completed', 'late'];
    if (!in_array($status, $status_options)) {
        $errors[] = 'Status tidak valid';
    }
    
    if (empty($errors)) {
        try {
            global $pdo;
            $stmt = $pdo->prepare(
                'INSERT INTO assignments (course_id, title, description, deadline, priority, status) 
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            
            $result = $stmt->execute([
                $course_id,
                $title,
                $description,
                $deadline,
                $priority,
                $status
            ]);
            
            if ($result) {
                // Buat notifikasi untuk user terkait
                header('Location: /admin/assignments.php');
                exit;
            }
        } catch (PDOException $e) {
            error_log('Add assignment error: ' . $e->getMessage());
            $errors[] = 'Gagal menambah tugas';
        }
    }
    
    if (!empty($errors)) {
        echo '<div class="alert alert-error mb-4">
                <i class="bx bx-error-circle me-2"></i>';
        foreach ($errors as $error) {
            echo '<p>' . htmlspecialchars($error) . '</p>';
        }
        echo '</div>';
    }
}
?>

<div class="padding-x4">
    <h1 class="text-3xl font-bold mb-4">Tambah Tugas Baru</h1>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error mb-4">
            <?php foreach ($errors as $error): ?><p><?= htmlspecialchars($error) ?></p><?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form action="" method="POST" class="max-w-2xl">
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-2">Judul Tugas</label>
                <input type="text" name="assignment_title" required
                       class="w-full px-4 py-3 rounded border">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Mata Kuliah</label>
                <select name="course_id" required class="w-full px-4 py-3 rounded border">
                    <option value="">-- Pilih Mata Kuliah --</option>
                    <?php try {
                        $cstmt = $pdo->prepare('SELECT id, name FROM courses ORDER BY name');
                        $cstmt->execute();
                        $courses = $cstmt->fetchAll();
                        foreach ($courses as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                        <?php endforeach; ?>
                    } catch (Exception $e) {} ?>
                </select>
            </div>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Deskripsi</label>
            <textarea name="assignment_description" rows="3" class="w-full px-4 py-3 rounded border"></textarea>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-2">Deadline</label>
                <input type="datetime-local" name="deadline" required
                       class="w-full px-4 py-3 rounded border">
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Prioritas</label>
                <select name="priority" required class="w-full px-4 py-3 rounded border">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-2">Status</label>
                <select name="status" required class="w-full px-4 py-3 rounded border">
                    <option value="pending" selected>Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="late">Late</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-2">Course Reference</label>
                <input type="text" class="w-full px-4 py-3 rounded border" readonly
                       value="<?= /* course name */ '' ?>">
            </div>
        </div>
        <button type="submit" name="add_assignment"
                class="w-full btn btn-primary py-3 rounded font-medium mt-4">
            Simpan Tugas
        </button>
    </form>
</div>