<?php
require_once 'config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['selected_address_id'])) {
    header('Location: cart.php');
    exit;
}

try {
    // Ambil data alamat
    $address = $database->addresses->findOne([
        '_id' => new MongoDB\BSON\ObjectId($_SESSION['selected_address_id'])
    ]);

    // Ambil data keranjang
    $cart_items = $database->cart->aggregate([
        [
            '$match' => ['user_id' => $_SESSION['user_id']]
        ],
        [
            '$lookup' => [
                'from' => 'products',
                'localField' => 'product_id',
                'foreignField' => '_id',
                'as' => 'product'
            ]
        ],
        ['$unwind' => '$product']
    ])->toArray();

    $subtotal = 0;
    foreach ($cart_items as $item) {
        $subtotal += $item['product']['price'] * $item['quantity'];
    }

    $shipping_fee = 9000; // Biaya pengiriman default
    $total = $subtotal + $shipping_fee;

} catch (Exception $e) {
    error_log($e->getMessage());
    header('Location: cart.php');
    exit;
}

// Proses pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $order_data = [
            'user_id' => $_SESSION['user_id'],
            'shipping_address' => $address,
            'items' => array_map(function($item) {
                return [
                    'product_id' => (string)$item['product']['_id'],
                    'product_name' => $item['product']['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['product']['price'],
                    'subtotal' => $item['product']['price'] * $item['quantity']
                ];
            }, $cart_items),
            'payment_method' => $_POST['payment_method'],
            'subtotal' => $subtotal,
            'shipping_fee' => $shipping_fee,
            'total_amount' => $total,
            'status' => 'pending',
            'created_at' => new MongoDB\BSON\UTCDateTime()
        ];

        $result = $database->orders->insertOne($order_data);
        
        if ($result->getInsertedCount()) {
            // Kosongkan keranjang
            $database->cart->deleteMany(['user_id' => $_SESSION['user_id']]);
            
            // Redirect ke halaman bukti pembayaran dengan auto logout
            header("Location: payment_receipt.php?order_id=" . $result->getInsertedId() . "&auto_logout=true");
            exit;
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        $error = 'Gagal memproses pembayaran';
    }
}

include 'includes/header.php';
?>

