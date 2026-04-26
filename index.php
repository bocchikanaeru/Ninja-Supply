<?php
session_start();
require_once __DIR__ . '/helpers/auth.php';

if (isset($_SESSION['login'])) {
    header('Location: pages/' . defaultPageByRole($_SESSION['role'] ?? ''));
} else {
    header("Location: login.php");
}
