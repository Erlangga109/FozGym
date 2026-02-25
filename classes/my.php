<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_login();

$pdo = get_pdo();
$user = current_user();
$user_id = (int)($user['id'] ?? 0);

// Classes the user has joined
$stmtJoined = $pdo->prepare('SELECT c.*, u.name AS trainer_name FROM enrollments e JOIN classes c ON e.class_id = c.id LEFT JOIN users u ON c.trainer_id = u.id WHERE e.user_id = ? ORDER BY c.name');
$stmtJoined->execute([$user_id]);
$joined = $stmtJoined->fetchAll();

// Available classes not joined yet
$stmtAvail = $pdo->prepare('SELECT c.*, (SELECT COUNT(*) FROM enrollments e WHERE e.class_id = c.id) AS enrolled_count, u.name AS trainer_name
  FROM classes c LEFT JOIN users u ON c.trainer_id = u.id
  WHERE c.active = 1 AND c.id NOT IN (SELECT class_id FROM enrollments WHERE user_id = ?)
  ORDER BY c.created_at DESC');
$stmtAvail->execute([$user_id]);
$available = $stmtAvail->fetchAll();

include __DIR__ . '/../partials/header.php';
?>
<h2 class="mb-4">Kelas Saya</h2>

<h4 class="mb-2">Diikuti</h4>
<?php if (empty($joined)): ?>
  <div class="alert alert-info">Anda belum mengikuti kelas manapun.</div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table table-striped">
      <thead><tr><th>Nama Kelas</th><th>Trainer</th><th>Jadwal</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($joined as $c): ?>
        <tr>
          <td><?= htmlspecialchars($c['name']); ?></td>
          <td><?= htmlspecialchars($c['trainer_name'] ?? '-'); ?></td>
          <td><?= htmlspecialchars($c['schedule']); ?></td>
          <td><a class="btn btn-sm btn-info" href="<?= get_base_url(); ?>/classes/view.php?id=<?= $c['id']; ?>">Lihat</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<h4 class="mt-4 mb-2">Tersedia</h4>
<?php if (empty($available)): ?>
  <div class="alert alert-success">Tidak ada kelas tersedia atau semua sudah penuh.</div>
<?php else: ?>
  <div class="table-responsive">
    <table class="table table-striped">
      <thead><tr><th>Nama Kelas</th><th>Trainer</th><th>Jadwal</th><th>Terdaftar</th><th>Sisa Kuota</th><th>Aksi</th></tr></thead>
      <tbody>
      <?php foreach ($available as $c): ?>
        <tr>
          <td><?= htmlspecialchars($c['name']); ?></td>
          <td><?= htmlspecialchars($c['trainer_name'] ?? '-'); ?></td>
          <td><?= htmlspecialchars($c['schedule']); ?></td>
          <td><?= (int)($c['enrolled_count'] ?? 0); ?></td>
          <td><?= max(0, (int)$c['max_participants'] - (int)$c['enrolled_count']); ?></td>
          <td>
            <?php if (((int)$c['max_participants'] - (int)$c['enrolled_count']) <= 0): ?>
              <button class="btn btn-sm btn-secondary" disabled>Penuh</button>
            <?php else: ?>
              <a class="btn btn-sm btn-success" href="<?= get_base_url(); ?>/enrollments/create.php?class_id=<?= $c['id']; ?>">Gabung</a>
            <?php endif; ?>
            <a class="btn btn-sm btn-info" href="<?= get_base_url(); ?>/classes/view.php?id=<?= $c['id']; ?>">Lihat</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/../partials/footer.php'; ?>

