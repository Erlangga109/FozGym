<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_login();
require_roles(['owner']);

$pdo = get_pdo();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    flash('error', 'Metode tidak valid.');
    header('Location: /FozGym/users/index.php');
    exit;
}

$user_id = (int)($_POST['user_id'] ?? 0);
$role = trim($_POST['role'] ?? '');
$allowed = ['owner','trainer','customer'];

if (!$user_id || !in_array($role, $allowed, true)) {
    flash('error', 'Input tidak valid.');
    header('Location: /FozGym/users/index.php');
    exit;
}

$current = current_user();
if ($current && (int)$current['id'] === $user_id && $role !== 'owner') {
    flash('error', 'Anda tidak dapat mengubah peran Anda sendiri dari owner.');
    header('Location: /FozGym/users/index.php');
    exit;
}

$stmt = $pdo->prepare('UPDATE users SET role = ? WHERE id = ?');
$stmt->execute([$role, $user_id]);
flash('success', 'Peran user diperbarui.');
header('Location: /FozGym/users/index.php');
exit;