<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$success = '';

// Ambil data produk yang akan diedit
if (isset($_GET['id'])) {
    try {
        $product = $database->products->findOne(['_id' => new MongoDB\BSON\ObjectId($_GET['id'])]);
        if (!$product) {
            header('Location: products.php?error=Produk tidak ditemukan');
            exit;
        }
    } catch (Exception $e) {
        header('Location: products.php?error=ID produk tidak valid');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $description = $_POST['description'];

    $updateData = [
        'name' => $name,
        'category' => $category,
        'price' => $price,
        'stock' => $stock,
        'description' => $description
    ];

    // Handle file upload jika ada gambar baru
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/images/products/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            $updateData['image'] = 'assets/images/products/' . $fileName;
        } else {
            $error = 'Gagal mengupload gambar';
        }
    }

    if (!$error) {
        try {
            $result = $database->products->updateOne(
                ['_id' => new MongoDB\BSON\ObjectId($_GET['id'])],
                ['$set' => $updateData]
            );

            if ($result->getModifiedCount()) {
                header('Location: products.php?success=Produk berhasil diperbarui');
                exit;
            } else {
                $error = 'Tidak ada perubahan pada produk';
            }
        } catch (Exception $e) {
            $error = 'Terjadi kesalahan, silakan coba lagi';
            error_log($e->getMessage());
        }
    }
}

include 'includes/header.php';
?>

<div class="admin-container">
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Produk</h5>
                    <a href="products.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="edit_product.php?id=<?php echo $_GET['id']; ?>" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama Produk</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo htmlspecialchars($product->name); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="category" class="form-label">Kategori</label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="shirt" <?php echo $product->category === 'shirt' ? 'selected' : ''; ?>>Baju</option>
                                        <option value="pants" <?php echo $product->category === 'pants' ? 'selected' : ''; ?>>Celana</option>
                                        <option value="jacket" <?php echo $product->category === 'jacket' ? 'selected' : ''; ?>>Jaket</option>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Harga</label>
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="number" class="form-control" id="price" name="price" 
                                                       value="<?php echo $product->price; ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="stock" class="form-label">Stok</label>
                                            <input type="number" class="form-control" id="stock" name="stock" 
                                                   value="<?php echo $product->stock; ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Deskripsi</label>
                                    <textarea class="form-control" id="description" name="description" 
                                              rows="4" required><?php echo htmlspecialchars($product->description); ?></textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Gambar Saat Ini</label>
                                            <img src="../<?php echo htmlspecialchars($product->image); ?>" 
                                                 alt="Current product image" 
                                                 class="img-fluid rounded mb-2">
                                        </div>

                                        <div class="mb-3">
                                            <label for="image" class="form-label">Upload Gambar Baru</label>
                                            <input type="file" class="form-control" id="image" name="image" 
                                                   accept="image/*" onchange="previewImage(this)">
                                            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar</small>
                                            <img id="preview" src="#" alt="Preview" 
                                                 class="img-fluid rounded mt-2" style="display: none;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include 'includes/footer.php'; ?> 