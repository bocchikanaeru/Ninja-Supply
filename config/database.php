<?php

mysqli_report(MYSQLI_REPORT_OFF);

$conn = mysqli_connect('localhost', 'root', '', 'db_ninjasupply');

if (!$conn) {
    die('Koneksi gagal: ' . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');

function columnExists(mysqli $conn, string $table, string $column): bool
{
    $table = mysqli_real_escape_string($conn, $table);
    $column = mysqli_real_escape_string($conn, $column);
    $result = mysqli_query($conn, "SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");

    return $result instanceof mysqli_result && mysqli_num_rows($result) > 0;
}

if (!columnExists($conn, 'barang', 'kategori')) {
    mysqli_query($conn, "ALTER TABLE barang ADD COLUMN kategori VARCHAR(100) NOT NULL DEFAULT 'Umum' AFTER nama_barang");
}

if (!columnExists($conn, 'barang', 'harga_beli')) {
    mysqli_query($conn, "ALTER TABLE barang ADD COLUMN harga_beli DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER deskripsi");
}

mysqli_query(
    $conn,
    "CREATE TABLE IF NOT EXISTS riwayat_stok (
        id_riwayat INT(11) NOT NULL AUTO_INCREMENT,
        id_barang INT(11) NOT NULL,
        nama_barang VARCHAR(100) NOT NULL,
        tipe VARCHAR(30) NOT NULL,
        jumlah INT(11) NOT NULL,
        harga_beli DECIMAL(10,2) NOT NULL DEFAULT 0,
        harga_jual DECIMAL(10,2) NOT NULL DEFAULT 0,
        total_nilai DECIMAL(12,2) NOT NULL DEFAULT 0,
        keterangan VARCHAR(255) NOT NULL,
        diproses_oleh VARCHAR(50) NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (id_riwayat),
        KEY idx_barang (id_barang),
        KEY idx_tipe (tipe)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
);

mysqli_query($conn, "UPDATE user SET role = 'pegawai' WHERE role IN ('kasir', 'gudang')");
