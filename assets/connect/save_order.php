<?php
// Nhớ kết nối DB
$conn = new mysqli("localhost", "root", "", "your_database_name");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"), true);

$address = $data['address'];
$delivery = $data['delivery_method'];
$payment = $data['payment_method'];
$items = $data['items'];

// Lưu đơn hàng chính
$stmt = $conn->prepare("INSERT INTO orders (address, delivery_method, payment_method) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $address, $delivery, $payment);
$stmt->execute();
$order_id = $stmt->insert_id;
$stmt->close();

// Lưu từng sản phẩm
$stmtItem = $conn->prepare("INSERT INTO order_items (order_id, name, price, image, size, color, quantity) VALUES (?, ?, ?, ?, ?, ?, ?)");
foreach ($items as $item) {
    $stmtItem->bind_param(
        "isdsssi",
        $order_id,
        $item['name'],
        $item['price'],
        $item['image'],
        $item['sizes'][0],
        $item['colors'][0],
        $item['quantity']
    );
    $stmtItem->execute();
}
$stmtItem->close();

echo json_encode(["success" => true, "order_id" => $order_id]);
$conn->close();
?>
