<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function normalizeRole(?string $role): string
{
    return match ($role) {
        'gudang', 'kasir', 'pegawai' => 'pegawai',
        'owner' => 'owner',
        default => '',
    };
}

function defaultPageByRole(?string $role): string
{
    return normalizeRole($role) === 'owner' ? 'laporan.php' : 'stok.php';
}

function checkLogin(): void
{
    if (empty($_SESSION['login'])) {
        header('Location: ../login.php');
        exit;
    }

    $_SESSION['role'] = normalizeRole($_SESSION['role'] ?? '');
}

function checkRole(array $roles = []): void
{
    $currentRole = normalizeRole($_SESSION['role'] ?? null);
    $allowedRoles = array_map('normalizeRole', $roles);

    if (!$currentRole || !in_array($currentRole, $allowedRoles, true)) {
        http_response_code(403);
        echo 'Akses ditolak.';
        exit;
    }
}

function redirectWithMessage(string $location, string $message, string $type = 'info'): void
{
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type,
    ];

    header("Location: {$location}");
    exit;
}

function getFlash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function formatRupiah(float $value): string
{
    return 'Rp ' . number_format($value, 0, ',', '.');
}
