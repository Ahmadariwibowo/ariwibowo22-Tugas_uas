<?php
require_once 'config/database.php';
session_start();

// Get category from URL parameter with validation
$validCategories = ['all', 'shirt', 'jacket', 'pants'];
$category = isset($_GET['category']) && in_array($_GET['category'], $validCategories) ? $_GET['category'] : 'all';

// Build query based on category
$filter = [];
if ($category !== 'all') {
    $filter['category'] = $category;
}

// Validasi sorting options
$validSortOptions = ['newest', 'price-low', 'price-high', 'name-asc', 'name-desc'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $validSortOptions) ? $_GET['sort'] : 'newest';

try {
    // Sorting options
    $sortOptions = [];
    switch ($sort) {
        case 'price-low':
            $sortOptions = ['price' => 1];
            break;
        case 'price-high':
            $sortOptions = ['price' => -1];
            break;
        case 'name-asc':
            $sortOptions = ['name' => 1];
            break;
        case 'name-desc':
            $sortOptions = ['name' => -1];
            break;
        default:
            $sortOptions = ['created_at' => -1];
    }

    // Cek apakah collection products kosong
    $productsCount = $database->products->countDocuments();
    
    if ($productsCount === 0) {
        // Jika kosong, masukkan sample products
        $database->products->insertMany($sampleProducts);
    }

    // Fetch products dengan filter dan sorting
    $products = $database->products->find($filter, [
        'sort' => $sortOptions,
        'limit' => 50
    ])->toArray();

} catch (Exception $e) {
    error_log("Error fetching products: " . $e->getMessage());
    $products = [];
}

// Category labels
$categoryLabels = [
    'all' => 'Semua Produk',
    'shirt' => 'Baju',
    'jacket' => 'Jaket',
    'pants' => 'Celana'
];

