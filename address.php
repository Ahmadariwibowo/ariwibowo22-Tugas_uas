<?php
require_once 'config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Cek apakah ada data alamat yang dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address_data = [
        'user_id' => $_SESSION['user_id'],
        'receiver_name' => $_POST['receiver_name'],
        'phone' => $_POST['phone'],
        'address' => $_POST['address'],
        'province' => $_POST['province'],
        'city' => $_POST['city'],
        'district' => $_POST['district'],
        'postal_code' => $_POST['postal_code'],
        'created_at' => new MongoDB\BSON\UTCDateTime()
    ];

    try {
        $result = $database->addresses->insertOne($address_data);
        if ($result->getInsertedCount()) {
            $_SESSION['selected_address_id'] = (string)$result->getInsertedId();
            header('Location: payment.php');
            exit;
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        $error = 'Gagal menyimpan alamat';
    }
}

include 'includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-geo-alt me-2"></i>
                        Alamat Pengiriman
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="address.php">
                        <!-- Nama Penerima & No. Telepon -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Penerima <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       name="receiver_name" 
                                       placeholder="Masukkan nama penerima"
                                       required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                                <input type="tel" 
                                       class="form-control" 
                                       name="phone" 
                                       placeholder="Contoh: 08123456789"
                                       pattern="[0-9]{10,13}"
                                       title="Masukkan nomor telepon yang valid (10-13 digit)"
                                       required>
                            </div>
                        </div>

                        <!-- Alamat Lengkap -->
                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea class="form-control" 
                                      name="address" 
                                      rows="3" 
                                      placeholder="Masukkan alamat lengkap (nama jalan, nomor rumah, RT/RW)"
                                      required></textarea>
                            <small class="text-muted">
                                Tuliskan alamat selengkap mungkin (nama jalan, nomor rumah, RT/RW, patokan)
                            </small>
                        </div>

                        <!-- Provinsi & Kota -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       name="province" 
                                       placeholder="Masukkan nama provinsi"
                                       required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kota/Kabupaten <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       name="city" 
                                       placeholder="Masukkan nama kota/kabupaten"
                                       required>
                            </div>
                        </div>

                        <!-- Kecamatan & Kode Pos -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       name="district" 
                                       placeholder="Masukkan nama kecamatan"
                                       required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kode Pos <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       name="postal_code" 
                                       placeholder="Masukkan kode pos"
                                       pattern="[0-9]{5}"
                                       title="Kode pos harus 5 digit angka"
                                       required>
                            </div>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>
                                Simpan & Lanjutkan ke Pembayaran
                            </button>
                            <a href="cart.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>
                                Kembali ke Keranjang
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 10px;
}

.card-header {
    border-bottom: 1px solid #eee;
    padding: 15px 20px;
}

.form-label {
    font-weight: 500;
    margin-bottom: 5px;
}

.form-control {
    padding: 10px 15px;
    border-radius: 6px;
    border: 1px solid #ddd;
}

.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
}

.btn {
    padding: 10px 20px;
}

.text-danger {
    color: #dc3545;
}

.text-muted {
    font-size: 0.875rem;
}
</style>

<?php include 'includes/footer.php'; ?>