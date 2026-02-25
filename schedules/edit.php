<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../partials/header.php';

require_login();

$pdo = get_pdo();
$user = current_user();

// Determine whose schedule is being edited, using $_REQUEST to handle both GET and POST
$effective_member_id = $user['id'];
if (is_trainer() && isset($_REQUEST['member_id'])) {
    $effective_member_id = (int)$_REQUEST['member_id'];
}

$schedule_id = (int)($_REQUEST['id'] ?? 0);

// Ambil jadwal berdasarkan ID dan member yang efektif
$stmt = $pdo->prepare("
    SELECT *
    FROM workout_schedules
    WHERE id = ? AND member_id = ? AND schedule_type = 'manual'
");
$stmt->execute([$schedule_id, $effective_member_id]);
$schedule = $stmt->fetch();

if (!$schedule) {
    flash('error', 'Jadwal tidak ditemukan atau Anda tidak memiliki akses');
    header('Location: index.php?member_id=' . $effective_member_id);
    exit;
}

// Proses form jika disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $schedule_name = $_POST['schedule_name'] ?? '';
    $target_muscles = $_POST['target_muscles'] ?? '';
    $training_goals = $_POST['training_goals'] ?? '';
    $difficulty_level = $_POST['difficulty_level'] ?? 'beginner';
    
    // Validasi input
    if (empty($schedule_name)) {
        flash('error', 'Nama jadwal harus diisi');
    } else {
        try {
            // Update jadwal utama
            $stmt = $pdo->prepare("
                UPDATE workout_schedules 
                SET schedule_name = ?, target_muscle_groups = ?, training_goals = ?, difficulty_level = ?
                WHERE id = ? AND member_id = ?
            ");
            $stmt->execute([$schedule_name, $target_muscles, $training_goals, $difficulty_level, $schedule_id, $effective_member_id]);
            
            flash('success', 'Jadwal latihan berhasil diperbarui');
            header('Location: view.php?id=' . $schedule_id . '&member_id=' . $effective_member_id);
            exit;
        } catch (PDOException $e) {
            flash('error', 'Gagal memperbarui jadwal: ' . $e->getMessage());
        }
    }
}

// Jika edit form di-submit, gunakan data dari POST, jika tidak ambil dari database
$schedule_name = $_POST['schedule_name'] ?? $schedule['schedule_name'];
$target_muscles = $_POST['target_muscles'] ?? $schedule['target_muscle_groups'];
$training_goals = $_POST['training_goals'] ?? $schedule['training_goals'];
$difficulty_level = $_POST['difficulty_level'] ?? $schedule['difficulty_level'];
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <h2>Edit Jadwal Latihan</h2>
            <p>Ubah detail jadwal latihan Anda</p>
            
            <?php if (flash('error')): ?>
                <div class="alert alert-danger"><?php echo flash('error'); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?= $schedule_id; ?>">
                <input type="hidden" name="member_id" value="<?= $effective_member_id; ?>">
                <div class="mb-3">
                    <label for="schedule_name" class="form-label">Nama Jadwal</label>
                    <input type="text" class="form-control" id="schedule_name" name="schedule_name" 
                           value="<?php echo htmlspecialchars($schedule_name); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="target_muscles" class="form-label">Target Otot</label>
                    <input type="text" class="form-control" id="target_muscles" name="target_muscles" 
                           value="<?php echo htmlspecialchars($target_muscles); ?>" 
                           placeholder="Contoh: Dada, Bahu, Punggung">
                </div>
                
                <div class="mb-3">
                    <label for="training_goals" class="form-label">Tujuan Latihan</label>
                    <textarea class="form-control" id="training_goals" name="training_goals" rows="3"
                              placeholder="Contoh: Membentuk otot, menurunkan berat badan, meningkatkan kekuatan"><?php echo htmlspecialchars($training_goals); ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="difficulty_level" class="form-label">Tingkat Kesulitan</label>
                    <select class="form-control" id="difficulty_level" name="difficulty_level">
                        <option value="beginner" <?php echo ($difficulty_level === 'beginner') ? 'selected' : ''; ?>>Pemula</option>
                        <option value="intermediate" <?php echo ($difficulty_level === 'intermediate') ? 'selected' : ''; ?>>Menengah</option>
                        <option value="advanced" <?php echo ($difficulty_level === 'advanced') ? 'selected' : ''; ?>>Lanjutan</option>
                    </select>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Perbarui Jadwal</button>
                    <a href="view.php?id=<?php echo $schedule['id']; ?>&member_id=<?= $effective_member_id; ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>