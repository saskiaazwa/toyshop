<?php
session_start();
require_once __DIR__ . '/api/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = intval($_POST['product_id']);

    // cek apakah produk sudah ada di cart
    $check = $pdo->prepare("SELECT id, qty FROM carts WHERE user_id = ? AND product_id = ?");
    $check->execute([$user_id, $product_id]);
    $existing = $check->fetch();

    if ($existing) {
        $update = $pdo->prepare("UPDATE carts SET qty = qty + 1 WHERE id = ?");
        $update->execute([$existing['id']]);
    } else {
        $insert = $pdo->prepare("INSERT INTO carts (user_id, product_id, qty) VALUES (?, ?, 1)");
        $insert->execute([$user_id, $product_id]);
    }

    header('Location: user.php');
    exit;
}
