<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_login();
require_roles(['owner','trainer']);
$pdo = get_pdo();
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM members WHERE id = ?');
$stmt->execute([$id]);
$member = $stmt->fetch();
if (!$member) { flash('error', 'Member tidak ditemukan.'); header('Location: /FozGym/members/index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $level = trim($_POST['fitness_level'] ?? 'Beginner');
    $active = isset($_POST['active']) ? 1 : 0;
    if (!$name || !$email) {
        flash('error', 'Nama dan email wajib diisi.');
    } else {
        $update = $pdo->prepare('UPDATE members SET name=?, email=?, phone=?, fitness_level=?, active=? WHERE id=?');
        $update->execute([$name, $email, $phone, $level, $active, $id]);
        flash('success', 'Member berhasil diperbarui.');
        header('Location: /FozGym/members/index.php');
        exit;
    }
}
include __DIR__ . '/../partials/header.php';
?>
<h2 class="mb-3">Edit Member</h2>
<form method="post" class="row g-3" style="max-width:640px">
  <div class="col-md-6">
    <label class="form-label">Nama</label>
    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($member['name']); ?>" required>
  </div>
  <div class="col-md-6">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($member['email']); ?>" required>
  </div>
  <div class="col-md-6">
    <label class="form-label">Telepon</label>
    <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($member['phone']); ?>">
  </div>
  <div class="col-md-6">
    <label class="form-label">Level Kebugaran</label>
    <select name="fitness_level" class="form-select">
      <?php foreach(['Beginner','Intermediate','Advanced'] as $lvl): ?>
        <option value="<?= $lvl; ?>" <?= $member['fitness_level']===$lvl?'selected':''; ?>><?= $lvl; ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-12 form-check">
    <input class="form-check-input" type="checkbox" name="active" id="active" <?= $member['active'] ? 'checked' : ''; ?>>
    <label class="form-check-label" for="active">Aktif</label>
  </div>
  <div class="col-12">
    <button class="btn btn-primary">Update</button>
    <a class="btn btn-outline-secondary" href="/FozGym/members/index.php">Kembali</a>
  </div>
</form>
<?php include __DIR__ . '/../partials/footer.php'; ?>