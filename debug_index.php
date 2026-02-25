<?php
// Debug version of index.php to troubleshoot loading issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Prevent session conflicts during debugging
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Simple output to check if the file loads
echo "<h1>Debug: Index.php is loading</h1>";
echo "<p>If you see this, the PHP file is processing.</p>";

// Show if user is logged in
if (isset($_SESSION['user'])) {
    echo "<p>User is logged in: " . htmlspecialchars($_SESSION['user']['name'] ?? 'Unknown') . "</p>";
} else {
    echo "<p>No user is logged in</p>";
}

// Include header after debugging output
include __DIR__ . '/partials/header.php';
?>
<style>
  body {
    background-image: none !important;
    background-color: #fff !important;
    color: #212529 !important; /* Dark text for white background */
  }
  body::before {
    display: none !important;
  }
</style>
<div class="hero mb-4">
  <div>
    <h1 class="display-5 fw-bold">Selamat Datang di <?= app_name(); ?></h1>
    <p class="lead">Gym modern dengan program latihan terstruktur, pelatih berpengalaman, dan AI Coach untuk memaksimalkan hasil Anda.</p>
    <?php if (!is_logged_in()): ?>
      <a href="/FozGym/register.php" class="btn btn-success btn-lg me-2">Daftar Member</a>
      <a href="/FozGym/login.php" class="btn btn-outline-light btn-lg">Login</a>
    <?php else: ?>
      <a href="/FozGym/dashboard.php" class="btn btn-primary btn-lg">Ke Dashboard</a>
    <?php endif; ?>
  </div>
</div>

<div class="row g-3">
  <div class="col-md-4">
    <a href="/FozGym/classes/index.php" class="card-link">
    <div class="card h-100">
      <div class="card-body">
        <div class="card-icon"><img src="img/1.png" alt="Program Kelas Logo" class="card-icon-img"></div>
        <h5 class="card-title">Program Kelas</h5>
        <p class="card-text">Pilates, HIIT, dan Strength Training dirancang untuk berbagai level.</p>
      </div>
    </div>
    </a>
  </div>
  <div class="col-md-4">
    <a href="/FozGym/trainers.php" class="card-link">
    <div class="card h-100">
      <div class="card-body">
        <div class="card-icon"><img src="img/2.png" alt="Pelatih Berpengalaman Logo" class="card-icon-img"></div>
        <h5 class="card-title">Pelatih Berpengalaman</h5>
        <p class="card-text">Pelatih bersertifikat siap mendampingi perjalanan kebugaran Anda.</p>
      </div>
    </div>
    </a>
  </div>
  <div class="col-md-4">
    <a href="/FozGym/ai.php" class="card-link">
    <div class="card h-100">
      <div class="card-body">
        <div class="card-icon"><img src="img/3.png" alt="AI Coach Logo" class="card-icon-img"></div>
        <h5 class="card-title">AI Coach</h5>
        <p class="card-text">Dapatkan saran latihan personal dengan fitur AI terintegrasi.</p>
      </div>
    </div>
    </a>
  </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>