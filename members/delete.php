<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../db.php';
require_login();
require_roles(['owner','trainer']);
$pdo = get_pdo();
$id = (int)($_GET['id'] ?? 0);
$pdo->prepare('DELETE FROM members WHERE id = ?')->execute([$id]);
flash('success', 'Member dihapus.');
header('Location: /FozGym/members/index.php');
exit;