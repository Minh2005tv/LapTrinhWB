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
$rawProducts = $pdo->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll();

$products = [];
foreach ($rawProducts as $p) {
    $p['sizes'] = $pdo->query("SELECT size FROM product_sizes WHERE product_id = {$p['id']}")->fetchAll(PDO::FETCH_COLUMN);
    $p['colors'] = $pdo->query("SELECT color FROM product_colors WHERE product_id = {$p['id']}")->fetchAll(PDO::FETCH_COLUMN);
    $p['images'] = $pdo->query("SELECT image_url FROM product_images WHERE product_id = {$p['id']}")->fetchAll(PDO::FETCH_COLUMN);
    $products[] = $p;
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sản phẩm</title>
    <link rel="stylesheet" href="../css/home/product_card.css">
    <link rel="stylesheet" href="../css/home/header.css">
    <link rel="stylesheet" href="../css/home/footer.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
            <header class="header">
            <div class="logo">
                <a href="#"><img src="../../assets/img/logo.jpg" alt="Logo"></a>
            </div>    
            <nav class="nav">
                <a href="#">Home</a>
                <a href="#">Men</a>
                <a href="#">Girl</a>
                <a href="#">Kids</a>
                <a href="#">Trademark</a>
            </nav>
            <div class="search">
                <div class="search-box">
                    <input type="text" placeholder="Search..." required>
                    <i class='bx bx-search'></i>
                </div>
            <div class="user-icons">
                <a href="../account/login.html" class="user-icon" title="Tài khoản">
                    <i class='bx bx-user-circle'></i>
                </a>
            </div>
                <a href="cart.html" class="cart-icon" title="Giỏ hàng">
                    <i class='bx bx-cart'></i>
                </a>    
            </div>
            </div>
        </header>
    <div class="product-container">
        <?php foreach ($products as $p): ?>
            <div class="item">
                <div class="img-box">
                    <?php if ($p['sales'] && $p['date_time']): ?>
                        <div class="badge">🔥 -<?= $p['sales'] ?>%</div>
                    <?php endif; ?>
                    <img src="../../<?= htmlspecialchars($p['images'][0] ?? 'uploads/no-image.png') ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                </div>
                <div class="details">
                    <h2><?= htmlspecialchars($p['name']) ?> <span><?= htmlspecialchars($p['type']) ?></span></h2>
                    <?php if ($p['sales']): ?>
                        <?php $discounted = $p['price'] * (1 - $p['sales'] / 100); ?>
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
                            <?php 
                                $color = trim($cl);
                                // Kiểm tra an toàn để loại bỏ giá trị rỗng hoặc không hợp lệ nếu cần
                                if ($color !== ''):
                            ?>
                                <li 
                                    style="background-color: <?= htmlspecialchars($color) ?>;" 
                                    title="<?= htmlspecialchars($color) ?>">
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>


                    <div class="buy-now">
                        <a href="#"><i class="fa fa-shopping-cart"></i> Thêm Giỏ & Mua Ngay</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
        <footer class="footer">
      <div class="footer-top">
        <div class="footer-box contact">
          <h4>GỌI MUA HÀNG ONLINE</h4>
          <p class="hotline">1900.123.456</p>
          <p>08:00 - 21:00 | Tất cả các ngày (Trừ tết Âm Lịch)</p>

          <h4>GÓP Ý & KHIẾU NẠI</h4>
          <p class="hotline">1900.266.888</p>
          <p>08:30 - 20:30 | Tất cả các ngày (Trừ tết Âm Lịch)</p>
        </div>

        <div class="footer-box">
          <h4>THÔNG TIN</h4>
          <ul>
            <li><a href="#">Giới thiệu về chúng tôi</a></li>
            <li><a href="#">Thông tin Website</a></li>
            <li><a href="#">Than Phiền Góp Ý</a></li>
            <li><a href="#">Chính sách và quy định</a></li>
          </ul>
        </div>

        <div class="footer-box">
          <h4>FAQ</h4>
          <ul>
            <li><a href="#">Vận chuyển</a></li>
            <li><a href="#">Chính sách đổi trả</a></li>
            <li><a href="#">Bảo hành</a></li>
            <li><a href="#">Đối tác cung cấp</a></li>
          </ul>
          <div class="socials">
            <img src="../../assets/icon/facebook.jpg" alt="Facebook" />
            <img src="../../assets/icon/ins.jpg" alt="Instagram" />
            <img src="../../assets/icon/shoppe.jpg" alt="Shopee" />
            <img src="../../assets/icon/tiktok.jpg" alt="TikTok" />
          </div>
        </div>
      </div>

      <div class="footer-bottom">
        <h3>HỆ THỐNG CỬA HÀNG</h3>
        <p>Xem địa chỉ các cửa hàng</p>

        <div class="store-list">
          <div class="store-item">
            <h5>CN1</h5>
            <p>
              📍 Số 2, đường Võ Oanh (D3 cũ), P. 25, Q. Bình Thạnh, TP. HCM.
            </p>
          </div>
          <div class="store-item">
            <h5>CN2</h5>
            <p>📍 10/12 Trần Não, KP3, P. Bình An, TP. Thủ Đức, TP. HCM.</p>
          </div>
          <div class="store-item">
            <h5>CN3</h5>
            <p>📍 hẻm 70 đường Tô Ký, phường Tân Chánh Hiệp, quận 12, TPHCM.</p>
          </div>
        </div>
      </div>
    </footer>
</body>
</html>
