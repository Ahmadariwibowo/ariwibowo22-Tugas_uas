<?php
require_once 'config/database.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = 'Password tidak cocok';
    } else {
        try {
            // Cek apakah email sudah terdaftar
            $existingUser = $database->users->findOne(['email' => $email]);
            
            if ($existingUser) {
                $error = 'Email sudah terdaftar';
            } else {
                // Simpan password tanpa hashing
                $result = $database->users->insertOne([
                    'name' => $name,
                    'email' => $email,
                    'password' => $password, // password langsung disimpan
                    'is_admin' => false,
                    'created_at' => new MongoDB\BSON\UTCDateTime()
                ]);

                if ($result->getInsertedCount()) {
                    $success = 'Pendaftaran berhasil! Silakan masuk';
                    header('refresh:2;url=login.php');
                } else {
                    $error = 'Gagal mendaftar, silakan coba lagi';
                }
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
    <title>Daftar - Casual Store</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .register-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .btn-register {
            width: 100%;
            padding: 10px;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .error {
            color: #e74c3c;
            margin-bottom: 15px;
            text-align: center;
        }

        .success {
            color: #27ae60;
            margin-bottom: 15px;
            text-align: center;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: #3498db;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1>Daftar</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn-register">Daftar</button>
        </form>

        <div class="login-link">
            Sudah punya akun? <a href="login.php">Masuk disini</a>
        </div>
    </div>
</body>
</html> 