<?php
require_once 'config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Ambil data keranjang dan total
try {
    $cart_items = $database->cart->aggregate([
        [
            '$match' => ['user_id' => $_SESSION['user_id']]
        ],
        [
            '$lookup' => [
                'from' => 'products',
                'localField' => 'product_id',
                'foreignField' => '_id',
                'as' => 'product'
            ]
        ],
        ['$unwind' => '$product']
    ])->toArray();

    $total = 0;
    foreach ($cart_items as $item) {
        $total += $item['product']['price'] * $item['quantity'];
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    header('Location: cart.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Casual Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Navbar Checkout Style */
        .checkout-navbar {
            background: #fff;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
            padding: 0.5rem 0;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .checkout-logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            text-decoration: none;
            color: #ee4d2d;
        }

        .checkout-logo img {
            height: 40px;
        }

        .checkout-title {
            font-size: 1.2rem;
            color: #222;
            margin: 0;
            padding-left: 1rem;
            border-left: 1px solid #ddd;
        }

        /* Checkout Content Style */
        .checkout-container {
            margin-top: 80px;
            background: #f5f5f5;
            min-height: calc(100vh - 80px);
            padding: 1.5rem 0;
        }

        .product-item {
            background: #fff;
            border-radius: 4px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border: 1px solid #eee;
        }

        .quantity-badge {
            background: #ee4d2d;
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 2px;
            font-size: 0.8rem;
        }

        .summary-card {
            background: #fff;
            border-radius: 4px;
            padding: 1rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .total-amount {
            color: #ee4d2d;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .checkout-btn {
            background: #ee4d2d;
            color: white;
            border: none;
            width: 100%;
            padding: 0.8rem;
            border-radius: 4px;
            font-weight: bold;
            margin-top: 1rem;
        }

        .checkout-btn:hover {
            background: #d73211;
        }

        /* Checkbox Style */
        .product-checkbox {
            width: 18px;
            height: 18px;
            margin-right: 1rem;
        }

        /* Price Style */
        .original-price {
            text-decoration: line-through;
            color: #999;
            font-size: 0.9rem;
        }

        .discounted-price {
            color: #ee4d2d;
            font-weight: bold;
        }

        /* Form Styles */
        .form-label {
            font-weight: 500;
            color: #333;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #ee4d2d;
            box-shadow: 0 0 0 0.2rem rgba(238, 77, 45, 0.15);
        }

        /* Shipping & Payment Options */
        .shipping-options,
        .payment-options {
            padding: 0.5rem;
        }

        .form-check {
            padding: 1rem;
            border: 1px solid #eee;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .form-check:hover {
            background-color: #fff9f8;
        }

        .form-check-input:checked ~ .form-check-label {
            color: #ee4d2d;
        }

        .form-check-input:checked {
            background-color: #ee4d2d;
            border-color: #ee4d2d;
        }

        .shipping-cost {
            font-weight: 500;
            color: #ee4d2d;
        }

        .payment-group {
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #eee;
        }

        .payment-group:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .payment-group h6 {
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>



    