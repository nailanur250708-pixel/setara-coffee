<?php
/**
 * Helper bersama — Café Setara
 */

declare(strict_types=1);

function rupiah(float $angka): string
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function e(string $nilai): string
{
    return htmlspecialchars($nilai, ENT_QUOTES, 'UTF-8');
}

function ambil_menu(PDO $pdo): array
{
    return $pdo->query('SELECT * FROM menu ORDER BY id ASC')->fetchAll();
}

function ambil_menu_tersedia(PDO $pdo): array
{
    return $pdo->query('SELECT * FROM menu WHERE tersedia = 1 AND stok > 0 ORDER BY nama ASC')->fetchAll();
}

function ambil_transaksi_terbaru(PDO $pdo, int $batas = 5): array
{
    $stmt = $pdo->prepare('SELECT * FROM transaksi ORDER BY dibuat_pada DESC, id DESC LIMIT :batas');
    $stmt->bindValue(':batas', $batas, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function statistik_hari_ini(PDO $pdo): array
{
    $stmt = $pdo->query("
        SELECT
            COALESCE(SUM(total), 0) AS penjualan,
            COUNT(*) AS transaksi
        FROM transaksi
        WHERE DATE(dibuat_pada) = CURDATE()
    ");
    $result = $stmt->fetch();

    $terlaris = $pdo->query("
        SELECT nama_menu, SUM(jumlah) AS qty
        FROM item_transaksi it
        JOIN transaksi t ON t.id = it.transaksi_id
        WHERE DATE(t.dibuat_pada) = CURDATE()
        GROUP BY nama_menu
        ORDER BY qty DESC
        LIMIT 1
    ")->fetch();

    $menipis = $pdo->query('SELECT COUNT(*) FROM menu WHERE stok > 0 AND stok <= 10')->fetchColumn();

    return [
        'penjualan' => (float) $result['penjualan'],
        'transaksi' => (int) $result['transaksi'],
        'terlaris'  => $terlaris ?: null,
        'menipis'   => (int) $menipis,
    ];
}

function buat_nomor_transaksi(PDO $pdo): string
{
    $tanggal = date('ymd');
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM transaksi WHERE DATE(dibuat_pada) = CURDATE()");
    $stmt->execute();
    $urutan = (int) $stmt->fetchColumn() + 1;
    return 'TRX-' . $tanggal . '-' . str_pad((string) $urutan, 3, '0', STR_PAD_LEFT);
}
