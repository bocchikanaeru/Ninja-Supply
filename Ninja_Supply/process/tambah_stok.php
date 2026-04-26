<?php
require_once '../helpers/auth.php';
require_once '../config/database.php';
require_once '../helpers/inventory.php';

checkLogin();
checkRole(['owner', 'pegawai']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/stock-in.php');
    exit;
}

$id = (int) ($_POST['id_barang'] ?? 0);
$jumlah = (int) ($_POST['jumlah'] ?? 0);

if ($id <= 0 || $jumlah <= 0) {
    redirectWithMessage('../pages/stock-in.php', 'Barang dan jumlah harus valid.', 'error');
}

$selectStmt = mysqli_prepare($conn, 'SELECT nama_barang, harga_beli FROM barang WHERE id_barang = ? LIMIT 1');
mysqli_stmt_bind_param($selectStmt, 'i', $id);
mysqli_stmt_execute($selectStmt);
$result = mysqli_stmt_get_result($selectStmt);
$barang = mysqli_fetch_assoc($result);
mysqli_stmt_close($selectStmt);

if (!$barang) {
    redirectWithMessage('../pages/stock-in.php', 'Barang tidak ditemukan.', 'error');
}

$stmt = mysqli_prepare($conn, 'UPDATE barang SET stok = stok + ? WHERE id_barang = ?');
mysqli_stmt_bind_param($stmt, 'ii', $jumlah, $id);
mysqli_stmt_execute($stmt);
$affectedRows = mysqli_stmt_affected_rows($stmt);
mysqli_stmt_close($stmt);

if ($affectedRows <= 0) {
    redirectWithMessage('../pages/stock-in.php', 'Barang tidak ditemukan atau stok gagal ditambah.', 'error');
}

logInventoryActivity(
    $conn,
    $id,
    $barang['nama_barang'],
    'stock_in',
    $jumlah,
    (float) $barang['harga_beli'],
    0,
    (float) $barang['harga_beli'] * $jumlah,
    'Penambahan stok masuk',
    $_SESSION['username'] ?? '-'
);

redirectWithMessage('../pages/stok.php', 'Stok berhasil ditambahkan.', 'success');
