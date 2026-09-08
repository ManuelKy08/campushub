<?php
/**
 * CampusHub - Halaman Kalender
 * Menampilkan acara akademik dan deadline tugas
 * Fitur: View events, add events, filter by type
 */
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

require_login();
$user = get_user();
?>

<div class="padding-x4">
    <!-- Header -->
    <header class="mb-6">
        <div class="d-flex justify-between align-items-center">
            <h1 class="text-3xl font-bold">Kalender Akademik</h1>
        </div>
    </header>
    
    <!-- Filters -->
    <div class="card p-4 mb-4">
        <form action="/calendar.php" method="GET" class="row g-3">
            <div class="col-md-3">
                <select name="type" class="form-select">
                    <option value="">Tipe Semua</option>
                    <option value="deadline">Deadline</option>
                    <option value="lecture">Lecture</option>
                    <option value="event">Acara</option>
                    <option value="milestone">Milenial</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="month" class="form-select">
                    <option value="">Bulan Semua</option>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>">Bulan <?= $m ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="year" class="form-select">
                    <option value="">Tahun Semua</option>
                    <?php $currentYear = date('Y'); ?>
                    <option value="<?= $currentYear - 1 ?>"><?= $currentYear - 1 ?></option>
                    <option value="<?= $currentYear ?>" selected><?= $currentYear ?></option>
                    <option value="<?= $currentYear + 1 ?>"><?= $currentYear + 1 ?></option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
    
    <!-- Events List -->
    <div class="card">
        <div class="card-header px-6 py-3 border-b">
            <h4 class="text font-medium">Acara & Deadline</h4>
        </div>
        <div class="card-body px-6 py-4">
            <?php if (!empty($events)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-left">Judul</th>
                                <th class="text-left">Mata Kuliah</th>
                                <th class="text-center">Tipe</th>
                                <th class="text-center">Waktu</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $event): ?>
                                <tr>
                                    <td class="font-medium">
                                        <a href="/calendar-detail.php?id=<?= $event['id'] ?>">
                                            <?= htmlspecialchars($event['title']) ?>
                                        </a>
                                    </td>
                                    <td class="text-muted small">
                                        <?= htmlspecialchars($event['course_name'] ?? '') ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $event['type'] ?> small">
                                            <?= ucfirst($event['type']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($event['is_all_day']): ?>
                                            <span class="text-sm"><?= date('d F Y', strtotime($event['start_time'])) ?></span>
                                        <?php else: ?>
                                            <span class="text-sm">
                                                <?= date('d M H:i', strtotime($event['start_time'])) ?> -
                                                <?= $event['end_time'] ? date('H:i', strtotime($event['end_time'])) : '23:59' ?>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <a href="/calendar-detail.php?id=<?= $event['id'] ?>" class="btn btn-sm btn-outline">Detail</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-muted">
                    <i class='bx bx-calendar text-4xl mb-3'></i>
                    <p>Belum ada acara terdaftar</p>
                    <a href="/calendar-add.php" class="btn btn-primary mt-3">Tambah Acara</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/foot-starter.php'; ?>