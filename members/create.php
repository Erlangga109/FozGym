<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_login();
require_roles(['owner','trainer']);
$pdo = get_pdo();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $level = trim($_POST['fitness_level'] ?? 'Beginner');
    $active = isset($_POST['active']) ? 1 : 0;

    if (!$name || !$email) {
        flash('error', 'Nama dan email wajib diisi.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO members(name, email, phone, fitness_level, active) VALUES(?,?,?,?,?)');
        $stmt->execute([$name, $email, $phone, $level, $active]);
        flash('success', 'Member berhasil ditambahkan.');
        header('Location: /FozGym/members/index.php');
        exit;
    }
}
include __DIR__ . '/../partials/header.php';
?>
<h2 class="mb-3">Tambah Member</h2>
<form method="post" class="row g-3" style="max-width:640px">
  <div class="col-md-6">
    <label class="form-label">Nama</label>
    <input type="text" name="name" class="form-control" required>
  </div>
  <div class="col-md-6">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control" required>
  </div>
  <div class="col-md-6">
    <label class="form-label">Telepon</label>
    <input type="text" name="phone" class="form-control">
  </div>
  <div class="col-md-6">
    <label class="form-label">Level Kebugaran</label>
    <select name="fitness_level" class="form-select">
      <option>Beginner</option>
      <option>Intermediate</option>
      <option>Advanced</option>
    </select>
  </div>
  <div class="col-12 form-check">
    <input class="form-check-input" type="checkbox" name="active" id="active">
    <label class="form-check-label" for="active">Aktif</label>
  </div>
  <div class="col-12">
    <button class="btn btn-success">Simpan</button>
    <a class="btn btn-outline-secondary" href="/FozGym/members/index.php">Batal</a>
  </div>
</form>
<?php include __DIR__ . '/../partials/footer.php'; ?>