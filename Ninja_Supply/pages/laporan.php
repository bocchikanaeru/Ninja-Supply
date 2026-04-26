<?php
include '../helpers/auth.php';
include '../config/database.php';

checkLogin();
checkRole(['owner']);

$barangResult = mysqli_query($conn, 'SELECT COUNT(*) AS total_barang, COALESCE(SUM(stok), 0) AS total_stok FROM barang');
$barangStat = mysqli_fetch_assoc($barangResult) ?: ['total_barang' => 0, 'total_stok' => 0];

$pegawaiResult = mysqli_query($conn, "SELECT COUNT(*) AS total_pegawai FROM user WHERE role IN ('pegawai', 'kasir', 'gudang') AND status = 'aktif'");
$pegawaiStat = mysqli_fetch_assoc($pegawaiResult) ?: ['total_pegawai' => 0];

$summaryResult = mysqli_query(
    $conn,
    "SELECT
        COALESCE(SUM(CASE WHEN tipe = 'penjualan' THEN total_nilai ELSE 0 END), 0) AS total_penjualan,
        COALESCE(SUM(CASE WHEN tipe = 'penjualan' THEN harga_beli * jumlah ELSE 0 END), 0) AS total_modal_terjual,
        COALESCE(SUM(CASE WHEN tipe IN ('kadaluarsa', 'rusak') THEN total_nilai ELSE 0 END), 0) AS total_kerugian
     FROM riwayat_stok"
);
$summary = mysqli_fetch_assoc($summaryResult) ?: [
    'total_penjualan' => 0,
    'total_modal_terjual' => 0,
    'total_kerugian' => 0,
];

$keuntungan = ((float) $summary['total_penjualan'] - (float) $summary['total_modal_terjual']) - (float) $summary['total_kerugian'];

$recentReportResult = mysqli_query(
    $conn,
    "SELECT nama_barang, tipe, jumlah, total_nilai, diproses_oleh, DATE_FORMAT(created_at, '%d %b %Y %H:%i') AS tanggal
     FROM riwayat_stok
     ORDER BY created_at DESC
     LIMIT 12"
);

include '../components/header.php';
include '../components/sidebar.php';
?>

<h2>Reports</h2>

<div class="stats-grid">
    <article class="stat-card stat-blue">
        <span>Total Barang</span>
        <strong><?php echo e((string) $barangStat['total_barang']); ?></strong>
    </article>
    <article class="stat-card stat-green">
        <span>Total Stok</span>
        <strong><?php echo e((string) $barangStat['total_stok']); ?></strong>
    </article>
    <article class="stat-card stat-purple">
        <span>Total Penjualan</span>
        <strong><?php echo formatRupiah((float) $summary['total_penjualan']); ?></strong>
    </article>
    <article class="stat-card stat-orange">
        <span>Total Kerugian</span>
        <strong><?php echo formatRupiah((float) $summary['total_kerugian']); ?></strong>
    </article>
</div>

<div class="simple-grid report-simple-grid">
    <section class="surface-panel">
        <div class="panel-head">
            <h3>Ringkasan</h3>
            <span>Owner panel</span>
        </div>
        <div class="summary-stack">
            <div class="summary-row">
                <span>Total Pegawai Aktif</span>
                <strong><?php echo e((string) $pegawaiStat['total_pegawai']); ?></strong>
            </div>
            <div class="summary-row">
                <span>Keuntungan Bersih</span>
                <strong><?php echo formatRupiah((float) $summary['total_modal_terjual']); ?></strong>
            </div>
            <div class="summary-row summary-total">
                <span>Keuntungan</span>
                <strong><?php echo formatRupiah($keuntungan); ?></strong>
            </div>
        </div>
    </section>
    
    <section class="surface-panel report-table-panel">
        <div class="panel-head">
            <h3>Riwayat Aktivitas</h3>
            <span>Stok dan penjualan terbaru</span>
        </div>
        <div class="table-wrap">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Tipe</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Oleh</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($recentReportResult) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($recentReportResult)): ?>
                            <tr>
                                <td><?php echo e($row['nama_barang']); ?></td>
                                <td><span class="pill pill-<?php echo e($row['tipe']); ?>"><?php echo e(ucfirst($row['tipe'])); ?></span></td>
                                <td><?php echo e((string) $row['jumlah']); ?></td>
                                <td><?php echo formatRupiah((float) $row['total_nilai']); ?></td>
                                <td><?php echo e($row['diproses_oleh']); ?></td>
                                <td><?php echo e($row['tanggal']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6">Belum ada riwayat aktivitas.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<?php include '../components/footer.php'; ?>
