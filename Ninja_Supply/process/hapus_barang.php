<?php
require_once '../helpers/auth.php';
require_once '../config/database.php';

checkLogin();
checkRole(['owner', 'pegawai']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/stok.php');
    exit;
}

$idBarang = (int) ($_POST['id_barang'] ?? 0);

if ($idBarang <= 0) {
    redirectWithMessage('../pages/stok.php', 'Data barang tidak valid.', 'error');
}

mysqli_begin_transaction($conn);

try {
    $deleteHistoryStmt = mysqli_prepare($conn, 'DELETE FROM riwayat_stok WHERE id_barang = ?');
    mysqli_stmt_bind_param($deleteHistoryStmt, 'i', $idBarang);
    mysqli_stmt_execute($deleteHistoryStmt);
    mysqli_stmt_close($deleteHistoryStmt);

    $deleteBarangStmt = mysqli_prepare($conn, 'DELETE FROM barang WHERE id_barang = ?');
    mysqli_stmt_bind_param($deleteBarangStmt, 'i', $idBarang);
    mysqli_stmt_execute($deleteBarangStmt);
    $affectedRows = mysqli_stmt_affected_rows($deleteBarangStmt);
    mysqli_stmt_close($deleteBarangStmt);

    if ($affectedRows <= 0) {
        throw new RuntimeException('Barang tidak ditemukan.');
    }

    mysqli_commit($conn);
} catch (Throwable $e) {
    mysqli_rollback($conn);
    redirectWithMessage('../pages/stok.php', 'Gagal menghapus barang.', 'error');
}

redirectWithMessage('../pages/stok.php', 'Data barang berhasil dihapus.', 'success');
