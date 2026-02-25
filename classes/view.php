<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_login();

$pdo = get_pdo();
$id = (int)($_GET['id'] ?? 0);

// Check if the logged-in user is owner or the trainer who created this class
$user = current_user();
$user_id = $user['id'];
$user_role = $user['role'];

$stmt = $pdo->prepare('SELECT c.*, u.name AS trainer_name FROM classes c LEFT JOIN users u ON c.trainer_id = u.id WHERE c.id = ?');
$stmt->execute([$id]);
$class = $stmt->fetch();
if (!$class) { flash('error', 'Kelas tidak ditemukan.'); header('Location: /FozGym/classes/index.php'); exit; }

// Check authorization - only owner and trainers can access, but customers can also view
if ($user_role === 'customer') {
    // Customers can view but with limited information
} elseif ($user_role === 'trainer') {
    // Trainers can view all classes
} elseif ($user_role === 'owner') {
    // Owners can view all classes
} else {
    flash('error', 'Anda tidak memiliki izin untuk mengakses kelas ini.');
    header('Location: /FozGym/classes/index.php');
    exit;
}

// Fetch enrolled users (not members, since we use user_id in enrollments)
$stmtUsers = $pdo->prepare('SELECT u.id, u.name, u.email FROM enrollments e JOIN users u ON e.user_id = u.id WHERE e.class_id = ? ORDER BY u.name');
$stmtUsers->execute([$id]);
$users = $stmtUsers->fetchAll();

// If user is customer, they can only see limited info
$can_see_enrolled_users = $user_role === 'owner' || $user_role === 'trainer';

include __DIR__ . '/../partials/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2>Detail Kelas</h2>
  <a class="btn btn-outline-secondary" href="/FozGym/classes/index.php">Kembali</a>
  </div>
<div class="card mb-4"><div class="card-body">
  <h5 class="card-title mb-2"><?= htmlspecialchars($class['name']); ?></h5>
  <p class="mb-1"><?= htmlspecialchars($class['description']); ?></p>
  <div class="text-muted mb-2">Jadwal: <?= htmlspecialchars($class['schedule']); ?></div>
  <div class="text-muted mb-2">Trainer: <?= htmlspecialchars($class['trainer_name'] ?? '-'); ?></div>
  <div class="text-muted">Jumlah Peserta Maksimal: <?= (int)$class['max_participants']; ?></div>
</div></div>

<h4>Anggota Kelas</h4>
<?php if ($can_see_enrolled_users): ?>
    <?php if (empty($users)): ?>
        <div class="alert alert-info">Belum ada anggota terdaftar.</div>
    <?php else: ?>
        <ul class="list-group">
            <?php foreach ($users as $u): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div><?= htmlspecialchars($u['name']); ?> <small class="text-muted">(<?= htmlspecialchars($u['email']); ?>)</small></div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
<?php else: ?>
    <?php if (empty($users)): ?>
        <div class="alert alert-info">Kelas ini belum memiliki peserta terdaftar.</div>
    <?php else: ?>
        <div class="alert alert-info">Kelas ini memiliki <?= count($users); ?> peserta terdaftar.</div>
    <?php endif; ?>
<?php endif; ?>

<?php include __DIR__ . '/../partials/footer.php'; ?>

