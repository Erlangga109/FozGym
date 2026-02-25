<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';

require_login();

$pdo = get_pdo();
$user = current_user();

// Determine whose schedule is being converted
$effective_member_id = $user['id'];
if (is_trainer() && isset($_GET['member_id'])) {
    $effective_member_id = (int)$_GET['member_id'];
}

$schedule_id = $_GET['id'] ?? 0;

// Ambil jadwal AI berdasarkan ID dan member yang efektif
$stmt = $pdo->prepare("
    SELECT * 
    FROM workout_schedules 
    WHERE id = ? AND member_id = ? AND schedule_type = 'ai_generated'
");
$stmt->execute([$schedule_id, $effective_member_id]);
$schedule = $stmt->fetch();

if (!$schedule) {
    flash('error', 'Jadwal AI tidak ditemukan atau Anda tidak memiliki akses');
    header('Location: index.php?member_id=' . $effective_member_id);
    exit;
}

try {
    // Mulai transaksi untuk memastikan keberhasilan operasi
    $pdo->beginTransaction();

    // Ubah tipe jadwal dari 'ai_generated' menjadi 'manual'
    $stmt = $pdo->prepare("UPDATE workout_schedules SET schedule_type = 'manual' WHERE id = ?");
    $stmt->execute([$schedule_id]);

    $pdo->commit();

    flash('success', 'Jadwal berhasil dikonversi ke mode manual dan sekarang bisa diedit!');
    // Redirect to the edit page, maintaining the member context
    header('Location: edit.php?id=' . $schedule_id . '&member_id=' . $effective_member_id);
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    flash('error', 'Gagal mengkonversi jadwal: ' . $e->getMessage());
    header('Location: view.php?id=' . $schedule_id . '&member_id=' . $effective_member_id);
    exit;
}