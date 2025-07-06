<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Kết nối CSDL
function getDb() {
    $host = 'localhost';
    $db   = 'selling_shoes';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    return new PDO($dsn, $user, $pass, $opt);
}

// Hàm trả về JSON
function response($success, $msg) {
    echo json_encode(['success' => $success, 'message' => $msg]);
    exit;
}

// Xử lý chỉnh sửa sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $sizes = json_decode($_POST['sizes_raw'] ?? '[]');
    $colors = json_decode($_POST['color'] ?? '[]');
    $date_time = $_POST['date_time'] ?? null;
    $sales = $_POST['sales'] ?? null;

    // Validate
    if ($id <= 0) response(false, 'ID sản phẩm không hợp lệ');
    if ($name === '' || $type === '') response(false, 'Tên và loại không được để trống');
    if (!is_numeric($price) || $price <= 0) response(false, 'Giá phải là số > 0');
    if (!is_array($sizes) || count($sizes) == 0 || count($sizes) > 5) response(false, 'Size tối đa 5');
    if (!is_array($colors) || count($colors) == 0 || count($colors) > 5) response(false, 'Màu tối đa 5');
    foreach ($sizes as $sz) if (!is_numeric($sz) || $sz <= 0) response(false, 'Size phải là số dương');
    foreach ($colors as $cl) if (trim($cl) === '') response(false, 'Màu không được rỗng');

    // Validate promotion logic
    if (($date_time && !$sales) || (!$date_time && $sales)) {
        response(false, 'Nếu đã nhập "Date & Time" thì phải nhập cả "Sales", và ngược lại.');
    }

    $pdo = getDb();
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, type = ?, price = ?, date_time = ?, sales = ? WHERE id = ?");
        $stmt->execute([$name, $type, $price, $date_time ?: null, $sales ?: null, $id]);

        $pdo->query("DELETE FROM product_sizes WHERE product_id = $id");
        $stmt = $pdo->prepare("INSERT INTO product_sizes (product_id, size) VALUES (?, ?)");
        foreach ($sizes as $sz) $stmt->execute([$id, $sz]);

        $pdo->query("DELETE FROM product_colors WHERE product_id = $id");
        $stmt = $pdo->prepare("INSERT INTO product_colors (product_id, color) VALUES (?, ?)");
        foreach ($colors as $cl) $stmt->execute([$id, $cl]);

        $pdo->commit();
        response(true, 'Nhấn chấp nhận để hoàn tất chỉnh sửa');
    } catch (Exception $e) {
        $pdo->rollBack();
        response(false, 'Lỗi: ' . $e->getMessage());
    }
}
?>