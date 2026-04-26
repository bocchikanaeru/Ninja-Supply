<?php
include '../helpers/auth.php';
include '../config/database.php';

checkLogin();
checkRole(['owner', 'pegawai']);

$q = mysqli_query($conn, "SELECT id_barang, nama_barang, stok, satuan, kategori FROM barang WHERE status = 'aktif' ORDER BY nama_barang ASC");

include '../components/header.php';
include '../components/sidebar.php';
?>

<h2>Stock Out</h2>

<form action="../process/kurang_stok.php" method="POST" class="panel-form">
    <label for="id_barang">Pilih Barang</label>
    <select id="id_barang" name="id_barang" required>
        <option value="">-- Pilih barang --</option>
        <?php while ($d = mysqli_fetch_assoc($q)): ?>
            <option value="<?php echo e((string) $d['id_barang']); ?>">
                <?php echo e($d['nama_barang']); ?> / <?php echo e($d['kategori']); ?> - stok <?php echo e((string) $d['stok']); ?> <?php echo e($d['satuan']); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label for="jumlah">Jumlah Keluar</label>
    <input id="jumlah" type="number" name="jumlah" min="1" required>

    <label for="alasan">Alasan Pengurangan</label>
    <select id="alasan" name="alasan" required>
        <option value="kadaluarsa">Kadaluarsa</option>
        <option value="rusak">Rusak</option>
    </select>

    <button type="submit">Kurangi Stok</button>
</form>

<?php include '../components/footer.php'; ?>
