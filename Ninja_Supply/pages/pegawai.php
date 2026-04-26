<?php
include '../helpers/auth.php';
include '../config/database.php';

checkLogin();
checkRole(['owner']);

$pegawaiResult = mysqli_query(
    $conn,
    "SELECT id_user, nama, username, email, no_handphone, status
     FROM user
     WHERE role IN ('pegawai', 'kasir', 'gudang')
     ORDER BY nama ASC"
);

include '../components/header.php';
include '../components/sidebar.php';
?>

<h2>Pegawai</h2>

<div class="simple-grid">
    <form action="../process/tambah_pegawai.php" method="POST" class="panel-form">
        <h3>Tambah Pegawai</h3>

        <label for="nama">Nama</label>
        <input id="nama" name="nama" required>

        <label for="username">Username</label>
        <input id="username" name="username" required>

        <label for="password">Password</label>
        <input id="password" type="password" name="password" minlength="6" required>

        <label for="email">Email</label>
        <input id="email" type="email" name="email" required>

        <label for="no_handphone">No. Handphone</label>
        <input id="no_handphone" name="no_handphone" required>

        <label for="alamat">Alamat</label>
        <textarea id="alamat" name="alamat" rows="3" required></textarea>

        <label for="jenis_kelamin">Jenis Kelamin</label>
        <select id="jenis_kelamin" name="jenis_kelamin" required>
            <option value="L">Laki-laki</option>
            <option value="P">Perempuan</option>
        </select>

        <button type="submit">Simpan Pegawai</button>
    </form>

    <section class="surface-panel">
        <div class="panel-head">
            <h3>Daftar Pegawai</h3>
            <span><?php echo (int) mysqli_num_rows($pegawaiResult); ?> akun</span>
        </div>
        <div class="table-wrap">
            <table class="dashboard-table employee-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>HP</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($pegawaiResult) > 0): ?>
                        <?php mysqli_data_seek($pegawaiResult, 0); ?>
                        <?php while ($pegawai = mysqli_fetch_assoc($pegawaiResult)): ?>
                            <tr>
                            <td><?php echo e($pegawai['nama']); ?></td>
                            <td><?php echo e($pegawai['username']); ?></td>
                            <td><?php echo e($pegawai['email']); ?></td>
                            <td><?php echo e($pegawai['no_handphone']); ?></td>
                            <td><span class="status-badge"><?php echo e($pegawai['status']); ?></span></td>
                            <td class="action-cell">
                                <form class="inline-form" action="../process/hapus_pegawai.php" method="POST" onsubmit="return confirm('Hapus pegawai ini?');">
                                    <input type="hidden" name="id_user" value="<?php echo e((string) $pegawai['id_user']); ?>">
                                    <button type="submit" class="button-danger">Hapus</button>
                                </form>
                            </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6">Belum ada akun pegawai.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<?php include '../components/footer.php'; ?>
