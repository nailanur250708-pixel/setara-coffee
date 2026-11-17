-- ==========================================================================
-- Schema database — Café Setara
-- Jalankan: mysql -u root -p < config/schema.sql
-- ==========================================================================

CREATE DATABASE IF NOT EXISTS setara_coffee
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE setara_coffee;

-- Daftar menu kafe
CREATE TABLE IF NOT EXISTS menu (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama        VARCHAR(120) NOT NULL,
  kategori    VARCHAR(60)  NOT NULL DEFAULT 'Minuman',
  harga       DECIMAL(10,2) NOT NULL,
  stok        INT UNSIGNED NOT NULL DEFAULT 0,
  tersedia    TINYINT(1)   NOT NULL DEFAULT 1,
  deskripsi   TEXT,
  gambar      VARCHAR(255) NULL,
  dibuat_pada DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Transaksi / pesanan
CREATE TABLE IF NOT EXISTS transaksi (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nomor        VARCHAR(30)  NOT NULL UNIQUE,
  meja         VARCHAR(20)  NULL,
  subtotal     DECIMAL(10,2) NOT NULL DEFAULT 0,
  pajak        DECIMAL(10,2) NOT NULL DEFAULT 0,
  total        DECIMAL(10,2) NOT NULL DEFAULT 0,
  catatan      TEXT,
  dibuat_pada  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Item di dalam transaksi
CREATE TABLE IF NOT EXISTS item_transaksi (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  transaksi_id INT UNSIGNED NOT NULL,
  menu_id      INT UNSIGNED NOT NULL,
  nama_menu    VARCHAR(120) NOT NULL,
  harga_satuan DECIMAL(10,2) NOT NULL,
  jumlah       INT UNSIGNED NOT NULL,
  subtotal     DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (transaksi_id) REFERENCES transaksi(id) ON DELETE CASCADE,
  FOREIGN KEY (menu_id)      REFERENCES menu(id)      ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data awal (seed)
INSERT INTO menu (nama, kategori, harga, stok, tersedia, deskripsi, gambar) VALUES
  ('Kopi Susu',    'Minuman', 27000, 40, 1, 'Espresso dengan susu creamy, favorit sejak hari pertama.', 'assets/img/menu-kopi-susu.svg'),
  ('Americano',    'Minuman', 28000, 35, 1, 'Espresso dipanjangkan dengan air panas, ringan dan bersih.', 'assets/img/menu-americano.svg'),
  ('Cappuccino',   'Minuman', 30000, 8,  1, 'Perpaduan espresso, susu, dan busa lembut di atasnya.', 'assets/img/menu-cappuccino.svg'),
  ('Matcha Latte', 'Minuman', 32000, 20, 1, 'Matcha premium dengan susu, rasa earthy dan lembut.', NULL),
  ('Croissant',    'Makanan', 22000, 12, 1, 'Pastry renyah dengan lapisan mentega.', NULL);
