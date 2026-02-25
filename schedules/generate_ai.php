<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../partials/header.php';

require_login();

$pdo = get_pdo();
$user = current_user();

// --- Refactored Member ID Logic ---
// 1. Determine the target member ID. If a trainer is creating for a member, the ID comes from GET or POST.
//    Otherwise, it's the logged-in user's own ID.
$member_id_for_schedule = $user['id'];
if (is_trainer() && isset($_REQUEST['member_id_for_schedule'])) {
    $member_id_for_schedule = (int)$_REQUEST['member_id_for_schedule'];
} elseif (is_trainer() && isset($_GET['member_id'])) {
    $member_id_for_schedule = (int)$_GET['member_id'];
}

// 2. Fetch the member's name if a trainer is creating the schedule.
$member_name = '';
if (is_trainer() && $member_id_for_schedule !== $user['id']) {
    $stmt_member = $pdo->prepare("SELECT name FROM members WHERE id = ?");
    $stmt_member->execute([$member_id_for_schedule]);
    $member_data = $stmt_member->fetch();
    if ($member_data) {
        $member_name = htmlspecialchars($member_data['name']);
    } else {
        flash('error', 'Member tidak ditemukan.');
        header('Location: /FozGym/classes/my_classes.php');
        exit;
    }
}
// --- End of Refactored Logic ---


// Periksa apakah API key untuk salah satu layanan sudah diset
$has_api_key = !empty($OPENAI_API_KEY) || !empty($GEMINI_API_KEY);

