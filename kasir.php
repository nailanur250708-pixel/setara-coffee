<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/helpers.php';

$menu        = ambil_menu_tersedia($pdo);
$notifikasi  = null;
$selectedId  = isset($_GET['menu']) ? (int) $_GET['menu'] : 0;

// Keranjang disimpan di session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['keranjang']) || !is_array($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// Tambah item ke keranjang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $menuId  = (int) ($_POST['menu_id'] ?? 0);
    $jumlah  = max(1, (int) ($_POST['jumlah'] ?? 1));
    $stmt    = $pdo->prepare('SELECT * FROM menu WHERE id = ? AND tersedia = 1');
    $stmt->execute([$menuId]);
    $item    = $stmt->fetch();

    if ($item) {
        if (isset($_SESSION['keranjang'][$menuId])) {
            $_SESSION['keranjang'][$menuId]['jumlah'] += $jumlah;
        } else {
            $_SESSION['keranjang'][$menuId] = [
                'id'     => (int) $item['id'],
                'nama'   => $item['nama'],
                'harga'  => (float) $item['harga'],
                'jumlah' => $jumlah,
            ];
        }
        $notifikasi = ['jenis' => 'success', 'pesan' => e($item['nama']) . ' ditambahkan ke pesanan.'];
    } else {
        $notifikasi = ['jenis' => 'error', 'pesan' => 'Menu tidak ditemukan atau tidak tersedia.'];
    }
}

// Ubah jumlah / hapus item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi_item'])) {
    $menuId = (int) ($_POST['menu_id'] ?? 0);
    if ($_POST['aksi_item'] === 'hapus') {
        unset($_SESSION['keranjang'][$menuId]);
    } elseif ($_POST['aksi_item'] === 'kurang' && isset($_SESSION['keranjang'][$menuId])) {
        $_SESSION['keranjang'][$menuId]['jumlah']--;
        if ($_SESSION['keranjang'][$menuId]['jumlah'] < 1) {
            unset($_SESSION['keranjang'][$menuId]);
        }
    } elseif ($_POST['aksi_item'] === 'tambah' && isset($_SESSION['keranjang'][$menuId])) {
        $_SESSION['keranjang'][$menuId]['jumlah']++;
    }
}

// Kosongkan keranjang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kosongkan'])) {
    $_SESSION['keranjang'] = [];
}

// Bayar / simpan transaksi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bayar'])) {
    if (empty($_SESSION['keranjang'])) {
        $notifikasi = ['jenis' => 'error', 'pesan' => 'Keranjang masih kosong.'];
    } else {
        $meja    = trim($_POST['meja'] ?? '');
        $catatan = trim($_POST['catatan'] ?? '');
        $subtotal = 0;
        foreach ($_SESSION['keranjang'] as $row) {
            $subtotal += $row['harga'] * $row['jumlah'];
        }
        $pajak = round($subtotal * 0.10, 2);
        $total = $subtotal + $pajak;
        $nomor = buat_nomor_transaksi($pdo);

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare('INSERT INTO transaksi (nomor, meja, subtotal, pajak, total, catatan) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$nomor, $meja ?: null, $subtotal, $pajak, $total, $catatan ?: null]);
            $transaksiId = (int) $pdo->lastInsertId();

            $stmtItem = $pdo->prepare('INSERT INTO item_transaksi (transaksi_id, menu_id, nama_menu, harga_satuan, jumlah, subtotal) VALUES (?, ?, ?, ?, ?, ?)');
            $stmtStok = $pdo->prepare('UPDATE menu SET stok = GREATEST(0, stok - ?) WHERE id = ?');
            foreach ($_SESSION['keranjang'] as $row) {
                $sub = $row['harga'] * $row['jumlah'];
                $stmtItem->execute([$transaksiId, $row['id'], $row['nama'], $row['harga'], $row['jumlah'], $sub]);
                $stmtStok->execute([$row['jumlah'], $row['id']]);
            }

            $pdo->commit();
            $_SESSION['keranjang'] = [];
            $notifikasi = ['jenis' => 'success', 'pesan' => 'Transaksi ' . e($nomor) . ' berhasil disimpan. Total ' . rupiah($total) . '.'];
        } catch (PDOException $e) {
            $pdo->rollBack();
            $notifikasi = ['jenis' => 'error', 'pesan' => 'Gagal menyimpan transaksi. Silakan coba lagi.'];
        }
    }
}

