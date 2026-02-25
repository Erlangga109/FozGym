<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../partials/header.php';

require_login();

$pdo = get_pdo();
$user = current_user();

// Determine whose schedule to show.
$effective_member_id = $user['id'];
$member_name = 'Saya'; // Default to "My"
if (is_trainer() && isset($_GET['member_id'])) {
    $effective_member_id = (int)$_GET['member_id'];
    
    $stmt_member = $pdo->prepare("SELECT name FROM members WHERE id = ?");
    $stmt_member->execute([$effective_member_id]);
    $member_data = $stmt_member->fetch();
    if ($member_data) {
        $member_name = htmlspecialchars($member_data['name']);
    }
}


// Ambil semua jadwal latihan untuk member ini
$stmt = $pdo->prepare("
    SELECT ws.*, COUNT(sd.id) as total_days 
    FROM workout_schedules ws 
    LEFT JOIN schedule_days sd ON ws.id = sd.schedule_id 
    WHERE ws.member_id = ? 
    GROUP BY ws.id 
    ORDER BY ws.created_at DESC
");
$stmt->execute([$effective_member_id]);
$schedules = $stmt->fetchAll();

// Fungsi untuk mendapatkan nama hari dalam bahasa Indonesia
function get_day_name($day_index) {
    $days = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
    return $days[$day_index] ?? 'Hari Tidak Valid';
}
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2>Jadwal Latihan <?= $member_name ?></h2>
            <p>Daftar jadwal latihan yang telah Anda buat atau dihasilkan oleh AI</p>
            <p class="text-muted"><small>Catatan: Jadwal manual bisa diedit langsung, sedangkan jadwal AI bisa dikonversi ke mode manual agar bisa diedit</small></p>
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="create.php?member_id=<?= $effective_member_id; ?>" class="btn btn-primary">Buat Jadwal Baru</a>
                <a href="generate_ai.php?member_id=<?= $effective_member_id; ?>" class="btn btn-success">Buat Jadwal Otomatis (AI)</a>
            </div>
            
            <?php if (flash('success')): ?>
                <div class="alert alert-success"><?php echo flash('success'); ?></div>
            <?php endif; ?>
            
            <?php if (flash('error')): ?>
                <div class="alert alert-danger"><?php echo flash('error'); ?></div>
            <?php endif; ?>
            
            <?php if (count($schedules) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nama Jadwal</th>
                                <th>Tipe</th>
                                <th>Target Otot</th>
                                <th>Tujuan Latihan</th>
                                <th>Hari</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedules as $schedule): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($schedule['schedule_name']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $schedule['schedule_type'] === 'ai_generated' ? 'bg-info' : 'bg-secondary'; ?>">
                                            <?php echo $schedule['schedule_type'] === 'ai_generated' ? 'AI Generated' : 'Manual'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($schedule['target_muscle_groups'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($schedule['training_goals'] ?? '-'); ?></td>
                                    <td><?php echo $schedule['total_days']; ?> hari</td>
                                    <td><?php echo date('d M Y', strtotime($schedule['created_at'])); ?></td>
                                    <td>
                                        <a href="view.php?id=<?= $schedule['id']; ?>&member_id=<?= $effective_member_id; ?>" class="btn btn-sm btn-info">Lihat</a>
                                        <?php if ($schedule['schedule_type'] === 'manual'): ?>
                                            <a href="edit.php?id=<?= $schedule['id']; ?>&member_id=<?= $effective_member_id; ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <?php else: ?>
                                            <a href="convert_to_manual.php?id=<?= $schedule['id']; ?>&member_id=<?= $effective_member_id; ?>" class="btn btn-sm btn-warning"
                                               title="Jadikan Editable"
                                               onclick="return confirm('Konversi jadwal AI ke manual agar bisa diedit?')">
                                                Edit
                                            </a>
                                        <?php endif; ?>
                                        <a href="delete.php?id=<?= $schedule['id']; ?>&member_id=<?= $effective_member_id; ?>" class="btn btn-sm btn-danger"
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    Belum ada jadwal latihan. <a href="create.php?member_id=<?= $effective_member_id; ?>">Buat jadwal pertama</a> atau
                    <a href="generate_ai.php?member_id=<?= $effective_member_id; ?>">gunakan fitur AI</a> untuk membuatkan jadwal otomatis.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>