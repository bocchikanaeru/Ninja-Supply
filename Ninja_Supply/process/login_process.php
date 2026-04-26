<?php
require_once '../helpers/auth.php';
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    redirectWithMessage('../login.php', 'Username dan password wajib diisi.', 'error');
}

$stmt = mysqli_prepare($conn, "SELECT username, password, role, status FROM user WHERE username = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

$passwordValid = $user && (password_verify($password, $user['password']) || hash_equals($user['password'], $password));

if (!$user || $user['status'] !== 'aktif' || !$passwordValid) {
    redirectWithMessage('../login.php', 'Login gagal. Periksa username atau password.', 'error');
}

$normalizedRole = normalizeRole($user['role'] ?? '');
if ($normalizedRole === '') {
    redirectWithMessage('../login.php', 'Role akun tidak valid.', 'error');
}

$_SESSION['login'] = true;
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $normalizedRole;

redirectWithMessage('../pages/' . defaultPageByRole($normalizedRole), 'Login berhasil.', 'success');
