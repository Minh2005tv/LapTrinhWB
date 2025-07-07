<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=selling_shoes;charset=utf8mb4", 'root', '');

// Giả lập user_id (sau này dùng $_SESSION['user_id'] nếu có login)
$user_id = null;

// Tạo cart nếu chưa có
if (!isset($_SESSION['cart_id'])) {
    $stmt = $pdo->prepare("INSERT INTO carts (user_id) VALUES (:user_id)");
    $stmt->execute(['user_id' => $user_id]);
    $_SESSION['cart_id'] = $pdo->lastInsertId();
}

$cart_id = $_SESSION['cart_id'];

$product_id = $_POST['product_id'];
$size = $_POST['size'] ?? 'M';

// Kiểm tra nếu sản phẩm đã có trong giỏ thì tăng số lượng
$check = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = ? AND product_id = ? AND size = ?");
$check->execute([$cart_id, $product_id, $size]);
$exist = $check->fetch();

if ($exist) {
    $stmt = $pdo->prepare("UPDATE cart_items SET quantity = quantity + 1 WHERE id = ?");
    $stmt->execute([$exist['id']]);
} else {
    $stmt = $pdo->prepare("INSERT INTO cart_items (cart_id, product_id, size, quantity) VALUES (?, ?, ?, 1)");
    $stmt->execute([$cart_id, $product_id, $size]);
}

echo json_encode(['success' => true]);
