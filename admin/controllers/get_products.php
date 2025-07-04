<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Kết nối CSDL (có thể copy hàm getDb từ admin.php)
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

$pdo = getDb();
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();

foreach ($products as &$p) {
    $p['sizes'] = $pdo->query("SELECT size FROM product_sizes WHERE product_id = {$p['id']}")->fetchAll(PDO::FETCH_COLUMN);
    $p['colors'] = $pdo->query("SELECT color FROM product_colors WHERE product_id = {$p['id']}")->fetchAll(PDO::FETCH_COLUMN);
    $p['images'] = $pdo->query("SELECT image_url FROM product_images WHERE product_id = {$p['id']}")->fetchAll(PDO::FETCH_COLUMN);
}
echo json_encode($products);