$sampleProducts = [
    // BAJU - Kemeja
    [
        'name' => 'Kemeja Oxford Premium',
        'category' => 'shirt',
        'price' => 459000,
        'stock' => 45,
        'description' => 'Kemeja Oxford premium dengan bahan cotton terbaik',
        'image' => 'assets/aw.jpg'
    ],
    [
        'name' => 'Kemeja Flanel Kotak',
        'category' => 'shirt',
        'price' => 289000,
        'stock' => 38,
        'description' => 'Kemeja flanel dengan motif kotak klasik',
        'image' => 'assets/images/products/baju/flanel-kotak-1.jpg'
    ],
    [
        'name' => 'Kemeja Denim Casual',
        'category' => 'shirt',
        'price' => 379000,
        'stock' => 30,
        'description' => 'Kemeja denim untuk gaya casual',
        'image' => 'assets/images/products/baju/denim-casual-1.jpg'
    ],

    // BAJU - Kaos
    [
        'name' => 'Kaos Premium Cotton',
        'category' => 'shirt',
        'price' => 199000,
        'stock' => 100,
        'description' => 'Kaos dengan bahan cotton premium 30s',
        'image' => 'assets/images/products/baju/premium-cotton-1.jpg'
    ],
    [
        'name' => 'Kaos Polo Classic',
        'category' => 'shirt',
        'price' => 249000,
        'stock' => 75,
        'description' => 'Polo shirt klasik dengan bahan pique cotton',
        'image' => 'assets/images/products/baju/polo-classic-1.jpg'
    ],
    [
        'name' => 'Kaos Henley',
        'category' => 'shirt',
        'price' => 229000,
        'stock' => 60,
        'description' => 'Kaos henley dengan desain minimalis',
        'image' => 'assets/images/products/baju/henley-1.jpg'
    ],

    // JAKET - Denim
    [
        'name' => 'Jaket Denim Vintage',
        'category' => 'jacket',
        'price' => 599000,
        'stock' => 25,
        'description' => 'Jaket denim dengan tampilan vintage',
        'image' => 'assets/images/products/jaket/denim-vintage-1.jpg'
    ],
    [
        'name' => 'Jaket Denim Premium',
        'category' => 'jacket',
        'price' => 789000,
        'stock' => 20,
        'description' => 'Jaket denim premium dengan wash spesial',
        'image' => 'assets/images/products/jaket/denim-premium-1.jpg'
    ],

    // JAKET - Bomber
    [
        'name' => 'Bomber Classic',
        'category' => 'jacket',
        'price' => 499000,
        'stock' => 40,
        'description' => 'Jaket bomber klasik dengan bahan polyester',
        'image' => 'assets/images/products/jaket/bomber-classic-1.jpg'
    ],
    [
        'name' => 'Bomber Premium',
        'category' => 'jacket',
        'price' => 699000,
        'stock' => 30,
        'description' => 'Jaket bomber premium dengan detail mewah',
        'image' => 'assets/images/products/jaket/bomber-premium-1.jpg'
    ],

    // JAKET - Hoodie
    [
        'name' => 'Hoodie Basic',
        'category' => 'jacket',
        'price' => 329000,
        'stock' => 85,
        'description' => 'Hoodie basic dengan bahan fleece',
        'image' => 'assets/images/products/jaket/hoodie-basic-1.jpg'
    ],
    [
        'name' => 'Hoodie Premium',
        'category' => 'jacket',
        'price' => 459000,
        'stock' => 55,
        'description' => 'Hoodie premium dengan bahan cotton fleece',
        'image' => 'assets/images/products/jaket/hoodie-premium-1.jpg'
    ],

    // CELANA - Jeans
    [
        'name' => 'Jeans Slim Fit',
        'category' => 'pants',
        'price' => 459000,
        'stock' => 45,
        'description' => 'Celana jeans dengan potongan slim fit',
        'image' => 'assets/images/products/celana/jeans-slim-1.jpg'
    ],
    [
        'name' => 'Jeans Regular Fit',
        'category' => 'pants',
        'price' => 429000,
        'stock' => 50,
        'description' => 'Celana jeans dengan potongan regular',
        'image' => 'assets/images/products/celana/jeans-regular-1.jpg'
    ],
    [
        'name' => 'Jeans Premium Wash',
        'category' => 'pants',
        'price' => 559000,
        'stock' => 35,
        'description' => 'Celana jeans dengan wash premium',
        'image' => 'assets/images/products/celana/jeans-premium-1.jpg'
    ],

    // CELANA - Chino
    [
        'name' => 'Chino Slim',
        'category' => 'pants',
        'price' => 359000,
        'stock' => 60,
        'description' => 'Celana chino dengan potongan slim',
        'image' => 'assets/images/products/celana/chino-slim-1.jpg'
    ],
    [
        'name' => 'Chino Regular',
        'category' => 'pants',
        'price' => 339000,
        'stock' => 65,
        'description' => 'Celana chino dengan potongan regular',
        'image' => 'assets/images/products/celana/chino-regular-1.jpg'
    ],

    // CELANA - Cargo
    [
        'name' => 'Cargo Tactical',
        'category' => 'pants',
        'price' => 429000,
        'stock' => 40,
        'description' => 'Celana cargo dengan banyak kantong',
        'image' => 'assets/images/products/celana/cargo-tactical-1.jpg'
    ],
    [
        'name' => 'Cargo Premium',
        'category' => 'pants',
        'price' => 489000,
        'stock' => 35,
        'description' => 'Celana cargo dengan bahan premium',
        'image' => 'assets/images/products/celana/cargo-premium-1.jpg'
    ],

    // TAMBAHAN BAJU
    [
        'name' => 'Kemeja Linen Premium',
        'category' => 'shirt',
        'price' => 529000,
        'stock' => 30,
        'description' => 'Kemeja linen dengan bahan premium',
        'image' => 'assets/images/products/baju/linen-premium-1.jpg'
    ],
    [
        'name' => 'Kemeja Batik Modern',
        'category' => 'shirt',
        'price' => 399000,
        'stock' => 40,
        'description' => 'Kemeja batik dengan motif modern',
        'image' => 'assets/images/products/baju/batik-modern-1.jpg'
    ],

    // TAMBAHAN JAKET
    [
        'name' => 'Jaket Parka Premium',
        'category' => 'jacket',
        'price' => 899000,
        'stock' => 25,
        'description' => 'Jaket parka dengan bahan waterproof',
        'image' => 'assets/images/products/jaket/parka-premium-1.jpg'
    ],
    [
        'name' => 'Jaket Varsity Classic',
        'category' => 'jacket',
        'price' => 659000,
        'stock' => 30,
        'description' => 'Jaket varsity dengan desain klasik',
        'image' => 'assets/images/products/jaket/varsity-classic-1.jpg'
    ],

    // TAMBAHAN CELANA
    [
        'name' => 'Celana Formal Premium',
        'category' => 'pants',
        'price' => 589000,
        'stock' => 30,
        'description' => 'Celana formal dengan bahan wool blend',
        'image' => 'assets/images/products/celana/formal-premium-1.jpg'
    ],
    [
        'name' => 'Celana Jogger Street',
        'category' => 'pants',
        'price' => 329000,
        'stock' => 50,
        'description' => 'Celana jogger untuk gaya street',
        'image' => 'assets/images/products/celana/jogger-street-1.jpg'
    ]
];

