<?php
/**
 * CampusHub - Admin: Assignments Edit
 * Mengedit tugas yang sudah ada
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_role('admin');

$assignment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($assignment_id <= 0) {
    header('Location: /admin/assignments.php');
    exit;
}

try {
    global $pdo;
    $stmt = $pdo->prepare("SELECT a.*, c.name as course_name FROM assignments a LEFT JOIN courses c ON a.course_id = c.id WHERE a.id = ?");
    $stmt->execute([$assignment_id]);
    $assignment = $stmt->fetch();
    
    if (!$assignment) {
        header('Location: /admin/assignments.php');
        exit;
    }
} catch (PDOException $e) {
    error_log('Assignment edit load error: ' . $e->getMessage());
    $assignment = null;
}
?>

<div class="padding-x4">
    <h1 class="text-3xl font-bold mb-4">Edit Tugas</h1>
    
    <?php if (!$assignment): ?>
        <div class="alert alert-error">Tugas tidak ditemukan</div>
    <?php else: ?>
        <form action="/admin/assignments-edit-process.php?id=<?= $assignment['id'] ?>" method="POST" class="max-w-2xl">
            <input type="hidden" name="original_title" value="<?= htmlspecialchars($assignment['title']) ?>">
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Judul</label>
                    <input type="text" name="assignment_title" value="<?= htmlspecialchars($assignment['title']) ?>"
                           class="w-full px-4 py-3 rounded border">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Mata Kuliah</label>
                    <select name="course_id" class="w-full px-4 py-3 rounded border">
                        <option value="<?= $assignment['course_id'] ?>"><?= htmlspecialchars($assignment['course_name'] ?? '') ?></option>
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
            <div>
                <label class="block text-sm font-medium mb-2">Deskripsi</label>
                <textarea name="assignment_description" rows="3" class="w-full px-4 py-3 rounded border"><?= htmlspecialchars($assignment['description'] ?? '') ?></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Deadline</label>
                    <input type="datetime-local" name="deadline" value="<?= htmlspecialchars($assignment['deadline'] ?? '') ?>"
                           class="w-full px-4 py-3 rounded border">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Prioritas</label>
                    <select name="priority" class="w-full px-4 py-3 rounded border">
                        <option value="low" <?= ($assignment['priority'] ?? 'medium') == 'low' ? 'selected' : '' ?>>Low</option>
                        <option value="medium" <?= ($assignment['priority'] ?? 'medium') == 'medium' ? 'selected' : '' ?>>Medium</option>
                        <option value="high" <?= ($assignment['priority'] ?? 'medium') == 'high' ? 'selected' : '' ?>>High</option>
                        <option value="urgent" <?= ($assignment['priority'] ?? 'medium') == 'urgent' ? 'selected' : '' ?>>Urgent</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-3 rounded border">
                        <option value="pending" <?= ($assignment['status'] ?? 'pending') == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="in_progress" <?= ($assignment['status'] ?? 'pending') == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="completed" <?= ($assignment['status'] ?? 'pending') == 'completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="late" <?= ($assignment['status'] ?? 'pending') == 'late' ? 'selected' : '' ?>>Late</option>
                    </select>
                </div>
            </div>
            <div class="text-end">
                <button type="submit" name="update_assignment"
                        class="w-auto btn btn-primary px-6">Simpan Perubahan</button>
                <a href="/admin/assignments.php" class="w-auto btn btn-outline px-6 ml-4">Batal</a>
            </div>
        </form>
    <?php endif; ?>
</div>