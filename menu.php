<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/helpers.php';

$notifikasi = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_menu'])) {
    $nama      = trim($_POST['nama'] ?? '');
    $kategori  = trim($_POST['kategori'] ?? 'Minuman');
    $harga     = (float) ($_POST['harga'] ?? 0);
    $stok      = (int) ($_POST['stok'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');

    if ($nama === '' || $harga <= 0) {
        $notifikasi = ['jenis' => 'error', 'pesan' => 'Nama dan harga wajib diisi dengan benar.'];
    } else {
        $stmt = $pdo->prepare('INSERT INTO menu (nama, kategori, harga, stok, tersedia, deskripsi) VALUES (?, ?, ?, ?, 1, ?)');
        $stmt->execute([$nama, $kategori, $harga, $stok, $deskripsi]);
        $notifikasi = ['jenis' => 'success', 'pesan' => 'Menu "' . e($nama) . '" berhasil ditambahkan.'];
    }
}

$menu   = ambil_menu($pdo);
$active = 'menu';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Menu — Café Setara</title>
  <link rel="stylesheet" href="css/tokens.css">
  <link rel="stylesheet" href="css/base.css">
  <link rel="stylesheet" href="css/components.css">
  <link rel="stylesheet" href="css/pages.css">
</head>
<body>

<?php include __DIR__ . '/includes/header.php'; ?>

<main class="container app-shell">
  <aside class="sidebar" aria-label="Navigasi aplikasi">
    <a class="sidebar__link" href="app.php">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
      Ringkasan
    </a>
    <a class="sidebar__link" href="kasir.php">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16v12H4zM8 7V5h8v2M9 12h6"/></svg>
      Kasir
    </a>
    <a class="sidebar__link" href="menu.php" aria-current="page">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h10"/></svg>
      Menu
    </a>
  </aside>

  <div>
    <div class="page-head">
      <div>
        <span class="caption text-muted">Daftar minuman</span>
        <h1>Menu kafe</h1>
      </div>
      <button class="btn btn--primary" type="button" id="bukaForm">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
        Tambah menu
      </button>
    </div>

    <?php if ($notifikasi): ?>
      <div class="card notifikasi notifikasi--<?= $notifikasi['jenis'] ?> mb-6">
        <p><?= $notifikasi['pesan'] ?></p>
      </div>
    <?php endif; ?>

    <div class="card mb-6" id="formTambah" hidden>
      <h2 class="card__title">Tambah menu baru</h2>
      <form class="stack mt-6" action="menu.php" method="post">
        <div class="field">
          <label class="field__label" for="nama">Nama menu</label>
          <input class="input" type="text" id="nama" name="nama" required placeholder="Misalnya: Matcha Latte">
        </div>
        <div class="grid grid--2">
          <div class="field">
            <label class="field__label" for="kategori">Kategori</label>
            <select class="select" id="kategori" name="kategori">
              <option value="Minuman">Minuman</option>
              <option value="Makanan">Makanan</option>
              <option value="Snack">Snack</option>
            </select>
          </div>
          <div class="field">
            <label class="field__label" for="harga">Harga (Rp)</label>
            <input class="input" type="number" id="harga" name="harga" min="1" step="1" required placeholder="27000">
          </div>
        </div>
        <div class="field">
          <label class="field__label" for="stok">Stok awal</label>
          <input class="input" type="number" id="stok" name="stok" min="0" value="0" required>
        </div>
        <div class="field">
          <label class="field__label" for="deskripsi">Deskripsi</label>
          <textarea class="textarea" id="deskripsi" name="deskripsi" placeholder="Ceritakan singkat tentang menu ini"></textarea>
        </div>
        <div class="hero__actions">
          <button class="btn btn--primary" type="submit" name="tambah_menu" value="1">Simpan menu</button>
          <button class="btn btn--ghost" type="button" id="tutupForm">Batal</button>
        </div>
      </form>
    </div>

    <div class="grid grid--3">
      <?php if (empty($menu)): ?>
        <article class="card"><p class="card__text">Belum ada menu. Tambahkan menu pertama Anda.</p></article>
      <?php else: ?>
        <?php foreach ($menu as $m): ?>
          <?php $gambar = $m['gambar'] ?: 'assets/img/menu-americano.svg'; ?>
          <article class="card menu-card">
            <img class="menu-card__img" src="<?= e($gambar) ?>" alt="<?= e($m['nama']) ?>">
            <div class="menu-card__body">
              <div class="menu-card__head">
                <h3 class="card__title"><?= e($m['nama']) ?></h3>
                <span class="menu-card__price"><?= rupiah((float) $m['harga']) ?></span>
              </div>
              <p class="card__text"><?= e($m['deskripsi'] ?: 'Menu Café Setara.') ?></p>
              <div class="menu-card__foot">
                <?php if (!$m['tersedia']): ?>
                  <span class="badge badge--neutral">Tidak tersedia</span>
                <?php elseif ((int) $m['stok'] <= 10): ?>
                  <span class="badge badge--accent">Stok <?= (int) $m['stok'] ?></span>
                <?php else: ?>
                  <span class="badge badge--success">Tersedia</span>
                <?php endif; ?>
                <a class="btn btn--secondary btn--sm" href="kasir.php?menu=<?= (int) $m['id'] ?>">Pilih</a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</main>

<script>
  const form = document.getElementById('formTambah');
  document.getElementById('bukaForm').addEventListener('click', () => {
    form.hidden = !form.hidden;
    if (!form.hidden) document.getElementById('nama').focus();
  });
  document.getElementById('tutupForm').addEventListener('click', () => { form.hidden = true; });
  <?php if ($notifikasi && $notifikasi['jenis'] === 'success'): ?>
    form.hidden = true;
  <?php endif; ?>
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
