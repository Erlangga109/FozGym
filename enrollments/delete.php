<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_login();
require_roles(['owner','trainer']);
$pdo = get_pdo();
$id = (int)($_GET['id'] ?? 0);
// Currently no need to manage capacity since we don't use capacity field
$stmt = $pdo->prepare('SELECT class_id FROM enrollments WHERE id = ?');
$stmt->execute([$id]);
$row = $stmt->fetch();
if ($row) {
    $class_id = (int)$row['class_id'];
    try {
        $pdo->prepare('DELETE FROM enrollments WHERE id = ?')->execute([$id]);
        $pdo->commit();
        flash('success', 'Pendaftaran berhasil dibatalkan.');
    } catch (Exception $e) {
        flash('error', 'Gagal membatalkan pendaftaran: ' . $e->getMessage());
    }
} else {
    flash('error', 'Pendaftaran tidak ditemukan.');
}
header('Location: ' . get_base_url() . '/enrollments/index.php');
exit;
