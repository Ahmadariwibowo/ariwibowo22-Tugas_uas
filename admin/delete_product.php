<?php
require_once '../config/database.php';
session_start();

// Cek apakah user sudah login dan adalah admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

if (isset($_GET['id'])) {
    try {
        $result = $database->products->deleteOne(['_id' => new MongoDB\BSON\ObjectId($_GET['id'])]);
        
        if ($result->getDeletedCount()) {
            header('Location: dashboard.php?success=Produk berhasil dihapus');
        } else {
            header('Location: dashboard.php?error=Produk tidak ditemukan');
        }
    } catch (Exception $e) {
        header('Location: dashboard.php?error=Gagal menghapus produk');
        error_log($e->getMessage());
    }
} else {
    header('Location: dashboard.php?error=ID produk tidak valid');
}
exit; 