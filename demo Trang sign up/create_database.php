<?php
$host = "localhost";
$user = "root";
$password = "";

// Kết nối MySQL
$conn = new mysqli($host, $user, $password);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Tạo cơ sở dữ liệu
$sql = "CREATE DATABASE IF NOT EXISTS shoe_store";
if (!$conn->query($sql)) {
    die("Lỗi tạo DB: " . $conn->error);
}

echo "Tạo CSDL OK<br>";

// Sử dụng cơ sở dữ liệu
$conn->select_db("shoe_store");

// Tạo bảng users
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20),
    address VARCHAR(255),
    role VARCHAR(20) DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($sql) === TRUE) {
    echo "Tạo bảng 'users' thành công!";
} else {
    echo "Lỗi tạo bảng: " . $conn->error;
}

$conn->close();
?>
