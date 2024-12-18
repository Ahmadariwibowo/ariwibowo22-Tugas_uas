<?php
require_once 'config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Ambil data keranjang user dengan detail produk
try {
    $cart_items = $database->cart->aggregate([
        [
            '$match' => [
                'user_id' => $_SESSION['user_id']
            ]
        ],
        [
            '$addFields' => [
                'productObjectId' => [
                    '$toObjectId' => '$product_id'
                ]
            ]
        ],
        [
            '$lookup' => [
                'from' => 'products',
                'localField' => 'productObjectId',
                'foreignField' => '_id',
                'as' => 'product'
            ]
        ],
        [
            '$unwind' => '$product'
        ]
    ])->toArray();

    $total = 0;
} catch (Exception $e) {
    $cart_items = [];
    error_log($e->getMessage());
}

include 'includes/header.php';
?>

<style>
/* Style untuk Keranjang Belanja */
.cart-container {
    padding: 20px 0;
    background: #f8f9fa;
    min-height: calc(100vh - 76px);
}

.cart-item {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    margin-bottom: 15px;
    border: 1px solid rgba(0,0,0,0.05);
}

.cart-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.cart-item img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border-radius: 8px;
}

.cart-item h6 {
    font-weight: 600;
    font-size: 1.1rem;
    color: #2c3e50;
    margin-bottom: 5px;
}

.category-badge {
    background: #e9ecef;
    color: #495057;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    display: inline-block;
}

.stock-badge {
    background: #d4edda;
    color: #155724;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    display: inline-block;
}

.price-tag {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
}

.quantity-control {
    display: flex;
    align-items: center;
    background: #f8f9fa;
    border-radius: 25px;
    padding: 5px;
    width: fit-content;
}

.quantity-btn {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: none;
    background: white;
    color: #2c3e50;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.quantity-btn:hover {
    background: #e9ecef;
    transform: scale(1.1);
}

.quantity-number {
    padding: 0 15px;
    font-weight: 600;
    color: #2c3e50;
}

.remove-btn {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    border: none;
    background: #fee2e2;
    color: #dc3545;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.remove-btn:hover {
    background: #dc3545;
    color: white;
    transform: rotate(90deg);
}

/* Style untuk Ringkasan Belanja */
.summary-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.05);
    position: sticky;
    top: 90px;
}

