<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/helpers.php';

$stat     = statistik_hari_ini($pdo);
$terbaru  = ambil_transaksi_terbaru($pdo, 5);
$active   = 'app';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Aplikasi — Café Setara</title>
  <link rel="stylesheet" href="css/tokens.css">
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/pages.css">
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="container app-shell">
  <aside class="sidebar" aria-label="Navigasi aplikasi">
    <a class="sidebar__link" href="app.php" aria-current="page">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
      Ringkasan
    </a>
    <a class="sidebar__link" href="kasir.php">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16v12H4zM8 7V5h8v2M9 12h6"/></svg>
      Kasir
    </a>
    <a class="sidebar__link" href="menu.php">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h10"/></svg>
      Menu
    </a>
  </aside>

  <div>
    <div class="page-head">
      <div>
        <span class="caption text-muted">Hari ini</span>
        <h1>Ringkasan kafe</h1>
      </div>
      <a class="btn btn--primary" href="kasir.php">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
        Transaksi baru
      </a>
    </div>

    <div class="grid grid--4">
      <article class="card">
        <p class="caption text-muted">Penjualan</p>
        <p class="stat-value"><?= rupiah($stat['penjualan']) ?></p>
        <span class="badge badge--success">Hari ini</span>
      </article>
      <article class="card">
        <p class="caption text-muted">Transaksi</p>
        <p class="stat-value"><?= $stat['transaksi'] ?></p>
        <p class="card__text">pesanan hari ini</p>
      </article>
      <article class="card">
        <p class="caption text-muted">Menu terlaris</p>
        <p class="stat-value"><?= $stat['terlaris'] ? e($stat['terlaris']['nama_menu']) : '—' ?></p>
        <p class="card__text"><?= $stat['terlaris'] ? (int) $stat['terlaris']['qty'] . ' terjual' : 'belum ada data' ?></p>
      </article>
      <article class="card">
        <p class="caption text-muted">Stok menipis</p>
        <p class="stat-value"><?= $stat['menipis'] ?> item</p>
        <?php if ($stat['menipis'] > 0): ?>
          <span class="badge badge--accent">Perlu diperbarui</span>
        <?php else: ?>
          <span class="badge badge--neutral">Aman</span>
        <?php endif; ?>
      </article>
    </div>

    <div class="section__head mt-12 mb-6">
      <h2>Aktivitas terbaru</h2>
      <p class="section__subtitle">Transaksi yang baru saja dicatat tim.</p>
    </div>

    <article class="card">
      <div class="table-wrap">
        <table class="table">
          <thead>
            <tr><th scope="col">Waktu</th><th scope="col">No. Transaksi</th><th scope="col">Meja</th><th scope="col" class="table__num">Total</th></tr>
          </thead>
          <tbody>
            <?php if (empty($terbaru)): ?>
              <tr><td colspan="4" class="text-muted">Belum ada transaksi. Mulai dari <a href="kasir.php">halaman kasir</a>.</td></tr>
            <?php else: ?>
              <?php foreach ($terbaru as $t): ?>
                <tr>
                  <td><?= date('H.i', strtotime($t['dibuat_pada'])) ?></td>
                  <td><?= e($t['nomor']) ?></td>
                  <td><?= e($t['meja'] ?: '-') ?></td>
                  <td class="table__num"><?= rupiah((float) $t['total']) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </article>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
