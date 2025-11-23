<?php
session_start();
require_once __DIR__ . '/api/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'user') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Hapus semua item dari carts user ini
$stmt = $pdo->prepare("DELETE FROM carts WHERE user_id = ?");
$stmt->execute([$user_id]);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Toy Universe</title>
    <link rel="stylesheet" href="assets/style.css?v=1">
    <style>
        .checkout-message {
            text-align: center;
            padding: 40px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 400px;
            margin: 60px auto;
        }
        .checkout-message h2 {
            margin-bottom: 10px;
            color: #ff4d94;
        }
        .btn {
            display: inline-block;
            padding: 8px 14px;
            background: #ff80b5;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover {
            background: #ff4d94;
        }
    </style>
</head>
<body>
    <div class="checkout-message">
        <h2>🎉 Thank You!</h2>
        <p>Your order has been successfully placed.</p>
        <a href="user.php" class="btn">Back to Home</a>
    </div>
</body>
</html>
