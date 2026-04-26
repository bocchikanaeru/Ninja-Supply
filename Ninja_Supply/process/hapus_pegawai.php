<?php
require_once '../helpers/auth.php';
require_once '../config/database.php';

checkLogin();
checkRole(['owner']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/pegawai.php');
    exit;
}

$idUser = (int) ($_POST['id_user'] ?? 0);

if ($idUser <= 0) {
    redirectWithMessage('../pages/pegawai.php', 'Pegawai tidak valid.', 'error');
}

$stmt = mysqli_prepare($conn, "DELETE FROM user WHERE id_user = ? AND role = 'pegawai'");
mysqli_stmt_bind_param($stmt, 'i', $idUser);
mysqli_stmt_execute($stmt);
$affected = mysqli_stmt_affected_rows($stmt);
mysqli_stmt_close($stmt);

if ($affected <= 0) {
    redirectWithMessage('../pages/pegawai.php', 'Pegawai tidak ditemukan atau tidak bisa dihapus.', 'error');
}

redirectWithMessage('../pages/pegawai.php', 'Pegawai berhasil dihapus.', 'success');
