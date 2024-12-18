<?php
require_once 'config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id']) && isset($_POST['action'])) {
    $cart_id = $_POST['cart_id'];
    $action = $_POST['action'];

    try {
        switch ($action) {
            case 'increase':
                $database->cart->updateOne(
                    ['_id' => new MongoDB\BSON\ObjectId($cart_id)],
                    ['$inc' => ['quantity' => 1]]
                );
                break;

            case 'decrease':
                // Ambil quantity saat ini
                $cart_item = $database->cart->findOne(['_id' => new MongoDB\BSON\ObjectId($cart_id)]);
                if ($cart_item && $cart_item->quantity > 1) {
                    $database->cart->updateOne(
                        ['_id' => new MongoDB\BSON\ObjectId($cart_id)],
                        ['$inc' => ['quantity' => -1]]
                    );
                } else {
                    // Hapus item jika quantity akan menjadi 0
                    $database->cart->deleteOne(['_id' => new MongoDB\BSON\ObjectId($cart_id)]);
                }
                break;

            case 'remove':
                $database->cart->deleteOne(['_id' => new MongoDB\BSON\ObjectId($cart_id)]);
                break;
        }

        header('Location: cart.php?success=1');
    } catch (Exception $e) {
        error_log($e->getMessage());
        header('Location: cart.php?error=1');
    }
} else {
    header('Location: cart.php');
}
exit; 