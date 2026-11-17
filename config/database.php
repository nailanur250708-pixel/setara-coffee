<?php
/**
 * Koneksi database — Café Setara
 * Satu sumber kebenaran untuk koneksi MySQL.
 */

declare(strict_types=1);

$dbHost = 'localhost';
$dbName = 'setara_coffee';
$dbUser = 'root';
$dbPass = '';

$dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Koneksi gagal — Café Setara</title>'
       . '<link rel="stylesheet" href="../css/tokens.css"><link rel="stylesheet" href="../css/base.css"></head>'
       . '<body><main class="container section"><h1>Koneksi database gagal</h1>'
       . '<p>Periksa pengaturan di <code>config/database.php</code> dan pastikan MySQL berjalan.</p>'
       . '<p class="text-muted"><small>' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</small></p>'
       . '</main></body></html>';
    exit;
}
