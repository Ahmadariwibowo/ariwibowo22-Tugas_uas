<?php
require_once 'config/database.php';
session_start();

// Cek apakah ada ID produk
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

// Ambil detail produk
try {
    $product = $database->products->findOne([
        '_id' => new MongoDB\BSON\ObjectId($_GET['id'])
    ]);

    if (!$product) {
        header('Location: index.php');
        exit;
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product->name); ?> - Casual Store</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f4f4f4;
        }

        .product-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            gap: 30px;
        }

        .product-image {
            flex: 0 0 500px;
        }

        .product-image img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            object-fit: cover;
        }

        .product-info {
            flex: 1;
        }

        .product-title {
            font-size: 24px;
            margin: 0 0 15px 0;
            color: #2c3e50;
        }

        .product-price {
            font-size: 28px;
            color: #e74c3c;
            margin: 15px 0;
            font-weight: bold;
        }

        .product-description {
            color: #666;
            line-height: 1.6;
            margin: 20px 0;
        }

        .product-stock {
            color: #27ae60;
            font-weight: bold;
            margin: 15px 0;
        }

        .product-category {
            background: #f8f9fa;
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-block;
            color: #666;
            margin: 10px 0;
        }

        .add-to-cart-form {
            margin-top: 20px;
        }

        .quantity-input {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .quantity-input input {
            width: 60px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
        }

        .add-to-cart-btn {
            background: #2c3e50;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }

        .add-to-cart-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #95a5a6;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .product-container {
                flex-direction: column;
            }

            .product-image {
                flex: none;
            }
        }
    </style>
</head>
<body>
    <div style="max-width: 1200px; margin: 0 auto;">
        <a href="index.php" class="back-btn">← Kembali</a>
    </div>

    <div class="product-container">
        <div class="product-image">
            <img src="<?php echo htmlspecialchars($product->image); ?>" 
                 alt="<?php echo htmlspecialchars($product->name); ?>">
        </div>

        <div class="product-info">
            <h1 class="product-title"><?php echo htmlspecialchars($product->name); ?></h1>
            
            <div class="product-category">
                <?php 
                    $categories = [
                        'shirt' => 'Baju',
                        'pants' => 'Celana',
                        'jacket' => 'Jaket'
                    ];
                    echo $categories[$product->category] ?? $product->category;
                ?>
            </div>

            <div class="product-price">
                Rp <?php echo number_format($product->price, 0, ',', '.'); ?>
            </div>

            <div class="product-stock">
                Stok: <?php echo $product->stock; ?> unit
            </div>

            <div class="product-description">
                <?php echo nl2br(htmlspecialchars($product->description)); ?>
            </div>

            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" action="add_to_cart.php" class="add-to-cart-form">
                    <input type="hidden" name="product_id" value="<?php echo $product->_id; ?>">
                    
                    <div class="quantity-input">
                        <label for="quantity">Jumlah:</label>
                        <input type="number" 
                               id="quantity" 
                               name="quantity" 
                               value="1" 
                               min="1" 
                               max="<?php echo $product->stock; ?>"
                               required>
                    </div>

                    <button type="submit" 
                            class="add-to-cart-btn"
                            <?php echo $product->stock <= 0 ? 'disabled' : ''; ?>>
                        <?php echo $product->stock > 0 ? 'Tambah ke Keranjang' : 'Stok Habis'; ?>
                    </button>
                </form>
            <?php else: ?>
                <a href="login.php" class="add-to-cart-btn" style="display: block; text-align: center; text-decoration: none;">
                    Login untuk Membeli
                </a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 