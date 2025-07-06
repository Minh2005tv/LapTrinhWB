<?php
// filepath: c:\Project_LTW\LapTrinhWB\assets\connect\product_card.php

function getDb() {
    $host = 'localhost';
    $db   = 'selling_shoes';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    try {
        return new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    } catch (PDOException $e) {
        die("Kết nối CSDL thất bại: " . $e->getMessage());
    }
}

$pdo = getDb();
$products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();

$discount = [];
$hot = [];
foreach ($products as $p) {
    if (!empty($p['discount']) && $p['discount'] >= 50) {
        $discount[] = $p;
    }
    if (!empty($p['hot']) && $p['hot']) {
        $hot[] = $p;
    }
}

// Xuất ra JS để client render
echo "<script>
window.discountProducts = " . json_encode($discount) . ";
window.hotProducts = " . json_encode($hot) . ";
</script>";
?>