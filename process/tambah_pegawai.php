<?php
require_once '../helpers/auth.php';
require_once '../config/database.php';

checkLogin();
checkRole(['owner']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/pegawai.php');
    exit;
}

$nama = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$email = trim($_POST['email'] ?? '');
$hp = trim($_POST['no_handphone'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$jk = $_POST['jenis_kelamin'] ?? '';
$role = 'pegawai';
$status = 'aktif';
$foto = '';

if ($nama === '' || $username === '' || $password === '' || $email === '' || $hp === '' || $alamat === '' || !in_array($jk, ['L', 'P'], true)) {
    redirectWithMessage('../pages/pegawai.php', 'Data pegawai harus diisi lengkap.', 'error');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirectWithMessage('../pages/pegawai.php', 'Format email pegawai tidak valid.', 'error');
}

if (strlen($password) < 6) {
    redirectWithMessage('../pages/pegawai.php', 'Password pegawai minimal 6 karakter.', 'error');
}

$checkStmt = mysqli_prepare($conn, 'SELECT id_user FROM user WHERE username = ? OR email = ? LIMIT 1');
mysqli_stmt_bind_param($checkStmt, 'ss', $username, $email);
mysqli_stmt_execute($checkStmt);
$existingUser = mysqli_stmt_get_result($checkStmt);
mysqli_stmt_close($checkStmt);

if (mysqli_fetch_assoc($existingUser)) {
    redirectWithMessage('../pages/pegawai.php', 'Username atau email pegawai sudah digunakan.', 'error');
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$idResult = mysqli_query($conn, 'SELECT COALESCE(MAX(id_user), 0) + 1 AS next_id FROM user');
$nextIdRow = mysqli_fetch_assoc($idResult);
$nextId = (int) ($nextIdRow['next_id'] ?? 1);

$stmt = mysqli_prepare(
    $conn,
    'INSERT INTO user (id_user, nama, username, password, role, foto, alamat, jenis_kelamin, email, no_handphone, status)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
mysqli_stmt_bind_param($stmt, 'issssssssss', $nextId, $nama, $username, $hashedPassword, $role, $foto, $alamat, $jk, $email, $hp, $status);
$success = mysqli_stmt_execute($stmt);
$error = mysqli_error($conn);
mysqli_stmt_close($stmt);

if (!$success) {
    redirectWithMessage('../pages/pegawai.php', 'Gagal menambah pegawai: ' . $error, 'error');
}

redirectWithMessage('../pages/pegawai.php', 'Pegawai berhasil ditambahkan.', 'success');
