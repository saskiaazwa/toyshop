<?php
require_once __DIR__ . '/api/db.php';
$email = 'admin@toy.local';
$pass = 'admin123';
$role = 'admin';
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
if ($stmt->fetch()) { echo 'Admin already exists'; exit; }
$hash = password_hash($pass, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO users (email, password_hash, full_name, role) VALUES (?, ?, ?, ?)');
$stmt->execute([$email, $hash, 'Site Admin', $role]);
echo 'Admin created: ' . $email . ' / ' . $pass;
?>