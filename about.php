<?php
require_once __DIR__ . '/config.php';
include __DIR__ . '/partials/header.php';
?>
<style>
  body {
    background-image: none !important;
    background-color: #fff !important;
    color: #212529 !important;
  }
  body::before {
    display: none !important;
  }
  .about-hero {
    background: linear-gradient(rgba(0,0,0,.6), rgba(0,0,0,.6)), url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=1200&auto=format&fit=crop');
    background-size: cover;
    background-position: center;
    color: #fff;
    border-radius: .75rem;
    min-height: 250px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    margin-bottom: 2rem;
  }
  .about-section {
    background-color: #f8f9fa;
    border-radius: .75rem;
    padding: 2rem;
    margin-bottom: 1.5rem;
  }
  .about-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background-color: #0d6efd;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin: 0 auto 1rem auto;
  }
</style>

<div class="about-hero">
  <div>
    <h1 class="display-5 fw-bold">Tentang FozGym</h1>
    <p class="lead">Sejarah dan Latar Belakang Kami</p>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-8 mx-auto">
    <div class="about-section">
      <div class="about-icon">🏋️</div>
      <h3 class="text-center mb-4">Sejarah FozGym</h3>
      <p class="lead text-center mb-4">
        FozGym didirikan dengan satu misi sederhana: <strong>menjadikan kebugaran dapat diakses oleh semua orang</strong>.
      </p>
      <p>
        Berawal dari sebuah studio kecil dengan peralatan dasar, FozGym kini telah berkembang menjadi pusat kebugaran modern yang dilengkapi dengan fasilitas lengkap dan teknologi terkini. Kami percaya bahwa setiap orang berhak mendapatkan akses ke program latihan yang terstruktur, pelatih berpengalaman, dan panduan yang tepat untuk mencapai tujuan kebugaran mereka.
      </p>
      <p>
        Seiring berjalannya waktu, FozGym terus berinovasi dengan mengintegrasikan <strong>AI Coach</strong> ke dalam layanan kami, memberikan saran latihan personal yang disesuaikan dengan kebutuhan dan tujuan setiap member.
      </p>
    </div>

    <div class="about-section">
      <div class="about-icon">🎯</div>
      <h3 class="text-center mb-4">Visi & Misi</h3>
      <div class="row g-3">
        <div class="col-md-6">
          <div class="card h-100">
            <div class="card-body text-center">
              <h5 class="card-title">Visi</h5>
              <p class="card-text">Menjadi pusat kebugaran terdepan yang menginspirasi masyarakat untuk hidup sehat dan bugar.</p>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card h-100">
            <div class="card-body text-center">
              <h5 class="card-title">Misi</h5>
              <ul class="text-start mb-0">
                <li>Menyediakan fasilitas latihan modern dan lengkap</li>
                <li>Memberikan panduan dari pelatih bersertifikat</li>
                <li>Menawarkan program untuk berbagai level kebugaran</li>
                <li>Mengintegrasikan teknologi AI untuk hasil optimal</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="about-section">
      <div class="about-icon">💪</div>
      <h3 class="text-center mb-4">Mengapa Memilih FozGym?</h3>
      <div class="row g-3">
        <div class="col-md-4">
          <div class="card h-100 text-center">
            <div class="card-body">
              <img src="img/1.png" alt="Program Kelas" class="card-icon-img mb-3" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover;">
              <h6 class="card-title">Program Kelas Lengkap</h6>
              <p class="card-text small">Pilates, HIIT, dan Strength Training untuk berbagai level.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card h-100 text-center">
            <div class="card-body">
              <img src="img/2.png" alt="Pelatih" class="card-icon-img mb-3" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover;">
              <h6 class="card-title">Pelatih Berpengalaman</h6>
              <p class="card-text small">Pelatih bersertifikat siap mendampingi perjalanan kebugaran Anda.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card h-100 text-center">
            <div class="card-body">
              <img src="img/3.png" alt="AI Coach" class="card-icon-img mb-3" style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover;">
              <h6 class="card-title">AI Coach Terintegrasi</h6>
              <p class="card-text small">Saran latihan personal dengan fitur AI terintegrasi.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="text-center mt-4">
      <?php if (!is_logged_in()): ?>
        <a href="<?= get_base_url(); ?>/register.php" class="btn btn-success btn-lg me-2">Daftar Member Sekarang</a>
        <a href="<?= get_base_url(); ?>/login.php" class="btn btn-outline-primary btn-lg">Login</a>
      <?php else: ?>
        <a href="<?= get_base_url(); ?>/dashboard.php" class="btn btn-primary btn-lg">Ke Dashboard</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
