<?php
/** Header +Navbar bersama. Variabel $active: home|app|kasir|menu */
$active = $active ?? '';
?>
<header class="navbar">
  <div class="container navbar__inner">
    <a class="navbar__logo" href="index.php">
      <span class="navbar__logo-mark" aria-hidden="true">CS</span>
      Café Setara
    </a>
    <nav class="navbar__menu" aria-label="Navigasi utama">
      <a class="navbar__link" href="index.php#fitur">Fitur</a>
      <a class="navbar__link" href="index.php#alur">Alur kerja</a>
      <a class="navbar__link" href="menu.php" <?= $active === 'menu' ? 'aria-current="page"' : '' ?>>Menu</a>
      <a class="navbar__link" href="index.php#faq">FAQ</a>
    </nav>
    <div class="navbar__actions">
      <a class="btn btn--primary" href="app.php">Masuk aplikasi</a>
    </div>
  </div>
</header>
