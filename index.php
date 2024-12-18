<?php
require_once 'config/database.php';
session_start();

// Fetch products from database
try {
    $products = $database->products->find([], [
        'sort' => ['created_at' => -1],
        'limit' => 12
    ]);
} catch (Exception $e) {
    $products = [];
    error_log("Error fetching products: " . $e->getMessage());
}

include 'includes/header.php';
?>

<div class="container mt-4">
  
  

    <!-- Featured Products -->
    <div class="featured-products">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="section-title">Produk Terbaru</h2>
            <a href="products.php" class="btn btn-outline-dark">Lihat Semua</a>
        </div>
        
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            <?php foreach ($products as $product): ?>
            <div class="col">
                <div class="card h-100 product-card">
                    <img src="<?php echo htmlspecialchars($product->image); ?>" 
                         class="card-img-top product-image" 
                         alt="<?php echo htmlspecialchars($product->name); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($product->name); ?></h5>
                        <p class="card-text text-muted">
                            <?php echo ucfirst($product->category); ?>
                        </p>
                        <p class="card-text fw-bold">
                            Rp <?php echo number_format($product->price, 0, ',', '.'); ?>
                        </p>
                        <div class="d-grid gap-2">
                            <a href="product-detail.php?id=<?php echo $product->_id; ?>" 
                               class="btn btn-outline-dark btn-sm">
                                Detail
                            </a>
                            <?php if ($product->stock > 0): ?>
                                <button onclick="addToCart('<?php echo $product->_id; ?>')" 
                                        class="btn btn-dark btn-sm">
                                    Tambah ke Keranjang
                                </button>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-sm" disabled>
                                    Stok Habis
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
function addToCart(productId) {
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart badge
            const cartBadge = document.querySelector('.cart-icon .badge');
            if (cartBadge) {
                cartBadge.textContent = data.cart_count;
                cartBadge.style.display = 'block';
            }
            
            // Show success message
            alert('Produk berhasil ditambahkan ke keranjang!');
        } else if (data.redirect) {
            window.location.href = data.redirect;
        } else {
            alert(data.message || 'Gagal menambahkan ke keranjang');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan, silakan coba lagi');
    });
}
</script>

<?php include 'includes/footer.php'; ?> 