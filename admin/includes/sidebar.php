<div class="sidebar">
    <div class="list-group list-group-flush">
        <a href="dashboard.php" class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>
        <a href="products.php" class="list-group-item list-group-item-action <?php echo in_array(basename($_SERVER['PHP_SELF']), ['products.php', 'add_product.php', 'edit_product.php']) ? 'active' : ''; ?>">
            <i class="bi bi-box-seam"></i>
            <span>Produk</span>
        </a>
        <a href="orders.php" class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">
            <i class="bi bi-cart3"></i>
            <span>Pesanan</span>
        </a>
        <a href="users.php" class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
            <i class="bi bi-people"></i>
            <span>Pengguna</span>
        </a>
        <a href="reports.php" class="list-group-item list-group-item-action <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
            <i class="bi bi-graph-up"></i>
            <span>Laporan</span>
        </a>
        <a href="../logout.php" class="list-group-item list-group-item-action text-danger">
            <i class="bi bi-box-arrow-right"></i>
            <span>Keluar</span>
        </a>
    </div>
</div> 