// Proses form jika disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $has_api_key) {
    $schedule_name = trim($_POST['schedule_name'] ?? '');
    $target_muscles = trim($_POST['target_muscles'] ?? '');
    $training_goals = trim($_POST['training_goals'] ?? '');
    $experience_level = $_POST['experience_level'] ?? 'beginner';
    $available_days = $_POST['available_days'] ?? [];
    $time_per_session = $_POST['time_per_session'] ?? 30;
    
    // Validasi input
    if (empty($schedule_name) || empty($target_muscles) || empty($training_goals)) {
        flash('error', 'Semua field wajib diisi');
    } else {
        // Simpan ke database sebagai jadwal sementara untuk diproses oleh AI
        try {
            // Simpan jadwal utama, using the correctly determined member ID
            $stmt = $pdo->prepare("
                INSERT INTO workout_schedules 
                (member_id, schedule_name, schedule_type, target_muscle_groups, training_goals, difficulty_level) 
                VALUES (?, ?, 'ai_generated', ?, ?, ?)
            ");
            $stmt->execute([$member_id_for_schedule, $schedule_name, $target_muscles, $training_goals, $experience_level]);
            $schedule_id = $pdo->lastInsertId();
            
            // Buat hari-hari latihan berdasarkan pilihan customer
            $all_days = [
                0 => 'Minggu',
                1 => 'Senin',
                2 => 'Selasa',
                3 => 'Rabu',
                4 => 'Kamis',
                5 => 'Jumat',
                6 => 'Sabtu'
            ];
            
            $stmt = $pdo->prepare("
                INSERT INTO schedule_days (schedule_id, day_of_week, day_name, exercises) 
                VALUES (?, ?, ?, '[]')
            ");
            
            // Hanya buat jadwal untuk hari yang dipilih customer
            foreach ($available_days as $day_index) {
                if (isset($all_days[$day_index])) {
                    $stmt->execute([$schedule_id, $day_index, $all_days[$day_index]]);
                }
            }
            
            // Redirect ke halaman generate untuk diproses oleh AI, passing the member ID
            header('Location: process_ai.php?id=' . $schedule_id . '&time_per_session=' . $time_per_session . '&member_id=' . $member_id_for_schedule);
            exit;
        } catch (PDOException $e) {
            flash('error', 'Gagal menyimpan jadwal: ' . $e->getMessage());
        }
    }
}
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <h2>Buat Jadwal Latihan Otomatis (AI)</h2>
            <?php if ($member_name): ?>
                <p class="lead">Untuk Member: <strong><?= $member_name; ?></strong></p>
            <?php else: ?>
                <p>AI akan membuatkan jadwal latihan sesuai target dan kebutuhan Anda</p>
            <?php endif; ?>
            
            <?php if (!$has_api_key): ?>
                <div class="alert alert-warning">
                    <h5>API Key Belum Diset</h5>
                    <p>Fitur AI memerlukan salah satu API key berikut untuk berfungsi:</p>
                    <ul>
                        <li><strong>OpenAI API Key</strong>: untuk menggunakan GPT (dikenakan biaya)</li>
                        <li><strong>Gemini API Key</strong>: untuk menggunakan Gemini dari Google (gratis terbatas)</li>
                    </ul>
                    <p>Silakan hubungi administrator untuk mengatur API key.</p>
                    <p>Atau jika Anda memiliki API key, Anda bisa menambahkannya di file <code>config.php</code> pada variabel <code>$OPENAI_API_KEY</code> atau <code>$GEMINI_API_KEY</code></p>
                    <p><strong>Rekomendasi:</strong> Gunakan Gemini API karena menyediakan kuota gratis.</p>
                </div>
            <?php endif; ?>
            
            <?php if (flash('error')): ?>
                <div class="alert alert-danger"><?php echo flash('error'); ?></div>
            <?php endif; ?>
            
            <?php if ($has_api_key): ?>
                <form method="POST" action="">
                    <input type="hidden" name="member_id_for_schedule" value="<?= $member_id_for_schedule; ?>">

                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Informasi Dasar</h5>
                            
                            <div class="mb-3">
                                <label for="schedule_name" class="form-label">Nama Jadwal</label>
                                <input type="text" class="form-control" id="schedule_name" name="schedule_name" 
                                       value="<?php echo htmlspecialchars($_POST['schedule_name'] ?? ''); ?>" required>
                                <div class="form-text">Berikan nama untuk jadwal latihan Anda (contoh: "Program Pembentukan Otot", "Program Penurunan Berat Badan")</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="target_muscles" class="form-label">Target Otot</label>
                                <input type="text" class="form-control" id="target_muscles" name="target_muscles" 
                                       value="<?php echo htmlspecialchars($_POST['target_muscles'] ?? ''); ?>" 
                                       placeholder="Contoh: Dada, Bahu, Punggung, Lengan, Kaki" required>
                                <div class="form-text">Otot utama yang ingin Anda latih</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="training_goals" class="form-label">Tujuan Latihan</label>
                                <textarea class="form-control" id="training_goals" name="training_goals" rows="3" required
                                          placeholder="Contoh: Membentuk otot, menurunkan berat badan, meningkatkan kekuatan, meningkatkan daya tahan"><?php echo htmlspecialchars($_POST['training_goals'] ?? ''); ?></textarea>
                                <div class="form-text">Jelaskan tujuan utama latihan Anda</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Pengalaman dan Ketersediaan Waktu</h5>
                            
                            <div class="mb-3">
                                <label for="experience_level" class="form-label">Tingkat Pengalaman</label>
                                <select class="form-control" id="experience_level" name="experience_level">
                                    <option value="beginner" <?php echo (($_POST['experience_level'] ?? '') === 'beginner') ? 'selected' : ''; ?>>Pemula (0-6 bulan latihan)</option>
                                    <option value="intermediate" <?php echo (($_POST['experience_level'] ?? '') === 'intermediate') ? 'selected' : ''; ?>>Menengah (6 bulan-2 tahun latihan)</option>
                                    <option value="advanced" <?php echo (($_POST['experience_level'] ?? '') === 'advanced') ? 'selected' : ''; ?>>Lanjutan (lebih dari 2 tahun latihan)</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="time_per_session" class="form-label">Waktu per Sesi (menit)</label>
                                <input type="number" class="form-control" id="time_per_session" name="time_per_session" 
                                       value="<?php echo htmlspecialchars($_POST['time_per_session'] ?? '45'); ?>" min="15" max="120">
                                <div class="form-text">Durasi rata-rata satu sesi latihan Anda (dalam menit)</div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Hari yang Tersedia untuk Latihan</label>
                                <div class="row">
                                    <?php
                                    $days = [
                                        1 => 'Senin',
                                        2 => 'Selasa',
                                        3 => 'Rabu',
                                        4 => 'Kamis',
                                        5 => 'Jumat',
                                        6 => 'Sabtu',
                                        0 => 'Minggu'
                                    ];
                                    $selected_days = $_POST['available_days'] ?? [1, 3, 5]; // Default: Senin, Rabu, Jumat
                                    ?>
                                    <?php foreach ($days as $index => $name): ?>
                                        <div class="col-md-4 col-lg-2 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="available_days[]" value="<?php echo $index; ?>" 
                                                       id="day_<?php echo $index; ?>" <?php echo (in_array($index, $selected_days)) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="day_<?php echo $index; ?>">
                                                    <?php echo $name; ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="form-text">Pilih hari-hari yang tersedia untuk latihan</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">Buat Jadwal Otomatis</button>
                        <a href="index.php" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            <?php else: ?>
                <div class="d-flex gap-2">
                    <a href="index.php" class="btn btn-secondary">Kembali ke Daftar Jadwal</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>