<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

// Fetch products
try {
    $products = $database->products->find();
} catch (Exception $e) {
    $products = [];
    error_log($e->getMessage());
}

include 'includes/header.php';
?>

<div class="admin-container">
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Produk</h5>
                    <a href="add_product.php" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Tambah Produk
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Gambar</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <img src="../<?php echo htmlspecialchars($product->image); ?>" 
                                             alt="<?php echo htmlspecialchars($product->name); ?>"
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    </td>
                                    <td><?php echo htmlspecialchars($product->name); ?></td>
                                    <td><?php echo htmlspecialchars($product->category); ?></td>
                                    <td>Rp <?php echo number_format($product->price, 0, ',', '.'); ?></td>
                                    <td><?php echo $product->stock; ?></td>
                                    <td>
                                        <a href="edit_product.php?id=<?php echo $product->_id; ?>" 
                                           class="btn btn-sm btn-info">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="delete_product.php?id=<?php echo $product->_id; ?>" 
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                            <i class="bi bi-trash"></i>
                                        </a>
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