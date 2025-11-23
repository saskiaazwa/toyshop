<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';
session_start();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
        $stmt->execute([intval($_GET['id'])]);
        echo json_encode($stmt->fetch());
        exit;
    }
    $stmt = $pdo->prepare('SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.created_at DESC');
    $stmt->execute();
    echo json_encode($stmt->fetchAll());
    exit;
}

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized - admin only']);
    exit;
}

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $name = $_POST['name'] ?? '';
    $desc = $_POST['description'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $cat = isset($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $featured = isset($_POST['featured']) ? (int)$_POST['featured'] : 0;
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fname = uniqid() . '_' . basename($_FILES['image']['name']);
        $dest = $uploadDir . $fname;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
            $imagePath = 'uploads/' . $fname;
        }
    }
    $stmt = $pdo->prepare('INSERT INTO products (name, description, price, image_url, category_id, featured) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$name, $desc, $price, $imagePath, $cat, $featured]);
    echo json_encode(['ok' => true, 'id' => $pdo->lastInsertId()]);
    exit;
}

if ($action === 'update') {
    $id = intval($_POST['id'] ?? 0);
    if (!$id) { http_response_code(400); echo json_encode(['error'=>'id required']); exit; }
    $name = $_POST['name'] ?? '';
    $desc = $_POST['description'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $cat = isset($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $featured = isset($_POST['featured']) ? (int)$_POST['featured'] : 0;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $fname = uniqid() . '_' . basename($_FILES['image']['name']);
        $dest = $uploadDir . $fname;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
            $imagePath = 'uploads/' . $fname;
            $stmt = $pdo->prepare('UPDATE products SET image_url = ? WHERE id = ?');
            $stmt->execute([$imagePath, $id]);
        }
    }
    $stmt = $pdo->prepare('UPDATE products SET name=?, description=?, price=?, category_id=?, featured=? WHERE id=?');
    $stmt->execute([$name, $desc, $price, $cat, $featured, $id]);
    echo json_encode(['ok'=>true]);
    exit;
}

if ($action === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    if (!$id) { http_response_code(400); echo json_encode(['error'=>'id required']); exit; }
    $stmt = $pdo->prepare('SELECT image_url FROM products WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row && $row['image_url']) {
        $path = __DIR__ . '/../' . $row['image_url'];
        if (file_exists($path)) @unlink($path);
    }
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$id]);
    echo json_encode(['ok'=>true]);
    exit;
}

http_response_code(400);
echo json_encode(['error'=>'Invalid action']);
?>
