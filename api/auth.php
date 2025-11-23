<?php
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';
session_start();
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

if ($method === 'POST') {
    $action = $_GET['action'] ?? ($input['action'] ?? '');
    if ($action === 'register') {
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $full = $input['full_name'] ?? null;
        if (!$email || !$password) { http_response_code(400); echo json_encode(['error'=>'Email & password required']); exit; }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare('INSERT INTO users (email, password_hash, full_name, role) VALUES (?, ?, ?, ?)');
            $stmt->execute([$email, $hash, $full, 'user']);
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_role'] = 'user';
            $_SESSION['user_name'] = $full;
            echo json_encode(['ok'=>true, 'id'=>$_SESSION['user_id']]);
        } catch (PDOException $e) {
            if ($e->errorInfo[1] === 1062) { http_response_code(400); echo json_encode(['error'=>'Email already registered']); }
            else { http_response_code(500); echo json_encode(['error'=>$e->getMessage()]); }
        }
        exit;
    } elseif ($action === 'login') {
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        if (!$email || !$password) { http_response_code(400); echo json_encode(['error'=>'Email & password required']); exit; }
        $stmt = $pdo->prepare('SELECT id, password_hash, role, full_name FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['password_hash'])) {
            http_response_code(400); echo json_encode(['error'=>'Invalid credentials']); exit;
        }
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['full_name'];
        echo json_encode(['ok'=>true, 'id'=>$user['id'], 'role'=>$user['role'], 'full_name'=>$user['full_name']]);
        exit;
    } elseif ($action === 'logout') {
        session_destroy();
        echo json_encode(['ok'=>true]);
        exit;
    }
}

if ($method === 'GET') {
    echo json_encode(['logged_in' => isset($_SESSION['user_id']), 'user_id' => $_SESSION['user_id'] ?? null, 'role' => $_SESSION['user_role'] ?? null, 'name' => $_SESSION['user_name'] ?? null]);
    exit;
}

http_response_code(405);
echo json_encode(['error'=>'Method not allowed']);
?>