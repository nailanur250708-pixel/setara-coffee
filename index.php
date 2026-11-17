<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/helpers.php';
$active = 'home';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Café Setara — Kelola Café Setara lebih rapi</title>
  <link rel="stylesheet" href="css/tokens.css">
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/pages.css">
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<main>
  <section class="hero">
    <div class="container hero__inner">
      <div>
        <span class="badge badge--success hero__badge">Satu ruang, untuk setiap hari</span>
        <h1 class="hero__title">Kelola Café Setara lebih rapi.</h1>
        <p class="hero__subtitle">
          Dari pesanan di meja kasir sampai laporan penjualan harian — semua dalam satu tempat yang hangat dan mudah dipakai tim Anda.
        </p>
        <div class="hero__actions">
          <a class="btn btn--primary" href="app.php">
            Mulai sekarang
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
          </a>
          <a class="btn btn--secondary" href="menu.php">Lihat menu</a>
        </div>
      </div>
      <div class="hero__media">
        <img src="assets/img/hero.svg" alt="Barista meracik kopi di Café Setara">
      </div>
    </div>
  </section>

  <section class="section section--muted" id="fitur">
    <div class="container">
      <div class="section__head section__head--center">
        <h2 class="section__title">Semua yang Anda butuhkan</h2>
        <p class="section__subtitle">Alat sederhana yang membantu tim fokus melayani tamu, bukan mengurus tumpukan kertas.</p>
      </div>
      <div class="grid grid--3">
        <article class="card">
          <div class="feature-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18M7 15l4-5 3 3 5-7"/></svg></div>
          <h3 class="card__title">Kasir cepat</h3>
          <p class="card__text">Catat pesanan dan hitung total dalam beberapa ketukan saja.</p>
        </article>
        <article class="card">
          <div class="feature-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16M4 12h16M4 17h10"/><circle cx="18" cy="17" r="3"/></svg></div>
          <h3 class="card__title">Menu teratur</h3>
          <p class="card__text">Kelola daftar minuman, harga, dan ketersediaan di satu tempat.</p>
        </article>
        <article class="card">
          <div class="feature-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18M5 8h14M6 16h12"/><circle cx="12" cy="12" r="9"/></svg></div>
          <h3 class="card__title">Laporan harian</h3>
          <p class="card__text">Lihat ringkasan penjualan tanpa perlu menyusun angka manual.</p>
        </article>
      </div>
    </div>
  </section>

  <section class="section" id="alur">
    <div class="container">
      <div class="section__head">
        <h2 class="section__title">Alur kerja yang mengalir</h2>
        <p class="section__subtitle">Tiga langkah sederhana dari pesanan masuk sampai laporan siap dibaca.</p>
      </div>
      <div class="grid grid--3">
        <div class="step"><span class="step__number">1</span><h3>Pilih menu</h3><p class="card__text">Barista memilih minuman dari daftar menu yang sudah tersedia.</p></div>
        <div class="step"><span class="step__number">2</span><h3>Catat transaksi</h3><p class="card__text">Kasir mencatat jumlah dan total pembayaran di halaman kasir.</p></div>
        <div class="step"><span class="step__number">3</span><h3>Cek ringkasan</h3><p class="card__text">Pemilik kafe memantau penjualan lewat dashboard aplikasi.</p></div>
      </div>
    </div>
  </section>

  <section class="section section--muted" id="faq">
    <div class="container">
      <div class="section__head"><h2 class="section__title">Pertanyaan yang sering muncul</h2></div>
      <div class="stack">
        <details class="card"><h3>Apakah aplikasi ini perlu dipasang?</h3><p class="card__text">Tidak. Café Setara berjalan langsung di browser, baik di komputer maupun ponsel.</p></details>
        <details class="card"><h3>Bisakah tim menggunakan bersamaan?</h3><p class="card__text">Bisa. Setiap perangkat dapat membuka halaman kasir dan menu yang sama.</p></details>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="cta-band">
        <h2 class="cta-band__title">Siap merapikan kafe Anda?</h2>
        <p class="cta-band__text">Mulai dari satu transaksi pertama hari ini.</p>
        <a class="btn btn--secondary" href="app.php">Masuk aplikasi</a>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
