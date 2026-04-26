<?php
require_once __DIR__ . '/helpers/auth.php';

if (!empty($_SESSION['login'])) {
    header('Location: pages/' . defaultPageByRole($_SESSION['role'] ?? ''));
    exit;
}

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup | Ninja Supply</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page">
    <div class="auth-card auth-card-wide">
        <h1>Daftar Owner</h1>
        <p>Halaman ini hanya untuk membuat akun owner.</p>

        <?php if ($flash): ?>
            <div class="alert alert-<?php echo e($flash['type']); ?>">
                <?php echo e($flash['message']); ?>
            </div>
        <?php endif; ?>

        <form action="process/signup_process.php" method="POST" class="auth-form">
            <label for="nama">Nama</label>
            <input id="nama" name="nama" placeholder="Nama lengkap" required>

            <label for="username">Username</label>
            <input id="username" name="username" placeholder="Username" required>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Minimal 6 karakter" required>

            <label for="email">Email</label>
            <input id="email" type="email" name="email" placeholder="nama@email.com" required>

            <label for="no_handphone">No. Handphone</label>
            <input id="no_handphone" name="no_handphone" placeholder="08xxxxxxxxxx" required>

            <label for="alamat">Alamat</label>
            <textarea id="alamat" name="alamat" placeholder="Alamat lengkap" rows="3" required></textarea>

            <label for="jenis_kelamin">Jenis Kelamin</label>
            <select id="jenis_kelamin" name="jenis_kelamin" required>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
            </select>

            <input type="hidden" name="role" value="owner">

            <button type="submit">Daftar Owner</button>
        </form>

        <p class="auth-link">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
    </div>
</body>
</html>
