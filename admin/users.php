<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

// Fetch users
try {
    $users = $database->users->find(['is_admin' => false]);
} catch (Exception $e) {
    $users = [];
    error_log($e->getMessage());
}

include 'includes/header.php';
?>

<div class="admin-container">
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Pengguna</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Tanggal Daftar</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user->name); ?></td>
                                    <td><?php echo htmlspecialchars($user->email); ?></td>
                                    <td><?php echo date('d/m/Y', $user->created_at->toDateTime()->getTimestamp()); ?></td>
                                    <td>
                                        <span class="badge bg-success">Aktif</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" 
                                                onclick="blockUser('<?php echo $user->_id; ?>')">
                                            <i class="bi bi-slash-circle"></i>
                                        </button>
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