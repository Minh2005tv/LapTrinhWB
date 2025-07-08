<?php
ob_clean();
header('Content-Type: application/json; charset=utf-8');

// Ghi log JSON nhận được (debug)
$body = file_get_contents('php://input');
file_put_contents(__DIR__.'/debug_json.txt', $body . "\n", FILE_APPEND);

// Parse JSON
$data = json_decode($body, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON: ' . json_last_error_msg()
    ]);
    exit;
}

// Validate dữ liệu
if (
    empty($data['address']) || 
    empty($data['delivery_method']) || 
    empty($data['payment_method']) || 
    empty($data['items']) || 
    !is_array($data['items'])
) {
    echo json_encode([
        'success' => false,
        'message' => 'Thiếu dữ liệu đầu vào.'
    ]);
    exit;
}

// Kết nối CSDL
require_once __DIR__.'/connect.php';

// Bắt đầu transaction
$conn->begin_transaction();

try {
    // Tạo đơn hàng
    $stmt = $conn->prepare("
        INSERT INTO orders (address, delivery_method, payment_method) 
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param('sss', $data['address'], $data['delivery_method'], $data['payment_method']);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Tạo chi tiết đơn hàng
    $stmt2 = $conn->prepare("
        INSERT INTO order_items 
        (order_id, name, price, image, size, color, quantity) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($data['items'] as $item) {
        $name = $item['name'];
        $price = floatval($item['price']);
        $image = isset($item['image']) ? $item['image'] : '';
        $size = isset($item['sizes'][0]) ? $item['sizes'][0] : '';
        $color = isset($item['colors'][0]) ? $item['colors'][0] : '';
        $quantity = intval($item['quantity']);

        $stmt2->bind_param(
            'isdsssi',
            $order_id,
            $name,
            $price,
            $image,
            $size,
            $color,
            $quantity
        );
        $stmt2->execute();
    }

    $stmt2->close();
    $conn->commit();

    echo json_encode([
        'success' => true,
        'order_id' => $order_id
    ]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi xử lý đơn hàng: ' . $e->getMessage()
    ]);
}
