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

// Xử lý thêm sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['name'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $sizes = json_decode($_POST['sizes_raw'] ?? '[]');
    $colors = json_decode($_POST['color'] ?? '[]');

    $date_time = $_POST['date_time'] ?? null;
    $sales = $_POST['sales'] ?? null;

    // Validate
    if ($name === '' || $type === '') response(false, 'Tên và loại không được để trống');
    if (!is_numeric($price) || $price <= 0) response(false, 'Giá phải là số > 0');
    if (!is_array($sizes) || count($sizes) == 0 || count($sizes) > 5) response(false, 'Size tối đa 5');
    if (!is_array($colors) || count($colors) == 0 || count($colors) > 5) response(false, 'Màu tối đa 5');
    foreach ($sizes as $sz) if (!is_numeric($sz) || $sz <= 0) response(false, 'Size phải là số dương');
    foreach ($colors as $cl) if (trim($cl) === '') response(false, 'Màu không được rỗng');

    if (!isset($_FILES['images']) || count($_FILES['images']['name']) == 0) response(false, 'Phải chọn ít nhất 1 ảnh');
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    $image_urls = [];
    foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
        $type_img = $_FILES['images']['type'][$i];
        if (!in_array($type_img, $allowed)) response(false, 'Ảnh không đúng định dạng');
        $ext = pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION);
        $newname = uniqid('img_') . '.' . $ext;
        $dest = '../../uploads/' . $newname;
        if (!move_uploaded_file($tmp, $dest)) response(false, 'Lỗi upload ảnh');
        $image_urls[] = 'uploads/' . $newname;
    }

    // Validate promotion logic
    if (($date_time && !$sales) || (!$date_time && $sales)) {
        response(false, 'Nếu đã nhập "Date & Time" thì phải nhập cả "Sales", và ngược lại.');
    }

    // Save to DB
    $pdo = getDb();
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("INSERT INTO products (name, type, price, date_time, sales) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $type, $price, $date_time ?: null, $sales ?: null]);
        $pid = $pdo->lastInsertId();

        $stmt = $pdo->prepare("INSERT INTO product_sizes (product_id, size) VALUES (?, ?)");
        foreach ($sizes as $sz) $stmt->execute([$pid, $sz]);

        $stmt = $pdo->prepare("INSERT INTO product_colors (product_id, color) VALUES (?, ?)");
        foreach ($colors as $cl) $stmt->execute([$pid, $cl]);

        $stmt = $pdo->prepare("INSERT INTO product_images (product_id, image_url) VALUES (?, ?)");
        foreach ($image_urls as $url) $stmt->execute([$pid, $url]);

        $pdo->commit();
        response(true, 'Thêm sản phẩm thành công');
    } catch (Exception $e) {
        $pdo->rollBack();
        response(false, 'Lỗi: ' . $e->getMessage());
    }
}
?>
