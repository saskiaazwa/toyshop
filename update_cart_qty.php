<?php
session_start();
require_once __DIR__ . '/api/db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'user') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart_id = intval($_POST['cart_id']);
    $action = $_POST['action'] ?? '';

    // Ambil qty sekarang
    $stmt = $pdo->prepare("SELECT qty FROM carts WHERE id = ? AND user_id = ?");
    $stmt->execute([$cart_id, $_SESSION['user_id']]);
    $cart = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cart) {
        $newQty = $cart['qty'];

        if ($action === 'increase') {
            $newQty++;
        } elseif ($action === 'decrease') {
            $newQty--;
        }

        if ($newQty <= 0) {
            // Hapus item dari cart
            $delete = $pdo->prepare("DELETE FROM carts WHERE id = ? AND user_id = ?");
            $delete->execute([$cart_id, $_SESSION['user_id']]);
        } else {
            // Update qty
            $update = $pdo->prepare("UPDATE carts SET qty = ? WHERE id = ? AND user_id = ?");
            $update->execute([$newQty, $cart_id, $_SESSION['user_id']]);
        }
    }
}

header('Location: cart.php');
exit;
