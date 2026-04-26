<?php
require_once '../helpers/auth.php';
require_once '../config/database.php';
require_once '../helpers/inventory.php';

checkLogin();
checkRole(['owner', 'pegawai']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/penjualan.php');
    exit;
}

$ids = $_POST['id_barang'] ?? [];
$jumlahs = $_POST['jumlah'] ?? [];

if (!is_array($ids) || !is_array($jumlahs) || count($ids) === 0 || count($ids) !== count($jumlahs)) {
    redirectWithMessage('../pages/penjualan.php', 'Data penjualan tidak valid.', 'error');
}

$grandTotal = 0.0;
$totalBarang = 0;
$validatedItems = [];

for ($i = 0; $i < count($ids); $i++) {
    $id = (int) ($ids[$i] ?? 0);
    $jumlah = (int) ($jumlahs[$i] ?? 0);

    if ($id <= 0 || $jumlah <= 0) {
        continue;
    }

    $selectStmt = mysqli_prepare($conn, 'SELECT nama_barang, harga_beli, harga, stok FROM barang WHERE id_barang = ? LIMIT 1');
    mysqli_stmt_bind_param($selectStmt, 'i', $id);
    mysqli_stmt_execute($selectStmt);
    $result = mysqli_stmt_get_result($selectStmt);
    $barang = mysqli_fetch_assoc($result);
    mysqli_stmt_close($selectStmt);

    if (!$barang) {
        redirectWithMessage('../pages/penjualan.php', 'Salah satu barang tidak ditemukan.', 'error');
    }

    if ((int) $barang['stok'] < $jumlah) {
        redirectWithMessage('../pages/penjualan.php', 'Stok ' . $barang['nama_barang'] . ' tidak mencukupi.', 'error');
    }

    $total = (float) $barang['harga'] * $jumlah;
    $grandTotal += $total;
    $totalBarang++;
    $validatedItems[] = [
        'id' => $id,
        'jumlah' => $jumlah,
        'nama_barang' => $barang['nama_barang'],
        'harga_beli' => (float) $barang['harga_beli'],
        'harga' => (float) $barang['harga'],
        'total' => $total,
    ];
}

if ($totalBarang === 0) {
    redirectWithMessage('../pages/penjualan.php', 'Minimal isi satu barang untuk dijual.', 'error');
}

mysqli_begin_transaction($conn);

try {
    foreach ($validatedItems as $item) {
        $updateStmt = mysqli_prepare($conn, 'UPDATE barang SET stok = stok - ? WHERE id_barang = ?');
        mysqli_stmt_bind_param($updateStmt, 'ii', $item['jumlah'], $item['id']);
        mysqli_stmt_execute($updateStmt);
        mysqli_stmt_close($updateStmt);

        logInventoryActivity(
            $conn,
            $item['id'],
            $item['nama_barang'],
            'penjualan',
            $item['jumlah'],
            $item['harga_beli'],
            $item['harga'],
            $item['total'],
            'Transaksi penjualan',
            $_SESSION['username'] ?? '-'
        );
    }

    mysqli_commit($conn);
} catch (Throwable $e) {
    mysqli_rollback($conn);
    redirectWithMessage('../pages/penjualan.php', 'Transaksi gagal disimpan.', 'error');
}

redirectWithMessage(
    '../pages/penjualan.php',
    'Penjualan berhasil. Total barang: ' . $totalBarang . '. Total bayar: ' . formatRupiah($grandTotal) . '.',
    'success'
);
