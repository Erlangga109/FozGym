<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_login(); // Keep this to ensure user is logged in

$pdo = get_pdo();
$user = current_user();
$user_role = $user['role'] ?? 'customer';
$user_id = $user['id'] ?? 0;
$is_admin = in_array($user_role, ['owner', 'trainer']);
$is_owner = $user_role === 'owner';
$is_trainer = $user_role === 'trainer';

// Fetch classes with trainer and current enrolled count
// Trainer can now see all classes, not just their own
$stmt = $pdo->query('SELECT c.*,
    (SELECT COUNT(*) FROM enrollments e WHERE e.class_id = c.id) AS enrolled_count,
    (SELECT name FROM users u WHERE u.id = c.trainer_id) AS trainer_name
  FROM classes c
  ORDER BY c.created_at DESC');
$classes = $stmt->fetchAll();

include __DIR__ . '/../partials/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Kelas yang Tersedia</h2>
  <?php if ($is_admin): ?>
    <a class="btn btn-primary" href="<?= get_base_url(); ?>/classes/create.php">Tambah Kelas</a>
  <?php endif; ?>
</div>

<table class="table table-striped table-hover">
  <thead>
    <tr>
      <th>Nama Kelas</th>
      <th>Deskripsi</th>
      <th>Jadwal</th>
      <th>Maks. Peserta</th>
      <th>Terdaftar</th>
      <th>Sisa Kuota</th>
      <th>Trainer</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($classes as $c): ?>
      <tr>
        <td><?= htmlspecialchars($c['name']); ?></td>
        <td><?= htmlspecialchars($c['description']); ?></td>
        <td><?= htmlspecialchars($c['schedule']); ?></td>
        <td><?= (int)$c['max_participants']; ?></td>
        <td><?= (int)$c['enrolled_count']; ?></td>
        <td><?= (int)$c['max_participants'] - (int)$c['enrolled_count']; ?></td>
        <td><?= htmlspecialchars($c['trainer_name'] ?? '-'); ?></td>
        <td class="table-actions">
          <?php if ($is_owner || ($is_trainer && (int)$c['trainer_id'] === $user_id)): ?>
            <a class="btn btn-sm btn-outline-primary" href="<?= get_base_url(); ?>/classes/edit.php?id=<?= $c['id']; ?>">Edit</a>
            <a class="btn btn-sm btn-outline-danger" href="<?= get_base_url(); ?>/classes/delete.php?id=<?= $c['id']; ?>" onclick="return confirm('Hapus kelas ini?');">Hapus</a>
          <?php elseif ($is_trainer): ?>
            <!-- Trainer can see class but can't edit/delete classes they don't own -->
            <a class="btn btn-sm btn-info" href="<?= get_base_url(); ?>/classes/view.php?id=<?= $c['id']; ?>">Lihat</a>
          <?php else: ?>
            <?php if (((int)$c['max_participants'] - (int)$c['enrolled_count']) <= 0): ?>
              <button class="btn btn-sm btn-secondary" disabled>Penuh</button>
            <?php else: ?>
              <a class="btn btn-sm btn-success" href="<?= get_base_url(); ?>/enrollments/create.php?class_id=<?= $c['id']; ?>">Daftar</a>
            <?php endif; ?>
            <a class="btn btn-sm btn-info" href="<?= get_base_url(); ?>/classes/view.php?id=<?= $c['id']; ?>">Lihat</a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php include __DIR__ . '/../partials/footer.php'; ?>
