<?php
require_once '../helpers/auth.php';
require_once '../config/database.php';

checkLogin();
checkRole(['owner', 'pegawai']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/stok.php');
    exit;
}

$namaBarang = trim($_POST['nama_barang'] ?? '');
$kategori = trim($_POST['kategori'] ?? 'Umum');
$harga = (float) ($_POST['harga'] ?? 0);
$hargaBeli = (float) ($_POST['harga_beli'] ?? 0);
$satuan = trim($_POST['satuan'] ?? 'pcs');
$stokMinimum = (int) ($_POST['stok_minimum'] ?? 0);
$deskripsi = trim($_POST['deskripsi'] ?? '');

if ($namaBarang === '' || $kategori === '' || $harga < 0 || $hargaBeli < 0 || $satuan === '' || $stokMinimum < 0) {
    redirectWithMessage('../pages/stok.php', 'Data barang belum valid. Periksa nama, kategori, harga, keuntungan bersih, satuan, dan stok minimum.', 'error');
}

$checkStmt = mysqli_prepare($conn, 'SELECT id_barang FROM barang WHERE nama_barang = ? LIMIT 1');
mysqli_stmt_bind_param($checkStmt, 's', $namaBarang);
mysqli_stmt_execute($checkStmt);
$existingResult = mysqli_stmt_get_result($checkStmt);
$existingBarang = mysqli_fetch_assoc($existingResult);
mysqli_stmt_close($checkStmt);

if ($existingBarang) {
    redirectWithMessage('../pages/stok.php', 'Nama barang sudah ada di daftar.', 'error');
}

$idResult = mysqli_query($conn, 'SELECT COALESCE(MAX(id_barang), 0) + 1 AS next_id FROM barang');
$nextIdRow = mysqli_fetch_assoc($idResult);
$nextId = (int) ($nextIdRow['next_id'] ?? 1);
$kodeBarang = 'BRG-' . str_pad((string) $nextId, 4, '0', STR_PAD_LEFT);
$deskripsi = $deskripsi !== '' ? $deskripsi : '-';
$stokAwal = 0;
$status = 'aktif';

$stmt = mysqli_prepare(
    $conn,
    'INSERT INTO barang (id_barang, kode_barang, nama_barang, kategori, deskripsi, harga_beli, harga, stok, satuan, stok_minimum, status)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
mysqli_stmt_bind_param(
    $stmt,
    'issssddisis',
    $nextId,
    $kodeBarang,
    $namaBarang,
    $kategori,
    $deskripsi,
    $hargaBeli,
    $harga,
    $stokAwal,
    $satuan,
    $stokMinimum,
    $status
);
$success = mysqli_stmt_execute($stmt);
$error = mysqli_error($conn);
mysqli_stmt_close($stmt);

if (!$success) {
    redirectWithMessage('../pages/stok.php', 'Gagal menyimpan barang: ' . $error, 'error');
}

redirectWithMessage('../pages/stok.php', 'Barang baru berhasil ditambahkan ke daftar. Silakan isi stok lewat menu Stock In.', 'success');
