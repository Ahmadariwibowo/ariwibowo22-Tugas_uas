<?php
require_once 'config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header('Location: index.php');
    exit;
}

try {
    $order = $database->orders->findOne([
        '_id' => new MongoDB\BSON\ObjectId($_GET['order_id'])
    ]);

    if (!$order) {
        header('Location: index.php');
        exit;
    }

    // Auto logout setelah 30 detik
    if (isset($_GET['auto_logout']) && $_GET['auto_logout'] === 'true') {
        header("Refresh: 30;url=logout.php");
        $show_countdown = true;
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
    <title>Bukti Pembayaran - Casual Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .receipt-container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .receipt-details {
            margin-bottom: 30px;
        }
        .receipt-table {
            margin-bottom: 30px;
        }
        .receipt-footer {
            text-align: center;
            margin-top: 30px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-light">
    <div class="receipt-container">
        <div class="receipt-header">
            <h4>Bukti Pembayaran</h4>
            <p class="text-muted">Order #<?php echo substr($order['_id'], -8); ?></p>
        </div>

        <div class="receipt-details">
            <div class="row">
                <div class="col-md-6">
                    <h6>Detail Pembeli:</h6>
                    <p>
                        <?php echo $order['shipping_address']['receiver_name']; ?><br>
                        <?php echo $order['shipping_address']['phone']; ?><br>
                        <?php echo $order['shipping_address']['address']; ?><br>
                        <?php echo $order['shipping_address']['city']; ?>, 
                        <?php echo $order['shipping_address']['postal_code']; ?>
                    </p>
                </div>
                <div class="col-md-6 text-end">
                    <h6>Tanggal Order:</h6>
                    <p><?php echo $order['created_at']->toDateTime()->format('d M Y H:i'); ?></p>
                    <h6>Metode Pembayaran:</h6>
                    <p><?php echo ucfirst($order['payment_method']); ?></p>
                </div>
            </div>
        </div>

        <div class="receipt-table">
            <table class="table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th class="text-center">Jumlah</th>
                        <th class="text-end">Harga</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order['items'] as $item): ?>
                    <tr>
                        <td><?php echo $item['product_name']; ?></td>
                        <td class="text-center"><?php echo $item['quantity']; ?></td>
                        <td class="text-end">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                        <td class="text-end">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" class="text-end">Subtotal:</td>
                        <td class="text-end">Rp <?php echo number_format($order['total_amount'] - $order['shipping_fee'], 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end">Ongkos Kirim:</td>
                        <td class="text-end">Rp <?php echo number_format($order['shipping_fee'], 0, ',', '.'); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                        <td class="text-end"><strong>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="receipt-footer">
            <p class="text-muted">Terima kasih telah berbelanja di Casual Store</p>
            <?php if (isset($show_countdown)): ?>
            <div class="alert alert-warning mt-3">
                <i class="bi bi-clock"></i> 
                Anda akan logout otomatis dalam <span id="countdown">30</span> detik
            </div>
            <?php endif; ?>
            <div class="no-print mt-4">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="bi bi-printer"></i> Cetak
                </button>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="bi bi-house"></i> Kembali ke Beranda
                </a>
                <a href="logout.php" class="btn btn-danger">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <?php if (isset($show_countdown)): ?>
    <script>
    let timeLeft = 30;
    const countdownElement = document.getElementById('countdown');

    const countdown = setInterval(function() {
        timeLeft--;
        countdownElement.textContent = timeLeft;
        
        if (timeLeft <= 0) {
            clearInterval(countdown);
        }
    }, 1000);

    // Jika user mencetak, tambah waktu countdown
    window.onbeforeprint = function() {
        timeLeft += 30;
        countdownElement.textContent = timeLeft;
    };
    </script>
    <?php endif; ?>
</body>
</html> 