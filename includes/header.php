<?php
// Hitung jumlah item di keranjang
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    try {
        $cart_count = $database->cart->count(['user_id' => $_SESSION['user_id']]);
    } catch (Exception $e) {
        error_log($e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casual Store - Fashion Terkini</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    <div class="content-wrapper">
        <!-- Content akan dimulai di sini -->
</body>
</html> 