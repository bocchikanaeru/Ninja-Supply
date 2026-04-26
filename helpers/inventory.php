<?php

function logInventoryActivity(
    mysqli $conn,
    int $idBarang,
    string $namaBarang,
    string $tipe,
    int $jumlah,
    float $hargaBeli,
    float $hargaJual,
    float $totalNilai,
    string $keterangan,
    string $diprosesOleh
): void {
    $stmt = mysqli_prepare(
        $conn,
        'INSERT INTO riwayat_stok (id_barang, nama_barang, tipe, jumlah, harga_beli, harga_jual, total_nilai, keterangan, diproses_oleh)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );

    mysqli_stmt_bind_param(
        $stmt,
        'issidddss',
        $idBarang,
        $namaBarang,
        $tipe,
        $jumlah,
        $hargaBeli,
        $hargaJual,
        $totalNilai,
        $keterangan,
        $diprosesOleh
    );
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
