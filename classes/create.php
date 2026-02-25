<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_login();
require_roles(['owner','trainer']);
$pdo = get_pdo();

// Check if logged-in user is trainer (not owner) to restrict trainer selection
$user = current_user();
$user_id = $user['id'];
$user_role = $user['role'];

// Only owner can assign classes to different trainers
if ($user_role === 'trainer') {
    // For trainers, fetch only their own information
    $stmt_trainers = $pdo->prepare("SELECT id, name FROM users WHERE role = 'trainer' AND id = ?");
    $stmt_trainers->execute([$user_id]);
} else {
    // For owners, fetch all trainers
    $stmt_trainers = $pdo->query("SELECT id, name FROM users WHERE role = 'trainer' ORDER BY name");
}
$trainers = $stmt_trainers->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $schedule = trim($_POST['schedule'] ?? '');
    $max_participants = (int)($_POST['max_participants'] ?? 10);
    $trainer_id = (int)($_POST['trainer_id'] ?? 0);
    $active = (int)($_POST['active'] ?? 1);

    // If user is trainer, force trainer_id to their own ID
    if ($user_role === 'trainer') {
        $trainer_id = $user_id;
    } else {
        // If owner, validate that trainer_id is valid
        if ($trainer_id > 0) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND role = 'trainer'");
            $stmt->execute([$trainer_id]);
            $result = $stmt->fetch();
            if (!$result) {
                flash('error', 'Trainer yang dipilih tidak valid.');
                $trainer_id = 0;
            }
        }
    }

    if (!$name) {
        flash('error', 'Nama kelas wajib diisi.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO classes(name, description, schedule, max_participants, trainer_id, active) VALUES(?,?,?,?,?,?)');
        $stmt->execute([$name, $description, $schedule, $max_participants, $trainer_id ?: null, $active]);
        flash('success', 'Kelas berhasil ditambahkan.');
        header('Location: ' . get_base_url() . '/classes/index.php');
        exit;
    }
}
include __DIR__ . '/../partials/header.php';
?>
<h2 class="mb-3">Tambah Kelas</h2>
<form method="post" class="row g-3" style="max-width:640px">
  <div class="col-12">
    <label class="form-label">Nama Kelas</label>
    <input type="text" name="name" class="form-control" required>
  </div>
  <div class="col-12">
    <label class="form-label">Deskripsi</label>
    <textarea name="description" class="form-control" rows="3"></textarea>
  </div>
  <div class="col-12">
    <label class="form-label">Jadwal</label>
    <input type="text" name="schedule" class="form-control">
  </div>
  <div class="col-md-6">
    <label class="form-label">Jumlah Peserta Maksimal</label>
    <input type="number" name="max_participants" class="form-control" value="10" min="1" max="100">
  </div>
  <div class="col-md-6">
    <label class="form-label">Status</label>
    <select name="active" class="form-select">
      <option value="1" selected>Aktif</option>
      <option value="0">Tidak Aktif</option>
    </select>
  </div>
  <?php if ($user_role === 'owner'): ?>
  <div class="col-12">
    <label class="form-label">Pelatih</label>
    <select name="trainer_id" class="form-select">
      <option value="">-- Tidak Ditugaskan --</option>
      <?php foreach ($trainers as $trainer): ?>
        <option value="<?= $trainer['id']; ?>"><?= htmlspecialchars($trainer['name']); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <?php else: ?>
  <input type="hidden" name="trainer_id" value="<?= $user_id; ?>">
  <div class="col-12">
    <label class="form-label">Pelatih</label>
    <div class="form-control-plaintext"><?= htmlspecialchars($user['name']); ?></div>
  </div>
  <?php endif; ?>
  <div class="col-12">
    <button type="submit" class="btn btn-success">Simpan</button>
    <a class="btn btn-outline-secondary" href="<?= get_base_url(); ?>/classes/index.php">Batal</a>
  </div>
</form>
<?php include __DIR__ . '/../partials/footer.php'; ?>