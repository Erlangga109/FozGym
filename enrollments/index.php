<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_login();
require_roles(['owner','trainer']);
$pdo = get_pdo();

// Perlu join dengan users table karena enrollments menggunakan user_id bukan member_id
$sql = 'SELECT e.id, u.name AS member_name, c.name AS class_title, e.enrollment_date AS enrolled_at
        FROM enrollments e
        JOIN users u ON e.user_id = u.id
        JOIN classes c ON e.class_id = c.id
        ORDER BY e.enrollment_date DESC';
$rows = $pdo->query($sql)->fetchAll();
include __DIR__ . '/../partials/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Pendaftaran Kelas</h2>
  <a class="btn btn-primary" href="<?= get_base_url(); ?>/enrollments/create.php">Daftarkan Member ke Kelas</a>
</div>
<table class="table table-striped table-hover">
  <thead><tr><th>Member</th><th>Kelas</th><th>Tanggal Daftar</th><th>Aksi</th></tr></thead>
  <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= htmlspecialchars($r['member_name']); ?></td>
        <td><?= htmlspecialchars($r['class_title']); ?></td>
        <td><?= htmlspecialchars($r['enrolled_at']); ?></td>
        <td class="table-actions">
          <a class="btn btn-sm btn-outline-danger" href="<?= get_base_url(); ?>/enrollments/delete.php?id=<?= $r['id']; ?>" onclick="return confirm('Batalkan pendaftaran ini?');">Batalkan</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php include __DIR__ . '/../partials/footer.php'; ?>