// Tambahkan created_at dan updated_at untuk setiap produk
foreach ($sampleProducts as &$product) {
    $product['created_at'] = new MongoDB\BSON\UTCDateTime();
    $product['updated_at'] = new MongoDB\BSON\UTCDateTime();
    $product['images'] = [
        $product['image'],
        str_replace('-1.jpg', '-2.jpg', $product['image']),
        str_replace('-1.jpg', '-3.jpg', $product['image']),
        str_replace('-1.jpg', '-4.jpg', $product['image'])
    ];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($categoryLabels[$category]); ?> - Casual Store</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
        }

        .product-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.2s;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-image {
            width: 100%;
            height: 300px;
            overflow: hidden;
            position: relative;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-details {
            padding: 15px;
        }

        .product-title {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .product-price {
            font-size: 20px;
            color: #2c3e50;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .sort-options {
            padding: 20px;
            text-align: right;
        }

        .sort-select {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }

        .category-filter {
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 20px;
        }

        .category-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            background: #f8f9fa;
            cursor: pointer;
        }

        .category-btn.active {
            background: #2c3e50;
            color: white;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <div class="category-filter">
            <?php foreach ($categoryLabels as $key => $label): ?>
                <a href="?category=<?php echo $key; ?>" 
                   class="category-btn <?php echo $category === $key ? 'active' : ''; ?>">
                    <?php echo $label; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="sort-options">
            <select class="sort-select" onchange="window.location.href=this.value">
                <option value="?category=<?php echo $category; ?>&sort=newest" 
                        <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Terbaru</option>
                <option value="?category=<?php echo $category; ?>&sort=price-low" 
                        <?php echo $sort === 'price-low' ? 'selected' : ''; ?>>Harga Terendah</option>
                <option value="?category=<?php echo $category; ?>&sort=price-high" 
                        <?php echo $sort === 'price-high' ? 'selected' : ''; ?>>Harga Tertinggi</option>
                <option value="?category=<?php echo $category; ?>&sort=name-asc" 
                        <?php echo $sort === 'name-asc' ? 'selected' : ''; ?>>Nama A-Z</option>
                <option value="?category=<?php echo $category; ?>&sort=name-desc" 
                        <?php echo $sort === 'name-desc' ? 'selected' : ''; ?>>Nama Z-A</option>
            </select>
        </div>

        <div class="product-grid">
            <?php if (empty($products)): ?>
                <p>Tidak ada produk yang tersedia.</p>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 loading="lazy">
                        </div>
                        <div class="product-details">
                            <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-price">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                            <p>Stok: <?php echo $product['stock']; ?></p>
                            <button onclick="addToCart('<?php echo $product['_id']; ?>')"
                                    <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
                                <?php echo $product['stock'] > 0 ? 'Tambah ke Keranjang' : 'Stok Habis'; ?>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
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
                alert('Produk berhasil ditambahkan ke keranjang!');
                // Update cart count if needed
                const cartCount = document.getElementById('cart-count');
                if (cartCount) {
                    cartCount.textContent = data.cart_count;
                }
            } else {
                alert('Gagal menambahkan produk ke keranjang.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menambahkan ke keranjang.');
        });
    }
    </script>
</body>
</html> 