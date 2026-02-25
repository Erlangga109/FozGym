<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_login(); // All users must be logged in

$pdo = get_pdo();
$user_role = current_user()['role'] ?? 'customer';
$is_admin = in_array($user_role, ['owner', 'trainer']);

if ($is_admin) {
    // --- Admin Logic ---
    $users = $pdo->query('SELECT id, name FROM users WHERE id IN (SELECT user_id FROM members) ORDER BY name')->fetchAll();
    $classes = $pdo->query('SELECT id, name FROM classes ORDER BY name')->fetchAll();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_id = (int)($_POST['user_id'] ?? 0);
        $class_id = (int)($_POST['class_id'] ?? 0);
        if (!$user_id || !$class_id) {
            flash('error', 'Pilih user dan kelas.');
        } else {
            try {
                $pdo->beginTransaction();

                // Check max_participants
                $stmtCap = $pdo->prepare('SELECT max_participants FROM classes WHERE id = ? FOR UPDATE');
                $stmtCap->execute([$class_id]);
                $rowCap = $stmtCap->fetch();
                if (!$rowCap) {
                    throw new Exception('Kelas tidak ditemukan.');
                }

                // Count how many enrollments are currently for this class
                $stmtCount = $pdo->prepare('SELECT COUNT(*) FROM enrollments WHERE class_id = ?');
                $stmtCount->execute([$class_id]);
                $currentCount = (int)$stmtCount->fetchColumn();

                $maxParticipants = (int)$rowCap['max_participants'];
                if ($currentCount >= $maxParticipants) {
                    flash('error', 'Kelas sudah penuh, tidak bisa mendaftar.');
                    $pdo->rollBack();
                } else {
                    // Prevent duplicate enrollment
                    $stmtDup = $pdo->prepare('SELECT id FROM enrollments WHERE user_id = ? AND class_id = ?');
                    $stmtDup->execute([$user_id, $class_id]);
                    if ($stmtDup->fetch()) {
                        flash('error', 'User sudah terdaftar di kelas ini.');
                        $pdo->rollBack();
                    } else {
                        // Insert enrollment
                        $stmtIns = $pdo->prepare('INSERT INTO enrollments(user_id, class_id, enrollment_date) VALUES(?,?,NOW())');
                        $stmtIns->execute([$user_id, $class_id]);
                        $pdo->commit();
                        flash('success', 'Pendaftaran berhasil.');
                        header('Location: ' . get_base_url() . '/enrollments/index.php');
                        exit;
                    }
                }
            } catch (Exception $e) {
                if ($pdo->inTransaction()) { $pdo->rollBack(); }
                flash('error', 'Pendaftaran gagal: ' . $e->getMessage());
            }
        }
    }
} else {
    // --- Customer Logic ---
    $current_user = current_user();
    $user_id = $current_user['id']; // Customer can only enroll themselves
    $class_id = (int)($_GET['class_id'] ?? 0);

    if (!$class_id) {
        flash('error', 'Kelas tidak valid.');
        header('Location: /FozGym/classes/index.php');
        exit;
    }

    // Get class details for confirmation
    $stmt = $pdo->prepare('SELECT name, max_participants FROM classes WHERE id = ?');
    $stmt->execute([$class_id]);
    $class = $stmt->fetch();

    if (!$class) {
        flash('error', 'Kelas tidak ditemukan.');
        header('Location: /FozGym/classes/index.php');
        exit;
    }

    // Count current enrollments for capacity check
    $stmtCount = $pdo->prepare('SELECT COUNT(*) FROM enrollments WHERE class_id = ?');
    $stmtCount->execute([$class_id]);
    $currentCount = (int)$stmtCount->fetchColumn();

    // Process enrollment on confirmation
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $pdo->beginTransaction();

            // Check if already enrolled
            $stmt = $pdo->prepare('SELECT id FROM enrollments WHERE user_id = ? AND class_id = ?');
            $stmt->execute([$user_id, $class_id]);
            if ($stmt->fetch()) {
                flash('error', 'Anda sudah terdaftar di kelas ini.');
                header('Location: /FozGym/classes/index.php');
                $pdo->rollBack();
                exit;
            }

            // Check capacity
            if ($currentCount >= (int)$class['max_participants']) {
                flash('error', 'Kelas sudah penuh, tidak bisa mendaftar.');
                $pdo->rollBack();
                header('Location: /FozGym/classes/index.php');
                exit;
            }

            $stmt = $pdo->prepare('INSERT INTO enrollments(user_id, class_id, enrollment_date) VALUES(?,?,NOW())');
            $stmt->execute([$user_id, $class_id]);

            $pdo->commit();

            flash('success', 'Anda berhasil mendaftar di kelas ' . htmlspecialchars($class['name']) . '!');
            header('Location: /FozGym/classes/index.php'); // Redirect back to classes list
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            flash('error', 'Pendaftaran gagal: ' . $e->getMessage());
            header('Location: /FozGym/classes/index.php');
            exit;
        }
    }
}

include __DIR__ . '/../partials/header.php';
?>

<?php if ($is_admin): ?>
    <h2 class="mb-3">Daftarkan Member ke Kelas</h2>
    <form method="post" class="row g-3" style="max-width:640px">
        <div class="col-12">
            <label class="form-label">User</label>
            <select name="user_id" class="form-select" required>
                <option value="">-- Pilih User --</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id']; ?>"><?= htmlspecialchars($u['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Kelas</label>
            <select name="class_id" class="form-select" required>
                <option value="">-- Pilih Kelas --</option>
                <?php foreach ($classes as $c): ?>
                    <option value="<?= $c['id']; ?>" <?= (int)($_GET['class_id'] ?? 0) === $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12">
            <button type="submit" class="btn btn-success">Daftarkan</button>
            <a class="btn btn-outline-secondary" href="<?= get_base_url(); ?>/enrollments/index.php">Batal</a>
        </div>
    </form>
<?php else: ?>
    <div class="card" style="max-width: 640px; margin: auto;">
        <div class="card-body">
            <h2 class="card-title mb-3">Konfirmasi Pendaftaran</h2>
            <p>Anda akan mendaftar sebagai:</p>
            <ul>
                <li><strong>User:</strong> <?= htmlspecialchars(current_user()['name']); ?></li>
                <li><strong>Kelas:</strong> <?= htmlspecialchars($class['name']); ?></li>
            </ul>
            <p>Apakah Anda yakin ingin melanjutkan?</p>
            <form method="post">
                <button type="submit" class="btn btn-success">Ya, Daftarkan Saya</button>
                <a class="btn btn-outline-secondary" href="<?= get_base_url(); ?>/classes/index.php">Batal</a>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../partials/footer.php'; ?>
