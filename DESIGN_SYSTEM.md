# Design System — Café Setara

Satu sumber kebenaran untuk seluruh UI. **Dilarang hardcode warna, font-family, ukuran font, spacing, atau radius di luar file token.**

## Struktur file

```
config/
├── database.php    ← koneksi PDO MySQL (setara_coffee)
└── schema.sql      ← tabel menu + transaksi + item_transaksi + data awal
includes/
├── helpers.php     ← rupiah(), e(), query, statistik, nomor transaksi
├── header.php      ← Navbar reusable
└── footer.php      ← Footer reusable
css/
├── tokens.css      ← SATU sumber kebenaran (semua variabel + @import font)
├── base.css        ← reset, tipografi dasar, container, utilitas teks
├── components.css  ← Button, Badge, Card, Input/Select/Textarea, Navbar, Section, Footer
└── pages.css       ← layout spesifik halaman (hero, sidebar, tabel, kartu menu)
assets/img/         ← ilustrasi SVG lokal
index.php / app.php / kasir.php / menu.php   ← halaman aplikasi
```

File `.html` versi statis tetap disimpan sebagai referensi UI murni.
Halaman `.php` memakai include header/footer yang sama persis dengan komponen CSS.

## Menjalankan secara lokal

1. Impor `config/schema.sql` ke MySQL (`setara_coffee`).
2. Sesuaikan kredensial di `config/database.php` (`$dbUser`, `$dbPass`).
3. Jalankan lewat server PHP: `php -S localhost:8000` lalu buka `http://localhost:8000/index.php`.

Fitur: tambah menu, keranjang kasir (session), bayar & simpan transaksi (transaction + update stok), dashboard statistik hari ini.

## Alur request kasir
`kasir.php` → POST `tambah` (masuk `$_SESSION['keranjang']`) → POST `bayar` →
`config/database.php` + helper `buat_nomor_transaksi()` → INSERT transaksi + item (transaction) → stok berkurang → keranjang dikosongkan.

Urutan `<link>` di semua halaman **wajib**: `tokens.css` → `base.css` → `components.css` → `pages.css`.

## Font

Dimuat **hanya sekali**, lewat `@import` di `tokens.css`:

- **Heading:** Fraunces (serif elegan), weight 500–600 — fallback `Georgia, serif`
- **Body/UI:** Inter, weight 400/500/600 — fallback `system-ui, sans-serif`

Tidak ada font ketiga.

## Token (ringkasan)

| Kategori | Token |
|---|---|
| Warna | `--color-bg`, `--color-surface`, `--color-surface-muted`, `--color-primary`, `--color-primary-hover`, `--color-primary-contrast`, `--color-text`, `--color-text-muted`, `--color-border`, `--color-accent`, `--color-success-bg`, `--color-success-text`, `--color-danger`, `--color-warning` |
| Font | `--font-heading`, `--font-body` |
| Ukuran font | `--fs-h1` (clamp 2.5–3.75rem), `--fs-h2` (2rem), `--fs-h3` (1.5rem), `--fs-body` (1rem), `--fs-small` (0.875rem), `--fs-caption` (0.75rem) |
| Spacing | `--space-1` … `--space-24` (4/8/12/16/24/32/48/64/96px) |
| Radius | `--radius-sm` (8), `--radius-md` (12), `--radius-lg` (20), `--radius-pill` (999) |
| Shadow | `--shadow-sm`, `--shadow-md` |
| Layout | `--container-max` (1200px), `--container-pad` (24px), `--container-pad-mobile` (16px), `--navbar-height` (64px) |
| Focus | `--focus-ring` (2px accent, offset 2px) |

Breakpoint: 640 / 768 / 1024 / 1280px (mobile-first).

## Contoh pemakaian komponen

### Button
```html
<a class="btn btn--primary" href="app.html">Masuk aplikasi</a>
<button class="btn btn--secondary" type="button">Batal</button>
<button class="btn btn--ghost" type="button">Lewati</button>
<button class="btn btn--accent btn--sm" type="button">Promo</button>
<!-- ikon opsional di kiri: svg dengan stroke="currentColor" -->
```
Varian: `btn--primary`, `btn--secondary`, `btn--ghost`, `btn--accent`. Modifier: `btn--sm`, `btn--block`. Tinggi 44px, radius-md, ada state hover/focus/disabled.

### Badge
```html
<span class="badge badge--success">Satu ruang, untuk setiap hari</span>
<span class="badge badge--neutral">Meja 01</span>
<span class="badge badge--accent">Perlu diperbarui</span>
```

### Card
```html
<article class="card">
  <h3 class="card__title">Kasir cepat</h3>
  <p class="card__text">Catat pesanan dan hitung total.</p>
</article>
<!-- tambahkan card--elevated untuk shadow lebih kuat, card--block untuk gambar penuh -->
```

### Input / Select / Textarea
```html
<div class="field">
  <label class="field__label" for="produk">Produk</label>
  <select class="select" id="produk" name="produk">…</select>
</div>
<input class="input" type="text" placeholder="Cari menu">
<textarea class="textarea" placeholder="Catatan"></textarea>
```

### Navbar
```html
<header class="navbar">
  <div class="container navbar__inner">
    <a class="navbar__logo" href="index.html">
      <span class="navbar__logo-mark" aria-hidden="true">CS</span> Café Setara
    </a>
    <nav class="navbar__menu">
      <a class="navbar__link" href="#fitur">Fitur</a> …
    </nav>
    <div class="navbar__actions"><a class="btn btn--primary" href="app.html">Masuk aplikasi</a></div>
  </div>
</header>
```

### Section
```html
<section class="section section--muted" id="fitur">
  <div class="container">
    <div class="section__head section__head--center">
      <h2 class="section__title">Judul</h2>
      <p class="section__subtitle">Subteks muted.</p>
    </div>
    …
  </div>
</section>
```

### Footer
```html
<footer class="footer">
  <div class="container footer__inner">
    <div><p class="footer__tagline">Café Setara</p><p class="footer__text">Satu ruang, untuk setiap hari.</p></div>
    <nav class="footer__nav"><a href="menu.html">Menu</a></nav>
  </div>
</footer>
```

## Aturan pengembangan berikutnya

1. Butuh warna baru? Tambahkan token di `tokens.css` dulu, lalu pakai `var(--token)`.
2. Semua tombol utama **wajib** pakai `.btn.btn--primary` — jangan menulis gaya tombol sendiri.
3. Ikon: satu library (di sini inline SVG) dengan `stroke="currentColor"`, stroke-width 1.5–2.
4. Gambar/hero: `border-radius: var(--radius-lg)` + `box-shadow: var(--shadow-md)`.
5. Teks deskripsi selalu `.text-muted` / `--color-text-muted`; heading selalu warna `--color-primary`.
6. Kontras minimal WCAG AA — cek sebelum menambah token warna baru.
7. Dark mode tidak diperlukan.
