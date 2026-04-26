<?php
require_once '../helpers/auth.php';
require_once '../config/database.php';
require_once '../helpers/inventory.php';

checkLogin();
checkRole(['owner', 'pegawai']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/stock-out.php');
    exit;
}

$id = (int) ($_POST['id_barang'] ?? 0);
$jumlah = (int) ($_POST['jumlah'] ?? 0);
$alasan = trim($_POST['alasan'] ?? 'kadaluarsa');

if ($id <= 0 || $jumlah <= 0) {
    redirectWithMessage('../pages/stock-out.php', 'Barang dan jumlah harus valid.', 'error');
}

$allowedAlasan = ['kadaluarsa', 'rusak'];
if (!in_array($alasan, $allowedAlasan, true)) {
    redirectWithMessage('../pages/stock-out.php', 'Alasan pengurangan tidak valid.', 'error');
}

$selectStmt = mysqli_prepare($conn, 'SELECT nama_barang, stok, harga_beli FROM barang WHERE id_barang = ? LIMIT 1');
mysqli_stmt_bind_param($selectStmt, 'i', $id);
mysqli_stmt_execute($selectStmt);
$result = mysqli_stmt_get_result($selectStmt);
$barang = mysqli_fetch_assoc($result);
mysqli_stmt_close($selectStmt);

if (!$barang) {
    redirectWithMessage('../pages/stock-out.php', 'Barang tidak ditemukan.', 'error');
}

if ((int) $barang['stok'] < $jumlah) {
    redirectWithMessage('../pages/stock-out.php', 'Stok tidak cukup untuk dikurangi.', 'error');
}

$updateStmt = mysqli_prepare($conn, 'UPDATE barang SET stok = stok - ? WHERE id_barang = ?');
mysqli_stmt_bind_param($updateStmt, 'ii', $jumlah, $id);
mysqli_stmt_execute($updateStmt);
mysqli_stmt_close($updateStmt);

logInventoryActivity(
    $conn,
    $id,
    $barang['nama_barang'],
    $alasan,
    $jumlah,
    (float) $barang['harga_beli'],
    0,
    (float) $barang['harga_beli'] * $jumlah,
    'Pengurangan stok karena ' . $alasan,
    $_SESSION['username'] ?? '-'
);

redirectWithMessage('../pages/stok.php', 'Stok berhasil dikurangi karena ' . $alasan . '.', 'success');
