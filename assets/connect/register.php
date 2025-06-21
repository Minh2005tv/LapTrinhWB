<?php
include 'connect.php';

function isValidPhone($phone) {
    return preg_match('/^\d{10}$/', $phone);
}

function isValidPassword($password) {
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,}$/', $password);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"] ?? '';
    $phone = $_POST["phone"] ?? '';
    $password = $_POST["password"] ?? '';
    $repassword = $_POST["repassword"] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("❌ Email không hợp lệ.");
    }

    if (!isValidPhone($phone)) {
        die("❌ Số điện thoại phải gồm đúng 10 chữ số.");
    }

    if (!isValidPassword($password)) {
        die("❌ Mật khẩu phải từ 8 ký tự trở lên, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt.");
    }

    if ($password !== $repassword) {
        die("❌ Mật khẩu nhập lại không khớp.");
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        die("❌ Email đã được đăng ký.");
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (email, phone, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $phone, $hashedPassword);

    if ($stmt->execute()) {
        header("Location: ../../pages/account/login.html");
        exit();
    } else {
        echo "Lỗi đăng ký: " . $stmt->error;
    }
}
?>
