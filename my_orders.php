<?php
require_once 'config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

try {
    // Ambil semua pesanan user
    $orders = $database->orders->find([
        'user_id' => $_SESSION['user_id']
    ], [
        'sort' => ['created_at' => -1] // Urutkan dari yang terbaru
    ])->toArray();
} catch (Exception $e) {
    error_log($e->getMessage());
    $error = 'Gagal mengambil data pesanan';
}

include 'includes/header.php';
?>

<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Pesanan Saya</h4>
                <a href="index.php" class="btn btn-outline-primary">
                    <i class="bi bi-cart-plus me-2"></i>
                    Belanja Lagi
                </a>
            </div>

            <?php if (empty($orders)): ?>
            <!-- Jika tidak ada pesanan -->
            <div class="text-center py-5">
                <i class="bi bi-bag-x display-1 text-muted"></i>
                <h5 class="mt-3">Belum ada pesanan</h5>
                <p class="text-muted">Anda belum memiliki riwayat pesanan</p>
                <a href="index.php" class="btn btn-primary">
                    Mulai Belanja
                </a>
            </div>
            <?php else: ?>
            <!-- Daftar Pesanan -->
            <?php foreach ($orders as $order): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <span class="text-muted">#<?php echo substr($order['_id'], -8); ?></span>
                            <span class="mx-2">•</span>
                            <span><?php echo $order['created_at']->toDateTime()->format('d M Y H:i'); ?></span>
                        </div>
                        <div class="col-auto">
                            <?php
                            $status_class = '';
                            $status_text = '';
                            switch($order['status']) {
                                case 'pending':
                                    $status_class = 'warning';
                                    $status_text = 'Menunggu Pembayaran';
                                    break;
                                case 'paid':
                                    $status_class = 'success';
                                    $status_text = 'Sudah Dibayar';
                                    break;
                                case 'shipped':
                                    $status_class = 'info';
                                    $status_text = 'Dalam Pengiriman';
                                    break;
                                case 'completed':
                                    $status_class = 'primary';
                                    $status_text = 'Selesai';
                                    break;
                            }
                            ?>
                            <span class="badge bg-<?php echo $status_class; ?>">
                                <?php echo $status_text; ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Produk yang dibeli -->
                    <?php foreach ($order['items'] as $item): ?>
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <?php
                            // Ambil data produk untuk mendapatkan gambar
                            $product = $database->products->findOne(['_id' => new MongoDB\BSON\ObjectId($item['product_id'])]);
                            ?>
                            <img src="<?php echo $product['image'] ?? 'assets/images/no-image.jpg'; ?>" 
                                 alt="<?php echo $item['product_name']; ?>"
                                 class="product-image">
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1"><?php echo $item['product_name']; ?></h6>
                            <p class="mb-0 text-muted">
                                <?php echo $item['quantity']; ?> x Rp <?php echo number_format($item['price'], 0, ',', '.'); ?>
                            </p>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold">
                                Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <hr>

                    <!-- Ringkasan pembayaran -->
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-2">Alamat Pengiriman:</h6>
                            <p class="mb-0">
                                <?php echo $order['shipping_address']['receiver_name']; ?><br>
                                <?php echo $order['shipping_address']['phone']; ?><br>
                                <?php echo $order['shipping_address']['address']; ?><br>
                                <?php echo "{$order['shipping_address']['district']}, {$order['shipping_address']['city']}, {$order['shipping_address']['province']} {$order['shipping_address']['postal_code']}"; ?>
                            </p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="mb-2">
                                <span class="text-muted">Subtotal:</span>
                                <span class="ms-2">Rp <?php echo number_format($order['subtotal'], 0, ',', '.'); ?></span>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted">Biaya Pengiriman:</span>
                                <span class="ms-2">Rp <?php echo number_format($order['shipping_fee'], 0, ',', '.'); ?></span>
                            </div>
                            <div class="mb-2">
                                <span class="text-muted">Total Pembayaran:</span>
                                <strong class="ms-2">Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></strong>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol aksi -->
                    <div class="mt-3 text-end">
                        <a href="payment_receipt.php?order_id=<?php echo $order['_id']; ?>" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-file-text me-1"></i>
                            Lihat Detail
                        </a>
                        <?php if ($order['status'] === 'completed'): ?>
                        <button class="btn btn-primary btn-sm" onclick="buyAgain('<?php echo $order['_id']; ?>')">
                            <i class="bi bi-cart-plus me-1"></i>
                            Beli Lagi
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.product-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #dee2e6;
}

.badge {
    padding: 0.5rem 1rem;
    font-weight: 500;
}
</style>

<script>
function buyAgain(orderId) {
    // Implementasi fungsi beli lagi
    // Bisa dengan menambahkan semua produk ke keranjang
    alert('Fitur beli lagi akan segera tersedia!');
}
</script>

<?php include 'includes/footer.php'; ?> 