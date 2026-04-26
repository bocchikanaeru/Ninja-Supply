<?php $role = normalizeRole($_SESSION['role'] ?? ''); ?>

<aside class="sidebar">
    <div class="brand">
        <div>
            <h2 class="brand-title">
                <span class="title-box" aria-hidden="true"></span>
                <span>Ninja Supply</span>
            </h2>
            <p>Panel <?php echo e(ucfirst($role)); ?></p>
        </div>
    </div>

    <nav>
        <ul class="nav-list">
            <?php if ($role === 'owner'): ?>
                <li><a href="laporan.php">Reports</a></li>
            <?php endif; ?>
            <li><a href="stok.php">Data Barang</a></li>
            <li><a href="stock-in.php">Stock In</a></li>
            <li><a href="stock-out.php">Stock Out</a></li>
            <li><a href="penjualan.php">Transaksi</a></li>
            <?php if ($role === 'owner'): ?>
                <li><a href="pegawai.php">Pegawai</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</aside>

<main class="main">
    <header class="header">
        <div><h1>Ninja Supply</h1></div>
        <div class="header-user">
            <span><?php echo e($_SESSION['username'] ?? 'Guest'); ?> (<?php echo e($role ?: '-'); ?>)</span>
            <a class="logout-link" href="../logout.php">Logout</a>
        </div>
    </header>
    <section class="content">
        <?php if ($flash = getFlash()): ?>
            <div class="alert alert-<?php echo e($flash['type']); ?>">
                <?php echo e($flash['message']); ?>
            </div>
        <?php endif; ?>