// Hitung ulang ringkasan
$subtotal = 0;
foreach ($_SESSION['keranjang'] as $row) {
    $subtotal += $row['harga'] * $row['jumlah'];
}
$pajak = round($subtotal * 0.10, 2);
$total = $subtotal + $pajak;
$active = 'kasir';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kasir — Café Setara</title>
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
    <a class="sidebar__link" href="kasir.php" aria-current="page">
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
        <span class="caption text-muted">Transaksi</span>
        <h1>Kasir</h1>
      </div>
      <span class="badge badge--neutral">Meja <input class="input input--inline" type="text" name="meja" form="formBayar" value="01" size="3" aria-label="Nomor meja"></span>
    </div>

    <?php if ($notifikasi): ?>
      <div class="card notifikasi notifikasi--<?= $notifikasi['jenis'] ?> mb-6">
        <p><?= $notifikasi['pesan'] ?></p>
      </div>
    <?php endif; ?>

    <div class="grid cols-1">
      <article class="card">
        <h2 class="card__title">Input pesanan</h2>
        <form class="stack mt-6" action="kasir.php" method="post">
          <input type="hidden" name="tambah" value="1">
          <div class="field">
            <label class="field__label" for="menu_id">Produk</label>
            <select class="select" id="menu_id" name="menu_id" required>
              <option value="">Pilih menu</option>
              <?php foreach ($menu as $m): ?>
                <option value="<?= (int) $m['id'] ?>" <?= $selectedId === (int) $m['id'] ? 'selected' : '' ?>>
                  <?= e($m['nama']) ?> — <?= rupiah((float) $m['harga']) ?> (stok <?= (int) $m['stok'] ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="field">
            <label class="field__label" for="jumlah">Jumlah</label>
            <input class="input" type="number" id="jumlah" name="jumlah" min="1" value="1" required>
          </div>
          <div>
            <button class="btn btn--primary" type="submit">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
              Tambah ke pesanan
            </button>
          </div>
        </form>
      </article>

      <article class="card">
        <h2 class="card__title">Pesanan saat ini</h2>
        <div class="table-wrap mt-6">
          <table class="table">
            <thead>
              <tr><th scope="col">Produk</th><th scope="col">Jumlah</th><th scope="col" class="table__num">Subtotal</th><th scope="col"></th></tr>
            </thead>
            <tbody>
              <?php if (empty($_SESSION['keranjang'])): ?>
                <tr><td colspan="4" class="text-muted">Belum ada pesanan. Pilih menu di atas.</td></tr>
              <?php else: ?>
                <?php foreach ($_SESSION['keranjang'] as $id => $row): ?>
                  <tr>
                    <td><?= e($row['nama']) ?></td>
                    <td>
                      <form action="kasir.php" method="post" class="qty">
                        <input type="hidden" name="menu_id" value="<?= (int) $id ?>">
                        <button class="qty__btn" type="submit" name="aksi_item" value="kurang" aria-label="Kurangi <?= e($row['nama']) ?>">&minus;</button>
                        <span><?= (int) $row['jumlah'] ?></span>
                        <button class="qty__btn" type="submit" name="aksi_item" value="tambah" aria-label="Tambah <?= e($row['nama']) ?>">+</button>
                      </form>
                    </td>
                    <td class="table__num"><?= rupiah($row['harga'] * $row['jumlah']) ?></td>
                    <td>
                      <form action="kasir.php" method="post">
                        <input type="hidden" name="menu_id" value="<?= (int) $id ?>">
                        <button class="btn btn--ghost btn--sm" type="submit" name="aksi_item" value="hapus">Hapus</button>
                      </form>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <form id="formBayar" action="kasir.php" method="post">
          <div class="field mt-6">
            <label class="field__label" for="catatan">Catatan (opsional)</label>
            <textarea class="textarea" id="catatan" name="catatan" placeholder="Misalnya: less sugar"></textarea>
          </div>
          <div class="summary mt-6">
            <div class="summary__row"><span class="text-muted">Subtotal</span><span><?= rupiah($subtotal) ?></span></div>
            <div class="summary__row"><span class="text-muted">Pajak (10%)</span><span><?= rupiah($pajak) ?></span></div>
            <div class="summary__row summary__row--total"><span>Total</span><span><?= rupiah($total) ?></span></div>
          </div>
          <div class="hero__actions mt-6">
            <button class="btn btn--primary" type="submit" name="bayar" value="1" <?= empty($_SESSION['keranjang']) ? 'disabled' : '' ?>>Bayar sekarang</button>
            <button class="btn btn--ghost" type="submit" name="kosongkan" value="1">Kosongkan</button>
          </div>
        </form>
      </article>
    </div>
  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

</body>
</html>
