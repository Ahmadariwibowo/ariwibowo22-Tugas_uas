<?php
// Hitung jumlah item di keranjang
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    try {
        $cart_count = $database->cart->count(['user_id' => $_SESSION['user_id']]);
    } catch (Exception $e) {
        error_log($e->getMessage());
    }
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-shop me-2"></i>
            Casual Store
        </a>

        <!-- Tombol Toggle untuk Mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu Navbar -->
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" 
                       href="index.php">Beranda</a>
                </li>
                
                <!-- Dropdown Kategori -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        Kategori
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="products.php?category=shirt">Baju</a></li>
                        <li><a class="dropdown-item" href="products.php?category=pants">Celana</a></li>
                        <li><a class="dropdown-item" href="products.php?category=jacket">Jaket</a></li>
                    </ul>
                </li>
            </ul>

            <!-- Search Form -->
            <form class="d-flex me-3" action="search.php" method="GET">
                <div class="input-group">
                    <input class="form-control" type="search" name="q" placeholder="Cari produk..." 
                           aria-label="Search">
                    <button class="btn btn-outline-light" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            <!-- Cart & User Menu -->
            <div class="d-flex align-items-center">
                <!-- Cart Icon -->
                <a href="cart.php" class="btn btn-outline-light position-relative me-2">
                    <i class="bi bi-cart3"></i>
                    <?php if ($cart_count > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?php echo $cart_count; ?>
                    </span>
                    <?php endif; ?>
                </a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php">
                                <i class="bi bi-person me-2"></i>Profil Saya
                            </a></li>
                            <li><a class="dropdown-item" href="my_orders.php">
                                <i class="bi bi-box-seam me-2"></i>
                                Pesanan Saya
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i>Keluar
                            </a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-light me-2">Masuk</a>
                    <a href="register.php" class="btn btn-light">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav> 