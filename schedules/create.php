<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../partials/header.php';

require_login();

$pdo = get_pdo();
$user = current_user();

// Determine whose schedule is being created
$member_id_for_schedule = $user['id'];
if (is_trainer() && isset($_GET['member_id'])) {
    $member_id_for_schedule = (int)$_GET['member_id'];
}

// Get the member's name if a trainer is creating the schedule
$member_name = '';
if (is_trainer() && $member_id_for_schedule !== $user['id']) {
    $stmt_member = $pdo->prepare("SELECT name FROM members WHERE id = ?");
    $stmt_member->execute([$member_id_for_schedule]);
    $member_data = $stmt_member->fetch();
    if ($member_data) {
        $member_name = htmlspecialchars($member_data['name']);
    }
}


// Proses form jika disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // The member ID from the hidden field is now the definitive one on POST
    $submit_member_id = (int)($_POST['member_id_for_schedule'] ?? $user['id']);

    $schedule_name = $_POST['schedule_name'] ?? '';
    $target_muscles = $_POST['target_muscles'] ?? '';
    $training_goals = $_POST['training_goals'] ?? '';
    $difficulty_level = $_POST['difficulty_level'] ?? 'beginner';
    
    // Validasi input
    if (empty($schedule_name)) {
        flash('error', 'Nama jadwal harus diisi');
    } else {
        try {
            // Simpan jadwal utama
            $stmt = $pdo->prepare("
                INSERT INTO workout_schedules 
                (member_id, schedule_name, schedule_type, target_muscle_groups, training_goals, difficulty_level) 
                VALUES (?, ?, 'manual', ?, ?, ?)
            ");
            $stmt->execute([$submit_member_id, $schedule_name, $target_muscles, $training_goals, $difficulty_level]);
            $schedule_id = $pdo->lastInsertId();
            
            // Buat 7 hari latihan kosong
            $days = [
                ['day_of_week' => 0, 'day_name' => 'Minggu'],
                ['day_of_week' => 1, 'day_name' => 'Senin'],
                ['day_of_week' => 2, 'day_name' => 'Selasa'],
                ['day_of_week' => 3, 'day_name' => 'Rabu'],
                ['day_of_week' => 4, 'day_name' => 'Kamis'],
                ['day_of_week' => 5, 'day_name' => 'Jumat'],
                ['day_of_week' => 6, 'day_name' => 'Sabtu']
            ];
            
            $stmt = $pdo->prepare("
                INSERT INTO schedule_days (schedule_id, day_of_week, day_name, exercises) 
                VALUES (?, ?, ?, '[]')
            ");
            
            foreach ($days as $day) {
                $stmt->execute([$schedule_id, $day['day_of_week'], $day['day_name']]);
            }
            
            flash('success', 'Jadwal latihan berhasil dibuat. Silakan isi detail latihan.');
            // Redirect to the edit page for the new schedule
            header('Location: edit.php?id=' . $schedule_id . '&member_id=' . $submit_member_id);
            exit;
        } catch (PDOException $e) {
            flash('error', 'Gagal menyimpan jadwal: ' . $e->getMessage());
        }
    }
}
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <h2>Buat Jadwal Latihan Baru</h2>
            <?php if ($member_name): ?>
                <p class="lead">Untuk Member: <strong><?= $member_name; ?></strong></p>
            <?php else: ?>
                <p>Buat jadwal latihan secara manual sesuai kebutuhan Anda</p>
            <?php endif; ?>
            
            <?php if (flash('error')): ?>
                <div class="alert alert-danger"><?php echo flash('error'); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <input type="hidden" name="member_id_for_schedule" value="<?= $member_id_for_schedule; ?>">
                <div class="mb-3">
                    <label for="schedule_name" class="form-label">Nama Jadwal</label>
                    <input type="text" class="form-control" id="schedule_name" name="schedule_name" 
                           value="<?php echo htmlspecialchars($_POST['schedule_name'] ?? ''); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="target_muscles" class="form-label">Target Otot</label>
                    <input type="text" class="form-control" id="target_muscles" name="target_muscles" 
                           value="<?php echo htmlspecialchars($_POST['target_muscles'] ?? ''); ?>" 
                           placeholder="Contoh: Dada, Bahu, Punggung">
                </div>
                
                <div class="mb-3">
                    <label for="training_goals" class="form-label">Tujuan Latihan</label>
                    <textarea class="form-control" id="training_goals" name="training_goals" rows="3"
                              placeholder="Contoh: Membentuk otot, menurunkan berat badan, meningkatkan kekuatan"><?php echo htmlspecialchars($_POST['training_goals'] ?? ''); ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="difficulty_level" class="form-label">Tingkat Kesulitan</label>
                    <select class="form-control" id="difficulty_level" name="difficulty_level">
                        <option value="beginner" <?php echo (($_POST['difficulty_level'] ?? '') === 'beginner') ? 'selected' : ''; ?>>Pemula</option>
                        <option value="intermediate" <?php echo (($_POST['difficulty_level'] ?? '') === 'intermediate') ? 'selected' : ''; ?>>Menengah</option>
                        <option value="advanced" <?php echo (($_POST['difficulty_level'] ?? '') === 'advanced') ? 'selected' : ''; ?>>Lanjutan</option>
                    </select>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Simpan dan Lanjutkan</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>