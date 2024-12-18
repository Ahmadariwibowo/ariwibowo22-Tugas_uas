<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

// Fetch orders
try {
    $orders = $database->orders->find([], ['sort' => ['created_at' => -1]]);
} catch (Exception $e) {
    $orders = [];
    error_log($e->getMessage());
}

include 'includes/header.php';
?>

<div class="admin-container">
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Pesanan</h5>
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
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
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
                                    <td><?php echo date('d/m/Y H:i', $order->created_at->toDateTime()->getTimestamp()); ?></td>
                                    <td>
                                        <a href="view_order.php?id=<?php echo $order->_id; ?>" 
                                           class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-success" 
                                                onclick="updateStatus('<?php echo $order->_id; ?>', 'completed')">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?> 