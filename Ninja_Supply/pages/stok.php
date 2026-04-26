<?php
include '../helpers/auth.php';
include '../config/database.php';

checkLogin();

$canManageBarang = in_array(normalizeRole($_SESSION['role'] ?? ''), ['owner', 'pegawai'], true);
$q = mysqli_query($conn, 'SELECT id_barang, kode_barang, nama_barang, kategori, harga_beli, harga, stok, satuan FROM barang ORDER BY nama_barang ASC');

include '../components/header.php';
include '../components/sidebar.php';
?>

<h2>Data Barang</h2>

<section class="surface-panel">
    <div class="table-wrap">
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Keuntungan Bersih</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Satuan</th>
                    <?php if ($canManageBarang): ?>
                        <th>Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php while ($d = mysqli_fetch_assoc($q)): ?>
                    <tr>
                        <td><?php echo e($d['kode_barang']); ?></td>
                        <td><?php echo e($d['nama_barang']); ?></td>
                        <td><?php echo e($d['kategori']); ?></td>
                        <td><?php echo formatRupiah((float) $d['harga_beli']); ?></td>
                        <td><?php echo formatRupiah((float) $d['harga']); ?></td>
                        <td><?php echo e((string) $d['stok']); ?></td>
                        <td><?php echo e($d['satuan']); ?></td>
                        <?php if ($canManageBarang): ?>
                            <td>
                                <form action="../process/hapus_barang.php" method="POST" onsubmit="return confirm('Hapus data barang ini?');">
                                    <input type="hidden" name="id_barang" value="<?php echo e((string) $d['id_barang']); ?>">
                                    <button type="submit" class="button-danger">Hapus</button>
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</section>

<?php if ($canManageBarang): ?>
    <form action="../process/tambah_barang.php" method="POST" class="panel-form">
        <h3>Tambah Barang Baru</h3>

        <label for="nama_barang">Nama Barang</label>
        <input id="nama_barang" name="nama_barang" placeholder="Contoh: Beras Premium 5 Kg" required>

        <label for="harga">Harga</label>
        <input id="harga" type="number" name="harga" min="0" value="0" required>

        <label for="harga_beli">Harga Beli</label>
        <input id="harga_beli" type="number" name="harga_beli" min="0" value="0" required>

        <label for="kategori">Kategori</label>
        <input id="kategori" name="kategori" value="Umum" required>

        <label for="satuan">Satuan</label>
        <input id="satuan" name="satuan" value="pcs" required>

        <label for="stok_minimum">Stok Minimum</label>
        <input id="stok_minimum" type="number" name="stok_minimum" min="0" value="0" required>

        <label for="deskripsi">Deskripsi</label>
        <textarea id="deskripsi" name="deskripsi" rows="3"></textarea>

        <button type="submit">Simpan Barang</button>
    </form>
<?php endif; ?>

<?php include '../components/footer.php'; ?>
