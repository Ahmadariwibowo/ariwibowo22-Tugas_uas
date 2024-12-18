<?php
require_once '../config/database.php';
session_start();

// Cek apakah user sudah login dan adalah admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = (float)$_POST['price'];
    $stock = (int)$_POST['stock'];
    $description = $_POST['description'];

    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/images/products/';
        
        // Buat direktori jika belum ada
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate nama file unik
        $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
        $uploadFile = $uploadDir . $fileName;
        $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

        // Cek apakah file adalah gambar
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $validExtensions)) {
            $error = 'Hanya file JPG, JPEG, PNG & GIF yang diizinkan';
        } else {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                $image = 'assets/images/products/' . $fileName;
            } else {
                $error = 'Gagal mengupload gambar';
            }
        }
    } else {
        $error = 'Gambar produk wajib diupload';
    }

    if (!$error) {
        try {
            $result = $database->products->insertOne([
                'name' => $name,
                'category' => $category,
                'price' => $price,
                'stock' => $stock,
                'description' => $description,
                'image' => $image,
                'created_at' => new MongoDB\BSON\UTCDateTime()
            ]);

            if ($result->getInsertedCount()) {
                header('Location: dashboard.php?success=Produk berhasil ditambahkan');
                exit;
            } else {
                $error = 'Gagal menambahkan produk';
            }
        } catch (Exception $e) {
            $error = 'Terjadi kesalahan, silakan coba lagi';
            error_log($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f4f4f4;
        }

        .form-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        textarea {
            height: 100px;
            resize: vertical;
        }

        .btn-submit {
            background: #27ae60;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-back {
            background: #95a5a6;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
        }

        .error-message {
            background: #e74c3c;
            color: white;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .preview-image {
            max-width: 200px;
            margin-top: 10px;
            border-radius: 4px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Tambah Produk Baru</h2>

        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="add_product.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Nama Produk</label>
                <input type="text" id="name" name="name" required 
                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="category">Kategori</label>
                <select id="category" name="category" required>
                    <option value="shirt" <?php echo (isset($_POST['category']) && $_POST['category'] === 'shirt') ? 'selected' : ''; ?>>Baju</option>
                    <option value="pants" <?php echo (isset($_POST['category']) && $_POST['category'] === 'pants') ? 'selected' : ''; ?>>Celana</option>
                    <option value="jacket" <?php echo (isset($_POST['category']) && $_POST['category'] === 'jacket') ? 'selected' : ''; ?>>Jaket</option>
                </select>
            </div>

            <div class="form-group">
                <label for="price">Harga</label>
                <input type="number" id="price" name="price" min="0" required
                       value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="stock">Stok</label>
                <input type="number" id="stock" name="stock" min="0" required
                       value="<?php echo isset($_POST['stock']) ? htmlspecialchars($_POST['stock']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="image">Gambar Produk</label>
                <input type="file" id="image" name="image" accept="image/*" required onchange="previewImage(this)">
                <img id="preview" src="#" alt="Preview gambar" class="preview-image">
            </div>

            <div class="form-actions">
                <a href="dashboard.php" class="btn-back">Kembali</a>
                <button type="submit" class="btn-submit">Simpan Produk</button>
            </div>
        </form>
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
</body>
</html> 