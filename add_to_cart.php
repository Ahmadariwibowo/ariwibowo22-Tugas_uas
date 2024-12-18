<?php
require_once 'config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'redirect' => 'login.php']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $product_id = $data['product_id'];
    $quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;

    try {
        // Cek stok produk
        $product = $database->products->findOne([
            '_id' => new MongoDB\BSON\ObjectId($product_id)
        ]);

        if (!$product || $product->stock < $quantity) {
            echo json_encode(['success' => false, 'message' => 'Stok tidak mencukupi']);
            exit;
        }

        // Cek apakah produk sudah ada di keranjang
        $existing_item = $database->cart->findOne([
            'user_id' => $_SESSION['user_id'],
            'product_id' => (string)$product_id
        ]);

        if ($existing_item) {
            // Update quantity jika produk sudah ada
            $database->cart->updateOne(
                ['_id' => $existing_item->_id],
                ['$inc' => ['quantity' => $quantity]]
            );
        } else {
            // Tambah produk baru ke keranjang
            $database->cart->insertOne([
                'user_id' => $_SESSION['user_id'],
                'product_id' => (string)$product_id,
                'quantity' => $quantity,
                'created_at' => new MongoDB\BSON\UTCDateTime()
            ]);
        }

        // Hitung total item di keranjang
        $cart_count = $database->cart->count([
            'user_id' => $_SESSION['user_id']
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang',
            'cart_count' => $cart_count
        ]);
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Gagal menambahkan ke keranjang']);
    }
    exit;
}