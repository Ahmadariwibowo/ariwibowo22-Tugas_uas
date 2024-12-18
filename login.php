<?php
require_once 'config/database.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    try {
        $query = ['email' => $email];
        if ($role === 'admin') {
            $query['is_admin'] = true;
        } else {
            $query['is_admin'] = false;
        }
        
        $user = $database->users->findOne($query);
        
        if ($user && $user->password === $password) {
            $_SESSION['user_id'] = (string)$user->_id;
            $_SESSION['is_admin'] = $user->is_admin ?? false;
            
            if ($user->is_admin) {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $error = $role === 'admin' ? 
                     'Email atau password admin tidak valid' : 
                     'Email atau password pembeli tidak valid';
        }
    } catch (Exception $e) {
        $error = 'Terjadi kesalahan, silakan coba lagi';
        error_log($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Casual Store</title>
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

        .login-container {
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

        .btn-login {
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

        .register-link {
            text-align: center;
            margin-top: 20px;
        }

        .register-link a {
            color: #3498db;
            text-decoration: none;
        }

        .role-selector {
            display: flex;
            margin-bottom: 20px;
            gap: 10px;
        }

        .role-option {
            flex: 1;
            padding: 10px;
            text-align: center;
            border: 2px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .role-option.active {
            border-color: #2c3e50;
            background: #2c3e50;
            color: white;
        }

        .role-option:hover {
            border-color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Masuk</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="role-selector">
            <div class="role-option active" data-role="buyer">Pembeli</div>
            <div class="role-option" data-role="admin">Admin</div>
        </div>

        <form method="POST" action="login.php">
            <input type="hidden" name="role" id="role" value="buyer">
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn-login">Masuk</button>
        </form>

        <div class="register-link">
            Belum punya akun? <a href="register.php">Daftar disini</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleOptions = document.querySelectorAll('.role-option');
            const roleInput = document.getElementById('role');

            roleOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // Hapus kelas active dari semua opsi
                    roleOptions.forEach(opt => opt.classList.remove('active'));
                    
                    // Tambah kelas active ke opsi yang dipilih
                    this.classList.add('active');
                    
                    // Update nilai input hidden
                    roleInput.value = this.dataset.role === 'admin' ? 'admin' : 'buyer';
                });
            });
        });
    </script>
</body>
</html> 