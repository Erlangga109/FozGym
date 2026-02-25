<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_login();
require_roles(['owner','trainer']);
$pdo = get_pdo();
$keyword = trim($_GET['q'] ?? '');
if ($keyword) {
    $stmt = $pdo->prepare('SELECT * FROM members WHERE name LIKE ? OR email LIKE ? ORDER BY created_at DESC');
    $stmt->execute(['%'.$keyword.'%', '%'.$keyword.'%']);
    $members = $stmt->fetchAll();
} else {
    $members = $pdo->query('SELECT * FROM members ORDER BY created_at DESC')->fetchAll();
}
include __DIR__ . '/../partials/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Member</h2>
  <a class="btn btn-primary" href="/FozGym/members/create.php">Tambah Member</a>
</div>
<form class="row g-2 mb-3" method="get">
  <div class="col-auto"><input class="form-control" type="text" name="q" placeholder="Cari nama atau email" value="<?= htmlspecialchars($keyword); ?>"></div>
  <div class="col-auto"><button class="btn btn-outline-secondary">Cari</button></div>
</form>
<table class="table table-striped table-hover">
  <thead><tr><th>Nama</th><th>Email</th><th>Telepon</th><th>Level</th><th>Aktif</th><th>Aksi</th></tr></thead>
  <tbody>
  <?php foreach ($members as $m): ?>
    <tr>
      <td><?= htmlspecialchars($m['name']); ?></td>
      <td><?= htmlspecialchars($m['email']); ?></td>
      <td><?= htmlspecialchars($m['phone']); ?></td>
      <td><?= htmlspecialchars($m['fitness_level']); ?></td>
      <td><?= $m['active'] ? 'Ya' : 'Tidak'; ?></td>
      <td class="table-actions">
        <a class="btn btn-sm btn-outline-primary" href="/FozGym/members/edit.php?id=<?= $m['id']; ?>">Edit</a>
        <a class="btn btn-sm btn-outline-danger" href="/FozGym/members/delete.php?id=<?= $m['id']; ?>" onclick="return confirm('Hapus member ini?');">Hapus</a>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php include __DIR__ . '/../partials/footer.php'; ?>