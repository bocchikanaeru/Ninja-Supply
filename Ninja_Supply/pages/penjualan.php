<?php
include '../helpers/auth.php';
include '../config/database.php';

checkLogin();
checkRole(['owner', 'pegawai']);

$barangResult = mysqli_query($conn, "SELECT id_barang, nama_barang, kategori, harga, stok FROM barang WHERE status = 'aktif' ORDER BY nama_barang ASC");
$barangOptions = [];
while ($barang = mysqli_fetch_assoc($barangResult)) {
    $barangOptions[] = $barang;
}

include '../components/header.php';
include '../components/sidebar.php';
?>

<h2>Transaksi</h2>

<form action="../process/transaksi.php" method="POST" class="panel-form panel-form-wide" id="sale-form">
    <h3>Input Penjualan</h3>

    <div id="sale-rows" class="sale-rows">
        <div class="sale-row">
            <div>
                <label>Barang</label>
                <select name="id_barang[]" required>
                    <option value="">-- Pilih barang --</option>
                    <?php foreach ($barangOptions as $barang): ?>
                        <option value="<?php echo e((string) $barang['id_barang']); ?>">
                            <?php echo e($barang['nama_barang']); ?> / <?php echo e($barang['kategori']); ?> - <?php echo formatRupiah((float) $barang['harga']); ?> - stok <?php echo e((string) $barang['stok']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Jumlah</label>
                <input type="number" name="jumlah[]" min="1" required>
            </div>
            <div class="sale-row-action">
                <button type="button" class="button-secondary remove-row">Hapus</button>
            </div>
        </div>
    </div>

    <div class="action-row">
        <button type="button" class="button-secondary" id="add-row">Tambah Barang</button>
    </div>

    <button type="submit">Transaksi</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const saleRows = document.getElementById('sale-rows');
    const addRowButton = document.getElementById('add-row');

    function createRow() {
        const row = document.createElement('div');
        row.className = 'sale-row';
        row.innerHTML = `
            <div>
                <label>Barang</label>
                <select name="id_barang[]" required>
                    <option value="">-- Pilih barang --</option>
                    <?php foreach ($barangOptions as $barang): ?>
                        <option value="<?php echo e((string) $barang['id_barang']); ?>">
                            <?php echo e($barang['nama_barang']); ?> / <?php echo e($barang['kategori']); ?> - <?php echo formatRupiah((float) $barang['harga']); ?> - stok <?php echo e((string) $barang['stok']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Jumlah</label>
                <input type="number" name="jumlah[]" min="1" required>
            </div>
            <div class="sale-row-action">
                <button type="button" class="button-secondary remove-row">Hapus</button>
            </div>
        `;
        saleRows.appendChild(row);
    }

    addRowButton.addEventListener('click', createRow);

    saleRows.addEventListener('click', function (event) {
        if (!event.target.classList.contains('remove-row')) {
            return;
        }

        const rows = saleRows.querySelectorAll('.sale-row');
        if (rows.length === 1) {
            rows[0].querySelector('select').value = '';
            rows[0].querySelector('input').value = '';
            return;
        }

        event.target.closest('.sale-row').remove();
    });
});
</script>

<?php include '../components/footer.php'; ?>
