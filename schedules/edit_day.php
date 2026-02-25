<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../partials/header.php';

require_login();

$pdo = get_pdo();
$user = current_user();

// Determine whose schedule day is being edited
$effective_member_id = $user['id'];
if (is_trainer() && isset($_GET['member_id'])) {
    $effective_member_id = (int)$_GET['member_id'];
}

$schedule_id = (int)($_GET['schedule_id'] ?? 0);
$day_id = (int)($_GET['day_id'] ?? 0);

// Ambil jadwal berdasarkan ID dan member yang efektif untuk verifikasi
if (is_trainer()) {
    $stmt = $pdo->prepare("
        SELECT ws.* 
        FROM workout_schedules ws 
        WHERE ws.id = ? AND ws.member_id = ?
    ");
    $stmt->execute([$schedule_id, $effective_member_id]);
} else {
    $stmt = $pdo->prepare("
        SELECT ws.* 
        FROM workout_schedules ws 
        WHERE ws.id = ? AND ws.member_id = ? AND ws.schedule_type = 'manual'
    ");
    $stmt->execute([$schedule_id, $effective_member_id]);
}
$schedule = $stmt->fetch();

if (!$schedule) {
    flash('error', 'Jadwal tidak ditemukan atau Anda tidak memiliki akses');
    header('Location: index.php?member_id=' . $effective_member_id);
    exit;
}

// Ambil detail hari
$stmt = $pdo->prepare("
    SELECT * FROM schedule_days 
    WHERE id = ? AND schedule_id = ?
");
$stmt->execute([$day_id, $schedule_id]);
$day = $stmt->fetch();

if (!$day) {
    flash('error', 'Hari tidak ditemukan dalam jadwal ini');
    header('Location: view.php?id=' . $schedule_id . '&member_id=' . $effective_member_id);
    exit;
}

// Proses form jika disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exercises = [];
    $exercise_names = $_POST['exercise_name'] ?? [];
    $sets = $_POST['sets'] ?? [];
    $reps = $_POST['reps'] ?? [];
    $rest = $_POST['rest'] ?? [];
    
    // Gabungkan data latihan
    for ($i = 0; $i < count($exercise_names); $i++) {
        if (!empty(trim($exercise_names[$i]))) {
            $exercises[] = [
                'name' => trim($exercise_names[$i]),
                'sets' => (int)($sets[$i] ?? 0),
                'reps' => (int)($reps[$i] ?? 0),
                'rest' => (int)($rest[$i] ?? 0)
            ];
        }
    }
    
    try {
        // Update hari dengan latihan yang baru
        $stmt = $pdo->prepare("UPDATE schedule_days SET exercises = ? WHERE id = ?");
        $stmt->execute([json_encode($exercises), $day_id]);
        
        flash('success', 'Latihan hari ' . $day['day_name'] . ' berhasil diperbarui');
        header('Location: view.php?id=' . $schedule_id . '&member_id=' . $effective_member_id);
        exit;
    } catch (PDOException $e) {
        flash('error', 'Gagal memperbarui latihan: ' . $e->getMessage());
    }
}

// Ambil data latihan yang sudah ada
$existing_exercises = json_decode($day['exercises'], true);
if (!$existing_exercises) {
    $existing_exercises = [];
}
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <h2>Edit Jadwal Harian - <?php echo htmlspecialchars($day['day_name']); ?></h2>
            <p>Edit latihan untuk hari <?php echo htmlspecialchars($day['day_name']); ?> dalam jadwal "<?php echo htmlspecialchars($schedule['schedule_name']); ?>"</p>
            
            <?php if (flash('error')): ?>
                <div class="alert alert-danger"><?php echo flash('error'); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div id="exercises-container">
                    <?php if (count($existing_exercises) > 0): ?>
                        <?php foreach ($existing_exercises as $index => $exercise): ?>
                            <div class="card mb-3 exercise-item">
                                <div class="card-body">
                                    <h5 class="card-title">Latihan #<?php echo $index + 1; ?></h5>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label class="form-label">Nama Latihan</label>
                                            <input type="text" class="form-control" name="exercise_name[]" 
                                                   value="<?php echo htmlspecialchars($exercise['name'] ?? ''); ?>" 
                                                   placeholder="Contoh: Push Up">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Sets</label>
                                            <input type="number" class="form-control" name="sets[]" 
                                                   value="<?php echo $exercise['sets'] ?? 0; ?>" min="0">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Reps</label>
                                            <input type="number" class="form-control" name="reps[]" 
                                                   value="<?php echo $exercise['reps'] ?? 0; ?>" min="0">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Rest (detik)</label>
                                            <input type="number" class="form-control" name="rest[]" 
                                                   value="<?php echo $exercise['rest'] ?? 0; ?>" min="0">
                                        </div>
                                        <div class="col-md-1 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger remove-exercise">Hapus</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="card mb-3 exercise-item">
                            <div class="card-body">
                                <h5 class="card-title">Latihan #1</h5>
                                <div class="row">
                                    <div class="col-md-5">
                                        <label class="form-label">Nama Latihan</label>
                                        <input type="text" class="form-control" name="exercise_name[]" 
                                               placeholder="Contoh: Push Up">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Sets</label>
                                        <input type="number" class="form-control" name="sets[]" value="0" min="0">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Reps</label>
                                        <input type="number" class="form-control" name="reps[]" value="0" min="0">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Rest (detik)</label>
                                        <input type="number" class="form-control" name="rest[]" value="0" min="0">
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger remove-exercise">Hapus</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <button type="button" class="btn btn-info mb-3" id="add-exercise">Tambah Latihan</button>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Simpan Jadwal Harian</button>
                    <a href="view.php?id=<?php echo $schedule_id; ?>&member_id=<?= $effective_member_id; ?>" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tambah latihan baru
    document.getElementById('add-exercise').addEventListener('click', function() {
        const container = document.getElementById('exercises-container');
        const exerciseCount = container.children.length + 1;
        
        const exerciseDiv = document.createElement('div');
        exerciseDiv.className = 'card mb-3 exercise-item';
        exerciseDiv.innerHTML = `
            <div class="card-body">
                <h5 class="card-title">Latihan #${exerciseCount}</h5>
                <div class="row">
                    <div class="col-md-5">
                        <label class="form-label">Nama Latihan</label>
                        <input type="text" class="form-control" name="exercise_name[]" 
                               placeholder="Contoh: Push Up">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sets</label>
                        <input type="number" class="form-control" name="sets[]" value="0" min="0">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Reps</label>
                        <input type="number" class="form-control" name="reps[]" value="0" min="0">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Rest (detik)</label>
                        <input type="number" class="form-control" name="rest[]" value="0" min="0">
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger remove-exercise">Hapus</button>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(exerciseDiv);
        
        // Tambah event listener untuk tombol hapus baru
        exerciseDiv.querySelector('.remove-exercise').addEventListener('click', function() {
            exerciseDiv.remove();
        });
    });
    
    // Tambah event listener untuk tombol hapus yang sudah ada
    document.querySelectorAll('.remove-exercise').forEach(button => {
        button.addEventListener('click', function() {
            const card = this.closest('.exercise-item');
            if (document.querySelectorAll('.exercise-item').length > 1) {
                card.remove();
            } else {
                alert('Minimal harus ada satu latihan');
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
