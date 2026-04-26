<?php
include '../helpers/auth.php';

checkLogin();

$target = normalizeRole($_SESSION['role'] ?? '') === 'owner' ? 'laporan.php' : 'stok.php';
header('Location: ' . $target);
exit;
