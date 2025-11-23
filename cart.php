<?php
session_start();
require_once __DIR__ . '/api/db.php';

// Pastikan user login
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'user') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data cart + produk
$stmt = $pdo->prepare("
    SELECT c.id AS cart_id, c.qty, p.id AS product_id, p.name, p.price, p.image_url
    FROM carts c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total harga
$total = 0;
foreach ($items as $item) {
    $total += $item['price'] * $item['qty'];
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart - Toy Universe</title>
    <link rel="stylesheet" href="assets/style.css?v=1">
    <style>
        .cart-item {
            display: flex;
            gap: 12px;
            background: #fff;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 10px;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .cart-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        .cart-info {
            flex: 1;
        }
        .cart-info h4 {
            margin: 0 0 4px;
        }
        .qty-controls {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .qty-btn {
            width: 24px;
            height: 24px;
            background: #ff80b5;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            line-height: 1;
            text-align: center;
        }
        .qty-btn:hover {
            background: #ff4d94;
        }
        .cart-actions {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .btn {
            display: inline-block;
            padding: 6px 10px;
            background: #ff80b5;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-align: center;
            font-size: 0.9rem;
        }
        .btn:hover {
            background: #ff4d94;
        }
    </style>
</head>
<body>
<div class="container">
    <header class="header">
        <div class="brand">
            <strong>Toy Universe</strong>
            <span class="small">Hello, <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></span>
        </div>
        <div class="topbar">
            <a href="user.php" class="btn ghost">Back</a>
            <a href="logout.php" class="btn ghost">Logout</a>
        </div>
    </header>

    <h3>🛒 My Cart</h3>

    <?php if (empty($items)): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <?php foreach ($items as $item): ?>
            <div class="cart-item">
                <img src="<?= htmlspecialchars($item['image_url'] ?: 'assets/img/toy1.svg') ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                <div class="cart-info">
                    <h4><?= htmlspecialchars($item['name']) ?></h4>
                    <div class="qty-controls">
                        <form action="update_cart_qty.php" method="POST" style="display:inline;">
                            <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                            <input type="hidden" name="action" value="decrease">
                            <button type="submit" class="qty-btn">−</button>
                        </form>
                        <span><?= $item['qty'] ?></span>
                        <form action="update_cart_qty.php" method="POST" style="display:inline;">
                            <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                            <input type="hidden" name="action" value="increase">
                            <button type="submit" class="qty-btn">+</button>
                        </form>
                    </div>
                    <p>$<?= number_format($item['price'], 2) ?> each</p>
                    <strong>Total: $<?= number_format($item['price'] * $item['qty'], 2) ?></strong>
                </div>
                <div class="cart-actions">
                    <form action="remove_from_cart.php" method="POST">
                        <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                        <button type="submit" class="btn">Remove</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>

    <h3>Grand Total: $<?= number_format($total, 2) ?></h3>
<form action="checkout.php" method="POST" style="margin-top: 20px; text-align: right;">
    <button type="submit" class="btn">Checkout 🛍️</button>
</form>

</div>
</body>
</html>
