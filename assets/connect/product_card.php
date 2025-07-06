<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

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
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sản phẩm</title>
    <link rel="stylesheet" href="../css/home/product_card.css">
</head>
<body>
    <div class="product-container">
        <?php foreach ($products as $p): ?>
            <div class="item">
                <div class="img-box">
                    <img src="../../<?= htmlspecialchars($p['images'][0] ?? 'uploads/no-image.png') ?>" alt="<?= htmlspecialchars($p['name']) ?>" style="width:100%; height:100%; object-fit:cover;">
                </div>
                <div class="details">
                    <h2><?= htmlspecialchars($p['name']) ?> <span><?= htmlspecialchars($p['type']) ?></span></h2>
                    <?php if ($p['sales']): ?>
                    <?php
                        $discounted = $p['price'] * (1 - $p['sales'] / 100);
                    ?>
                    <p class="price">
                        <span class="original"><?= number_format($p['price'], 0, ',', '.') ?>đ</span>
                        <span class="discounted"><?= number_format($discounted, 0, ',', '.') ?>đ</span>
                    </p>
                    <?php else: ?>
                        <p class="price"><?= number_format($p['price'], 0, ',', '.') ?>đ</p>
                    <?php endif; ?>

                    <label>Size</label>
                    <ul class="sizes">
                        <?php foreach ($p['sizes'] as $sz): ?>
                            <li><?= htmlspecialchars($sz) ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <label>Màu</label>
                    <ul class="colors">
                        <?php foreach ($p['colors'] as $cl): ?>
                            <li style="background-color: <?= htmlspecialchars($cl) ?>;"></li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if ($p['sales'] && $p['date_time']): ?>
                        <div class="sale-box">
                            🔥 Giảm <?= $p['sales'] ?>% đến <?= date('d/m/Y H:i', strtotime($p['date_time'])) ?>
                        </div>
                    <?php else: ?>
                        <div class="sale-box small">
                            <a href="#">Xem chi tiết</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>