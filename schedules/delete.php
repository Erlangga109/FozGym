<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';

require_login();

$pdo = get_pdo();
$user = current_user();

// Determine whose schedule is being deleted
$effective_member_id = $user['id'];
if (is_trainer() && isset($_GET['member_id'])) {
    $effective_member_id = (int)$_GET['member_id'];
}

$schedule_id = $_GET['id'] ?? 0;

// Ambil jadwal untuk verifikasi
$stmt = $pdo->prepare("
    SELECT *
    FROM workout_schedules
    WHERE id = ? AND member_id = ?
");
$stmt->execute([$schedule_id, $effective_member_id]);
$schedule = $stmt->fetch();

if (!$schedule) {
    flash('error', 'Jadwal tidak ditemukan atau Anda tidak memiliki akses');
    header('Location: index.php?member_id=' . $effective_member_id);
    exit;
}

try {
    // Hapus schedule_days terlebih dahulu (karena ada foreign key constraint)
    $stmt = $pdo->prepare("DELETE FROM schedule_days WHERE schedule_id = ?");
    $stmt->execute([$schedule_id]);
    
    // Hapus workout_schedules
    $stmt = $pdo->prepare("DELETE FROM workout_schedules WHERE id = ? AND member_id = ?");
    $stmt->execute([$schedule_id, $effective_member_id]);
    
    flash('success', 'Jadwal latihan berhasil dihapus');
} catch (PDOException $e) {
    flash('error', 'Gagal menghapus jadwal: ' . $e->getMessage());
}

header('Location: index.php?member_id=' . $effective_member_id);
exit;