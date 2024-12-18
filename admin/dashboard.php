<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

// Mengambil statistik
try {
    $totalProducts = $database->products->countDocuments();
    $totalOrders = $database->orders->countDocuments();
    $totalUsers = $database->users->countDocuments(['is_admin' => false]);
    
    // Menghitung total pendapatan
    $totalRevenue = 0;
    $orders = $database->orders->find(['status' => 'completed']);
    foreach ($orders as $order) {
        $totalRevenue += $order->total_amount;
    }

    // Mengambil pesanan terbaru
    $recentOrders = $database->orders->find(
        [],
        [
            'limit' => 5,
            'sort' => ['created_at' => -1]
        ]
    );
} catch (Exception $e) {
    error_log($e->getMessage());
}

include 'includes/header.php';
?>

<div class="admin-container">
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="container-fluid">
            <!-- Statistik Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Pendapatan</h5>
                            <h3 class="mb-0">Rp <?php echo number_format($totalRevenue, 0, ',', '.'); ?></h3>
                            <small>Total dari semua pesanan</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Pesanan</h5>
                            <h3 class="mb-0"><?php echo $totalOrders; ?></h3>
                            <small>Pesanan yang diterima</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Produk</h5>
                            <h3 class="mb-0"><?php echo $totalProducts; ?></h3>
                            <small>Produk yang tersedia</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Pengguna</h5>
                            <h3 class="mb-0"><?php echo $totalUsers; ?></h3>
                            <small>Pengguna terdaftar</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pesanan Terbaru -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Pesanan Terbaru</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID Pesanan</th>
                                            <th>Pelanggan</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentOrders as $order): ?>
                                        <tr>
                                            <td>#<?php echo substr($order->_id, -6); ?></td>
                                            <td><?php echo $order->customer_name; ?></td>
                                            <td>Rp <?php echo number_format($order->total_amount, 0, ',', '.'); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $order->status === 'completed' ? 'success' : 
                                                        ($order->status === 'pending' ? 'warning' : 'info'); 
                                                ?>">
                                                    <?php echo ucfirst($order->status); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d/m/Y', $order->created_at->toDateTime()->getTimestamp()); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Produk dengan Stok Rendah -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Stok Menipis</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                <?php
                                $lowStockProducts = $database->products->find(
                                    ['stock' => ['$lt' => 10]],
                                    ['limit' => 5]
                                );
                                foreach ($lowStockProducts as $product):
                                ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo $product->name; ?>
                                    <span class="badge bg-danger rounded-pill"><?php echo $product->stock; ?></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html> 