<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_login();
require_roles(['owner','trainer']);
$pdo = get_pdo();
$id = (int)($_GET['id'] ?? 0);

// Check if the logged-in user is owner or the trainer who created this class
$user = current_user();
$user_id = $user['id'];
$user_role = $user['role'];

// Get class information to check trainer_id
$stmt = $pdo->prepare('SELECT trainer_id FROM classes WHERE id = ?');
$stmt->execute([$id]);
$class = $stmt->fetch();
if (!$class) {
    flash('error', 'Kelas tidak ditemukan.');
    header('Location: ' . get_base_url() . '/classes/index.php');
    exit;
}

if ($user_role !== 'owner' && (int)$class['trainer_id'] !== $user_id) {
    flash('error', 'Anda tidak memiliki izin untuk menghapus kelas ini.');
    header('Location: ' . get_base_url() . '/classes/index.php');
    exit;
}

$pdo->prepare('DELETE FROM classes WHERE id = ?')->execute([$id]);
flash('success', 'Kelas dihapus.');
header('Location: ' . get_base_url() . '/classes/index.php');
exit;