<?php
require_once 'admin.php'; // chứa hàm getDb()

header('Content-Type: application/json');
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu ID sản phẩm']);
    exit;
}

$id = intval($_GET['id']);
$pdo = getDb();

try {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Xóa sản phẩm thành công']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
}
