<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_login();
require_roles(['owner','trainer']);
$pdo = get_pdo();
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM classes WHERE id = ?');
$stmt->execute([$id]);
$class = $stmt->fetch();
if (!$class) { flash('error', 'Kelas tidak ditemukan.'); header('Location: ' . get_base_url() . '/classes/index.php'); exit; }

// Check if the logged-in user is owner or the trainer who created this class
$user = current_user();
$user_id = $user['id'];
$user_role = $user['role'];

if ($user_role !== 'owner' && (int)$class['trainer_id'] !== $user_id) {
    flash('error', 'Anda tidak memiliki izin untuk mengedit kelas ini.');
    header('Location: ' . get_base_url() . '/classes/index.php');
    exit;
}

// Fetch trainers to populate the dropdown
$stmt_trainers = $pdo->query("SELECT id, name FROM users WHERE role = 'trainer' ORDER BY name");
$trainers = $stmt_trainers->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $schedule = trim($_POST['schedule'] ?? '');
    $max_participants = (int)($_POST['max_participants'] ?? 10);
    $trainer_id = (int)($_POST['trainer_id'] ?? 0);
    $active = (int)($_POST['active'] ?? 1);

    if (!$name) {
        flash('error', 'Nama kelas wajib diisi.');
    } else {
        $update = $pdo->prepare('UPDATE classes SET name=?, description=?, schedule=?, max_participants=?, trainer_id=?, active=? WHERE id=?');
        $update->execute([$name, $description, $schedule, $max_participants, $trainer_id ?: null, $active, $id]);
        flash('success', 'Kelas berhasil diperbarui.');
        header('Location: ' . get_base_url() . '/classes/index.php');
        exit;
    }
}
include __DIR__ . '/../partials/header.php';
?>
<h2 class="mb-3">Edit Kelas</h2>
<form method="post" class="row g-3" style="max-width:640px">
  <div class="col-12">
    <label class="form-label">Nama Kelas</label>
    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($class['name']); ?>" required>
  </div>
  <div class="col-12">
    <label class="form-label">Deskripsi</label>
    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($class['description']); ?></textarea>
  </div>
  <div class="col-12">
    <label class="form-label">Jadwal</label>
    <input type="text" name="schedule" class="form-control" value="<?= htmlspecialchars($class['schedule']); ?>">
  </div>
  <div class="col-md-6">
    <label class="form-label">Jumlah Peserta Maksimal</label>
    <input type="number" name="max_participants" class="form-control" value="<?= (int)$class['max_participants']; ?>" min="1" max="100">
  </div>
  <div class="col-md-6">
    <label class="form-label">Status</label>
    <select name="active" class="form-select">
      <option value="1" <?= $class['active'] ? 'selected' : '' ?>>Aktif</option>
      <option value="0" <?= $class['active'] ? '' : 'selected' ?>>Tidak Aktif</option>
    </select>
  </div>
  <div class="col-12">
    <label class="form-label">Pelatih</label>
    <select name="trainer_id" class="form-select">
      <option value="">-- Tidak Ditugaskan --</option>
      <?php foreach ($trainers as $trainer): ?>
        <option value="<?= $trainer['id']; ?>" <?= (int)$class['trainer_id'] === $trainer['id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($trainer['name']); ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-12">
    <button class="btn btn-primary">Update</button>
    <a class="btn btn-outline-secondary" href="<?= get_base_url(); ?>/classes/index.php">Kembali</a>
  </div>
</form>
<?php include __DIR__ . '/../partials/footer.php'; ?>