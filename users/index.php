<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_login();
require_roles(['owner']);
$pdo = get_pdo();
$users = $pdo->query('SELECT id, name, email, role FROM users ORDER BY id ASC')->fetchAll();
include __DIR__ . '/../partials/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Manajemen User</h2>
</div>
<p class="text-muted">Ubah peran user: owner, trainer, atau customer.</p>
<table class="table table-striped table-hover">
  <thead>
    <tr>
      <th>ID</th>
      <th>Nama</th>
      <th>Email</th>
      <th>Peran</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($users as $u): ?>
      <tr>
        <td><?= (int)$u['id']; ?></td>
        <td><?= htmlspecialchars($u['name']); ?></td>
        <td><?= htmlspecialchars($u['email']); ?></td>
        <td>
          <form method="post" action="/FozGym/users/update_role.php" class="d-flex gap-2 align-items-center">
            <input type="hidden" name="user_id" value="<?= (int)$u['id']; ?>">
            <select name="role" class="form-select form-select-sm" style="width:160px">
              <?php foreach (['owner','trainer','customer'] as $role): ?>
                <option value="<?= $role; ?>" <?= $u['role'] === $role ? 'selected' : ''; ?>><?= ucfirst($role); ?></option>
              <?php endforeach; ?>
            </select>
            <button class="btn btn-sm btn-primary">Simpan</button>
          </form>
        </td>
        <td>
          <!-- Tambahan aksi jika perlu di masa depan -->
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php include __DIR__ . '/../partials/footer.php'; ?>