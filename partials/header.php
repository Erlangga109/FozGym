<?php
require_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= app_name(); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= get_base_url(); ?>/assets/style.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="<?= get_base_url(); ?>/">
      <img src="<?= get_base_url(); ?>/img/logo.png" alt="FozGym Logo" height="40" class="d-inline-block align-text-top me-2">FozGym
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="<?= get_base_url(); ?>/about.php">Tentang</a></li>
        <?php if (is_logged_in()): ?>
          <li class="nav-item"><a class="nav-link" href="<?= get_base_url(); ?>/dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= get_base_url(); ?>/classes/index.php">Kelas</a></li>
          <?php if (is_trainer()): ?>
            <li class="nav-item"><a class="nav-link" href="<?= get_base_url(); ?>/classes/my_classes.php">Kelas Saya</a></li>
          <?php endif; ?>
          <?php if (is_owner()): ?>
            <li class="nav-item"><a class="nav-link" href="<?= get_base_url(); ?>/members/index.php">Member</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= get_base_url(); ?>/enrollments/index.php">Pendaftaran</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="<?= get_base_url(); ?>/ai.php">AI Coach</a></li>
          <?php if (is_customer() || is_trainer()): ?>
            <li class="nav-item"><a class="nav-link" href="<?= get_base_url(); ?>/schedules/index.php">Jadwal Latihan</a></li>
            <?php if (is_customer()): ?>
              <li class="nav-item"><a class="nav-link" href="<?= get_base_url(); ?>/classes/my.php">Kelas Saya</a></li>
            <?php endif; ?>
          <?php endif; ?>
          <?php if (is_owner()): ?>
            <li class="nav-item"><a class="nav-link" href="<?= get_base_url(); ?>/users/index.php">User</a></li>
          <?php endif; ?>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav">
        <?php if (!is_logged_in()): ?>
          <li class="nav-item"><a class="nav-link" href="<?= get_base_url(); ?>/login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= get_base_url(); ?>/register.php">Daftar</a></li>
        <?php else: ?>
          <li class="nav-item"><span class="navbar-text me-2">Halo, <?= htmlspecialchars(current_user()['name']); ?> (<?= htmlspecialchars(current_user()['role']); ?>)</span></li>
          <li class="nav-item"><a class="nav-link" href="<?= get_base_url(); ?>/logout.php">Logout</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container py-4">
<?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= htmlspecialchars($msg); ?></div><?php endif; ?>
<?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= htmlspecialchars($msg); ?></div><?php endif; ?>