.summary-card .card-header {
    background: linear-gradient(45deg, #2c3e50, #3498db);
    color: white;
    border-radius: 10px 10px 0 0;
    padding: 15px 20px;
}

.summary-card .card-body {
    padding: 20px;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px dashed #dee2e6;
}

.summary-item:last-child {
    border-bottom: none;
}

.total-price {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
}

.checkout-btn {
    background: linear-gradient(45deg, #2c3e50, #3498db);
    border: none;
    width: 100%;
    padding: 12px;
    border-radius: 25px;
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
}

.checkout-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.checkout-btn:disabled {
    background: #dee2e6;
    cursor: not-allowed;
}

/* Empty Cart Style */
.empty-cart {
    text-align: center;
    padding: 40px 20px;
}

.empty-cart i {
    font-size: 5rem;
    color: #dee2e6;
    margin-bottom: 20px;
}

.empty-cart h5 {
    color: #2c3e50;
    font-size: 1.5rem;
    margin-bottom: 10px;
}

.empty-cart p {
    color: #6c757d;
    margin-bottom: 20px;
}

.continue-shopping-btn {
    background: linear-gradient(45deg, #2c3e50, #3498db);
    color: white;
    padding: 10px 25px;
    border-radius: 25px;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-block;
}

.continue-shopping-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    color: white;
}
</style>

<div class="cart-container">
    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient">
                        <h5 class="mb-0 text-dark">
                            <i class="bi bi-cart3 me-2"></i>Keranjang Belanja
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($cart_items)): ?>
                            <!-- Header Tabel -->
                            <div class="cart-header d-none d-md-flex bg-light p-3">
                                <div class="row w-100 align-items-center">
                                    <div class="col-md-6">
                                        <span class="text-muted">Produk</span>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <span class="text-muted">Harga</span>
                                    </div>
                                    <div class="col-md-2 text-center">
                                        <span class="text-muted">Jumlah</span>
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <span class="text-muted">Subtotal</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Daftar Produk -->
                            <?php foreach ($cart_items as $item): 
                                $subtotal = $item['product']['price'] * $item['quantity'];
                                $total += $subtotal;
                            ?>
                                <div class="cart-item p-3">
                                    <div class="row align-items-center">
                                        <!-- Gambar & Info Produk -->
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <div class="product-image me-3">
                                                    <img src="<?php echo htmlspecialchars($item['product']['image']); ?>" 
                                                         alt="<?php echo htmlspecialchars($item['product']['name']); ?>"
                                                         class="rounded">
                                                </div>
                                                <div class="product-info">
                                                    <h6 class="product-title">
                                                        <?php echo htmlspecialchars($item['product']['name']); ?>
                                                    </h6>
                                                    <span class="category-badge">
                                                        <?php echo ucfirst($item['product']['category']); ?>
                                                    </span>
                                                    <div class="stock-info mt-1">
                                                        <span class="stock-badge">
                                                            <i class="bi bi-check2-circle me-1"></i>
                                                            Stok: <?php echo $item['product']['stock']; ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Harga -->
                                        <div class="col-md-2 text-center">
                                            <span class="price-tag">
                                                Rp <?php echo number_format($item['product']['price'], 0, ',', '.'); ?>
                                            </span>
                                        </div>

                                        <!-- Kontrol Jumlah -->
                                        <div class="col-md-2">
                                            <div class="quantity-control mx-auto">
                                                <button class="quantity-btn minus" 
                                                        onclick="updateQuantity('<?php echo $item['_id']; ?>', 'decrease')">
                                                    <i class="bi bi-dash"></i>
                                                </button>
                                                <span class="quantity-number"><?php echo $item['quantity']; ?></span>
                                                <button class="quantity-btn plus"
                                                        onclick="updateQuantity('<?php echo $item['_id']; ?>', 'increase')">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Subtotal & Hapus -->
                                        <div class="col-md-2">
                                            <div class="d-flex justify-content-end align-items-center">
                                                <span class="subtotal-price me-3">
                                                    Rp <?php echo number_format($subtotal, 0, ',', '.'); ?>
                                                </span>
                                                <button class="remove-btn" 
                                                        onclick="removeItem('<?php echo $item['_id']; ?>')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        <?php else: ?>
                            <div class="empty-cart">
                                <i class="bi bi-cart-x"></i>
                                <h5>Keranjang Belanja Kosong</h5>
                                <p>Anda belum menambahkan produk ke keranjang.</p>
                                <a href="index.php" class="continue-shopping-btn">
                                    <i class="bi bi-arrow-left me-2"></i>Lanjut Belanja
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Belanja -->
            <div class="col-lg-4">
                <div class="summary-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-receipt me-2"></i>Ringkasan Belanja
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="summary-item">
                            <span>Total Item</span>
                            <span class="fw-bold">
                                <?php echo array_sum(array_column($cart_items, 'quantity')); ?> barang
                            </span>
                        </div>
                        <div class="summary-item">
                            <span>Total Harga</span>
                            <span class="total-price">
                                Rp <?php echo number_format($total, 0, ',', '.'); ?>
                            </span>
                        </div>
                        <a href="address.php" class="checkout-btn mt-3" 
                                <?php echo empty($cart_items) ? 'disabled' : ''; ?>>
                            <i class="bi bi-credit-card me-2"></i>
                            Lanjut ke Pembayaran
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateQuantity(cartId, action) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'update_cart.php';
    
    const cartIdInput = document.createElement('input');
    cartIdInput.type = 'hidden';
    cartIdInput.name = 'cart_id';
    cartIdInput.value = cartId;
    
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = action;
    
    form.appendChild(cartIdInput);
    form.appendChild(actionInput);
    document.body.appendChild(form);
    form.submit();
}

function removeItem(cartId) {
    if (confirm('Apakah Anda yakin ingin menghapus produk ini dari keranjang?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'update_cart.php';
        
        const cartIdInput = document.createElement('input');
        cartIdInput.type = 'hidden';
        cartIdInput.name = 'cart_id';
        cartIdInput.value = cartId;
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'remove';
        
        form.appendChild(cartIdInput);
        form.appendChild(actionInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?php include 'includes/footer.php'; ?> 