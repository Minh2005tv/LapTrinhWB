<?php
session_start();
include("connect.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Kiểm tra kết nối
    if (!$conn) {
        die("Kết nối thất bại: " . mysqli_connect_error());
    }

    // Cho phép đăng nhập bằng số điện thoại hoặc email
    $sql = "SELECT * FROM users WHERE phone = ? OR email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $phone, $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['user'] = $row['phone'];
            header("Location: ../pages/home/home.php");
            exit();
        } else {
            echo "<script>alert('Sai mật khẩu'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Số điện thoại hoặc email không tồn tại'); window.history.back();</script>";
    }

    mysqli_close($conn);
} else {
    echo "<script>alert('Phương thức không hợp lệ');</script>";
}
?>