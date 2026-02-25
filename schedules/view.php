<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../partials/header.php';

require_login();

$pdo = get_pdo();
$user = current_user();
// Determine the effective member_id for whom the schedule is being viewed
// If member_id is passed via GET (e.g., by a trainer), use that. Otherwise, use the logged-in user's ID.
$effective_member_id = (int)($_GET['member_id'] ?? $user['id']);

$schedule_id = (int)($_GET['id'] ?? 0);

// Ambil jadwal berdasarkan ID dan member (tanpa JOIN untuk menghindari masalah foreign key)
$stmt = $pdo->prepare("
    SELECT ws.*
    FROM workout_schedules ws
    WHERE ws.id = ? AND ws.member_id = ?
");
$stmt->execute([$schedule_id, $effective_member_id]);
$schedule = $stmt->fetch();

if (!$schedule) {
    flash('error', 'Jadwal tidak ditemukan atau Anda tidak memiliki akses');
    header('Location: index.php?member_id=' . $effective_member_id);
    exit;
}

// Ambil detail hari-hari dalam jadwal
$stmt = $pdo->prepare("
    SELECT * FROM schedule_days 
    WHERE schedule_id = ? 
    ORDER BY day_of_week
");
$stmt->execute([$schedule_id]);
$schedule_days = $stmt->fetchAll();

// Fungsi untuk mendapatkan nama hari dalam bahasa Indonesia
function get_day_name($day_index) {
    $days = [
        0 => 'Minggu',
        1 => 'Senin', 
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu'
    ];
    return $days[$day_index] ?? 'Hari Tidak Valid';
}
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2>Detail Jadwal: <?php echo htmlspecialchars($schedule['schedule_name']); ?></h2>
            
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Detail Jadwal</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Nama Jadwal:</strong></td>
                            <td><?php echo htmlspecialchars($schedule['schedule_name']); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Tipe Jadwal:</strong></td>
                            <td>
                                <span class="badge <?php echo $schedule['schedule_type'] === 'ai_generated' ? 'bg-info' : 'bg-secondary'; ?>">
                                    <?php echo $schedule['schedule_type'] === 'ai_generated' ? 'AI Generated' : 'Manual'; ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Target Otot:</strong></td>
                            <td><?php echo htmlspecialchars($schedule['target_muscle_groups'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Tujuan Latihan:</strong></td>
                            <td><?php echo htmlspecialchars($schedule['training_goals'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Tingkat Kesulitan:</strong></td>
                            <td><?php echo ucfirst($schedule['difficulty_level'] ?? 'beginner'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Dibuat:</strong></td>
                            <td><?php echo date('d M Y H:i', strtotime($schedule['created_at'])); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <h3>Detail Harian</h3>
            <div class="row">
                <?php foreach ($schedule_days as $day): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><?php echo get_day_name($day['day_of_week']) . ' (' . $day['day_name'] . ')'; ?></h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $exercises = json_decode($day['exercises'], true);
                                if ($exercises && count($exercises) > 0):
                                ?>
                                    <ul class="list-group list-group-flush">
                                        <?php foreach ($exercises as $exercise): ?>
                                            <li class="list-group-item">
                                                <strong><?php echo htmlspecialchars($exercise['name'] ?? 'Latihan Tanpa Nama'); ?></strong><br>
                                                <small class="text-muted">
                                                    Sets: <?php echo $exercise['sets'] ?? 0; ?> |
                                                    Reps: <?php echo $exercise['reps'] ?? 0; ?> |
                                                    Rest: <?php echo $exercise['rest'] ?? 0; ?> detik
                                                </small>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-muted">Belum ada latihan untuk hari ini</p>
                                <?php endif; ?>

                                <?php if ($schedule['schedule_type'] === 'manual' || (is_trainer() && $schedule['schedule_type'] === 'ai_generated')): ?>
                                    <div class="mt-2">
                                        <a href="edit_day.php?schedule_id=<?php echo $schedule['id']; ?>&day_id=<?php echo $day['id']; ?>&member_id=<?= $effective_member_id; ?>"
                                           class="btn btn-sm btn-outline-primary">Edit Hari Ini</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="mt-3">
                <a href="index.php?member_id=<?= $effective_member_id; ?>" class="btn btn-secondary">Kembali ke Daftar Jadwal</a>
                <?php if ($schedule['schedule_type'] === 'manual'): ?>
                    <a href="edit.php?id=<?php echo $schedule['id']; ?>&member_id=<?= $effective_member_id; ?>" class="btn btn-warning">Edit Jadwal</a>
                <?php else: ?>
                    <a href="convert_to_manual.php?id=<?php echo $schedule['id']; ?>&member_id=<?= $effective_member_id; ?>" class="btn btn-warning"
                       onclick="return confirm('Apakah Anda yakin ingin mengubah jadwal ini menjadi mode manual? Jadwal akan bisa diedit setelah ini.');">Jadikan Editable</a>
                <?php endif; ?>
                <a href="delete.php?id=<?php echo $schedule['id']; ?>&member_id=<?= $effective_member_id; ?>" class="btn btn-danger"
                   onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">Hapus Jadwal</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
