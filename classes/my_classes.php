<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_login();
require_roles(['trainer']); // Only trainers can access this page

$pdo = get_pdo();
$current_trainer_id = current_user()['id'];

// Fetch classes assigned to this trainer
$stmt_classes = $pdo->prepare("
    SELECT id, name, description
    FROM classes
    WHERE trainer_id = ?
    ORDER BY name
");
$stmt_classes->execute([$current_trainer_id]);
$assigned_classes = $stmt_classes->fetchAll();

include __DIR__ . '/../partials/header.php';
?>

<h2 class="mb-4">Kelas Saya</h2>

<?php if (empty($assigned_classes)): ?>
    <div class="alert alert-info">Anda belum ditugaskan ke kelas manapun.</div>
<?php else: ?>
    <?php foreach ($assigned_classes as $class): ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><?= htmlspecialchars($class['name']); ?></h5>
            </div>
            <div class="card-body">
                <p><?= htmlspecialchars($class['description']); ?></p>
                <h6>Anggota Terdaftar:</h6>
                <?php
                // Fetch users enrolled in this class (use user_id, not member_id)
                $stmt_users = $pdo->prepare("
                    SELECT u.id, u.name, u.email
                    FROM users u
                    JOIN enrollments e ON u.id = e.user_id
                    WHERE e.class_id = ?
                    ORDER BY u.name
                ");
                $stmt_users->execute([$class['id']]);
                $enrolled_users = $stmt_users->fetchAll();
                ?>

                <?php if (empty($enrolled_users)): ?>
                    <div class="alert alert-warning">Belum ada anggota yang terdaftar di kelas ini.</div>
                <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($enrolled_users as $user): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <?= htmlspecialchars($user['name']); ?>
                                    <small class="text-muted">(<?= htmlspecialchars($user['email']); ?>)</small>
                                </div>
                                <a href="<?= get_base_url(); ?>/schedules/create.php?member_id=<?= $user['id']; ?>" class="btn btn-sm btn-primary">Buat Jadwal Manual</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include __DIR__ . '/../partials/footer.php'; ?>
