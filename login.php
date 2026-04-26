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
    <title>Login | Ninja Supply</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page">
    <div class="auth-card">
        <h1 class="login-brand">
            <span class="title-box" aria-hidden="true"></span>
            <span>Ninja Supply</span>
        </h1>

        <?php if ($flash): ?>
            <div class="alert alert-<?php echo e($flash['type']); ?>">
                <?php echo e($flash['message']); ?>
            </div>
        <?php endif; ?>

        <form action="process/login_process.php" method="POST" class="auth-form">
            <label for="username">Username</label>
            <input id="username" name="username" placeholder="Masukkan username" required>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Masukkan password" required>

            <button type="submit">Login</button>
        </form>

        <p class="auth-link">Khusus owner baru: <a href="signup.php">Daftar owner</a></p>
    </div>
</body>
</html>
