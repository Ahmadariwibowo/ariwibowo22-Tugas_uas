<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

// Fetch statistics
try {
    // Monthly revenue
    $monthlyRevenue = [];
    $orders = $database->orders->find(['status' => 'completed']);
    foreach ($orders as $order) {
        $month = date('M Y', $order->created_at->toDateTime()->getTimestamp());
        if (!isset($monthlyRevenue[$month])) {
            $monthlyRevenue[$month] = 0;
        }
        $monthlyRevenue[$month] += $order->total_amount;
    }

    // Product categories
    $categories = [];
    $products = $database->products->find();
    foreach ($products as $product) {
        if (!isset($categories[$product->category])) {
            $categories[$product->category] = 0;
        }
        $categories[$product->category]++;
    }
} catch (Exception $e) {
    error_log($e->getMessage());
}

include 'includes/header.php';
?>

<div class="admin-container">
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="container-fluid">
            <!-- Monthly Revenue Chart -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Pendapatan Bulanan</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Product Categories -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Kategori Produk</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="categoryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_keys($monthlyRevenue)); ?>,
        datasets: [{
            label: 'Pendapatan Bulanan',
            data: <?php echo json_encode(array_values($monthlyRevenue)); ?>,
            borderColor: '#4e73df',
            tension: 0.1
        }]
    }
});

// Category Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_keys($categories)); ?>,
        datasets: [{
            data: <?php echo json_encode(array_values($categories)); ?>,
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e']
        }]
    }
});
</script>

<?php include 'includes/footer.php'; ?> 