<div class="container mt-5 mb-5">
    <div class="row">
        <!-- Detail Pesanan -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-cart-check me-2"></i>
                        Detail Pesanan
                    </h5>
                    <a href="cart.php" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-pencil me-1"></i>
                        Ubah Pesanan
                    </a>
                </div>
                <div class="card-body">
                    <!-- Alamat Pengiriman -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">
                                <i class="bi bi-geo-alt me-2"></i>
                                Alamat Pengiriman
                            </h6>
                            <a href="address.php" class="btn btn-link btn-sm text-decoration-none">
                                <i class="bi bi-pencil me-1"></i>
                                Ubah
                            </a>
                        </div>
                        <div class="border rounded p-3">
                            <p class="mb-0">
                                <strong><?php echo $address['receiver_name']; ?></strong><br>
                                <?php echo $address['phone']; ?><br>
                                <?php echo $address['address']; ?><br>
                                <?php echo "{$address['district']}, {$address['city']}, {$address['province']} {$address['postal_code']}"; ?>
                            </p>
                        </div>
                    </div>

                    <!-- Daftar Produk dari Keranjang -->
                    <div class="mb-4">
                        <h6 class="mb-3">
                            <i class="bi bi-box me-2"></i>
                            Produk yang Dibeli
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produk</th>
                                        <th class="text-center" width="100">Jumlah</th>
                                        <th class="text-end" width="150">Harga</th>
                                        <th class="text-end" width="150">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart_items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo $item['product']['image']; ?>" 
                                                     alt="<?php echo $item['product']['name']; ?>"
                                                     class="product-image me-3">
                                                <div>
                                                    <h6 class="mb-0"><?php echo $item['product']['name']; ?></h6>
                                                    <?php if (isset($item['product']['category'])): ?>
                                                    <small class="text-muted">Kategori: <?php echo $item['product']['category']; ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="quantity-badge">
                                                <?php echo $item['quantity']; ?>
                                            </span>
                                        </td>
                                        <td class="text-end align-middle">
                                            Rp <?php echo number_format($item['product']['price'], 0, ',', '.'); ?>
                                        </td>
                                        <td class="text-end align-middle">
                                            <strong>
                                                Rp <?php echo number_format($item['product']['price'] * $item['quantity'], 0, ',', '.'); ?>
                                            </strong>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="3" class="text-end">Total Harga Produk:</td>
                                        <td class="text-end">
                                            <strong>
                                                Rp <?php echo number_format($subtotal, 0, ',', '.'); ?>
                                            </strong>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Opsi Pengiriman -->
                    <div class="mb-4">
                        <h6 class="mb-3">
                            <i class="bi bi-truck me-2"></i>
                            Opsi Pengiriman
                        </h6>
                        <div class="border rounded p-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="shipping" id="regular" checked>
                                <label class="form-check-label d-flex justify-content-between align-items-center" for="regular">
                                    <div>
                                        <strong>Pengiriman Reguler</strong>
                                        <br>
                                        <small class="text-muted">Estimasi 2-3 hari</small>
                                    </div>
                                    <span>Rp <?php echo number_format($shipping_fee, 0, ',', '.'); ?></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metode Pembayaran -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-credit-card me-2"></i>
                        Metode Pembayaran
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="payment.php" id="paymentForm">
                        <!-- Transfer Bank -->
                        <div class="payment-group mb-4">
                            <h6 class="mb-3">Transfer Bank</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="bca" value="bca" required>
                                <label class="form-check-label d-flex align-items-center" for="bca">
                                    <img src="assets/images/bank-bca.png" alt="BCA" height="30" class="me-2">
                                    <span>Bank BCA</span>
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="bni" value="bni">
                                <label class="form-check-label d-flex align-items-center" for="bni">
                                    <img src="assets/images/bank-bni.png" alt="BNI" height="30" class="me-2">
                                    <span>Bank BNI</span>
                                </label>
                            </div>
                        </div>

                        <!-- E-Wallet -->
                        <div class="payment-group">
                            <h6 class="mb-3">E-Wallet</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="payment_method" id="dana" value="dana">
                                <label class="form-check-label d-flex align-items-center" for="dana">
                                    <img src="assets/images/dana.png" alt="DANA" height="30" class="me-2">
                                    <span>DANA</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="ovo" value="ovo">
                                <label class="form-check-label d-flex align-items-center" for="ovo">
                                    <img src="assets/images/ovo.png" alt="OVO" height="30" class="me-2">
                                    <span>OVO</span>
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Ringkasan Pembayaran -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-receipt me-2"></i>
                        Ringkasan Pembayaran
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Detail Produk -->
                    <div class="mb-3">
                        <h6 class="mb-3">Detail Produk (<?php echo count($cart_items); ?> Item)</h6>
                        <?php foreach ($cart_items as $item): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">
                                <?php echo $item['product']['name']; ?> 
                                <small>(<?php echo $item['quantity']; ?>x)</small>
                            </span>
                            <span>Rp <?php echo number_format($item['product']['price'] * $item['quantity'], 0, ',', '.'); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <hr>

                    <!-- Rincian Biaya -->
                    <div class="mb-3">
                        <h6 class="mb-3">Rincian Biaya</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Harga (<?php echo array_sum(array_column($cart_items, 'quantity')); ?> barang)</span>
                            <span>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Biaya Pengiriman</span>
                            <span>Rp <?php echo number_format($shipping_fee, 0, ',', '.'); ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Biaya Layanan</span>
                            <span>Rp 1.000</span>
                        </div>
                    </div>

                    <hr>

                    <!-- Total Pembayaran -->
                    <div class="d-flex justify-content-between mb-4">
                        <strong>Total Pembayaran</strong>
                        <strong class="text-primary">
                            Rp <?php echo number_format($total + 1000, 0, ',', '.'); ?>
                        </strong>
                    </div>

                    <!-- Tombol Bayar -->
                    <button type="submit" form="paymentForm" class="btn btn-primary w-100">
                        <i class="bi bi-lock me-2"></i>
                        Bayar Sekarang
                    </button>

                    <!-- Catatan -->
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Dengan mengklik tombol di atas, Anda menyetujui syarat dan ketentuan yang berlaku
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.product-image {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 4px;
}

.form-check {
    padding: 1rem;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    cursor: pointer;
}

.form-check:hover {
    background-color: #f8f9fa;
}

.form-check-input:checked ~ .form-check-label {
    color: #0d6efd;
}
</style>

<?php include 'includes/footer.php'; ?>