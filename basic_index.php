<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FozGym - Basic Version</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="<?= get_base_url(); ?>/">🏋️ FozGym</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" href="<?= get_base_url(); ?>/login.php">Login</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= get_base_url(); ?>/register.php">Daftar</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container py-4">
  <div class="hero mb-4">
    <div>
      <h1 class="display-5 fw-bold">Selamat Datang di FozGym</h1>
      <p class="lead">Gym modern dengan program latihan terstruktur, pelatih berpengalaman, dan AI Coach untuk memaksimalkan hasil Anda.</p>
      <a href="<?= get_base_url(); ?>/register.php" class="btn btn-success btn-lg me-2">Daftar Member</a>
      <a href="<?= get_base_url(); ?>/login.php" class="btn btn-outline-light btn-lg">Login</a>
    </div>
  </div>
  
  <p>Website sedang dalam perbaikan. Jika Anda mengalami masalah loading, silakan coba:</p>
  <ul>
    <li>Akses <a href="<?= get_base_url(); ?>/simple_test.php">simple_test.php</a> untuk mengecek fungsi dasar</li>
    <li>Akses <a href="<?= get_base_url(); ?>/check_db_exists.php">check_db_exists.php</a> untuk mengecek database</li>
    <li>Akses <a href="<?= get_base_url(); ?>/init_db.php">init_db.php</a> untuk inisialisasi tabel</li>
  </ul>
</div>

<footer class="text-center py-3 small">
  <div>© <?= date('Y'); ?> FozGym — Fitness for Everyone</div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>