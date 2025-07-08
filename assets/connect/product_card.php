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
$gender = $_GET['gender'] ?? null;
$offer = isset($_GET['offer']);

if ($offer) {
    // Chỉ lấy sản phẩm đang khuyến mãi
    $stmt = $pdo->prepare("SELECT * FROM products WHERE sales > 0 AND (date_time IS NULL OR date_time >= NOW()) ORDER BY created_at DESC");
    $stmt->execute();
    $rawProducts = $stmt->fetchAll();
} elseif ($gender) {
    // Lấy sản phẩm theo giới tính, bao gồm cả có và không khuyến mãi
    $genderMap = [
        'boys' => ['boy', 'boys'],
        'girls' => ['girl', 'girls'],
        'kids' => ['kid', 'kids', 'children']
    ];

    $gender = strtolower($gender);
    $gendersToMatch = $genderMap[$gender] ?? [$gender]; // fallback nếu không khớp

    $placeholders = implode(',', array_fill(0, count($gendersToMatch), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE LOWER(type) IN ($placeholders) ORDER BY created_at DESC");
    $stmt->execute(array_map('strtolower', $gendersToMatch));
    $rawProducts = $stmt->fetchAll();
} else {
    $rawProducts = $pdo->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll();
}

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
    <link rel="icon" href="../../assets/icon/favicon.jpg" type="image/jpg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <header class="header">
            <div class="logo">
                <a href="#"><img src="../../assets/img/logo.jpg" alt="Logo"></a>
            </div>    
            <nav class="nav">
                <a href="../../pages/home/home.html">Home</a>
                <a href="?gender=boys">Boys</a>
                <a href="?gender=girls">Girls</a>
                <a href="?gender=kids">Kids</a>
                <a href="?offer=1">Special Offer</a>
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
            <div class="cart-icons">
                <a href="../../pages/home/cart.html" class="cart-icon" title="Giỏ hàng">
                    <i class='bx bx-cart'></i>
                </a>    
            </div>
            </div>
        </header>
            <?php if ($gender): ?>
            <?= htmlspecialchars(ucfirst($gender)) ?>
            <?php endif; ?>

            <div class="product-container">
                <?php foreach ($products as $p): ?>
            <div class="item">
                <div class="img-box">
                    <?php if ($p['sales'] && $p['date_time']): ?>
                        <div class="badge">🔥 -<?= $p['sales'] ?>%</div>
                    <?php endif; ?>
                    <?php
                    $discounted = $p['price'];
                    if ($p['sales']) {
                        $discounted = $p['price'] * (1 - $p['sales'] / 100);
                    }

                    $productData = [
                        'name' => $p['name'],
                        'price' => $p['sales'] ? $discounted : $p['price'],
                        'image' => '../../' . ($p['images'][0] ?? 'uploads/no-image.png'),
                        'sizes' => $p['sizes'],
                        'colors' => $p['colors']
                    ];
                    $encoded = base64_encode(json_encode($productData));
                    ?>
                    <a href="product_detail.php?data=<?= urlencode($encoded) ?>">
                         <img src="../../<?= htmlspecialchars($p['images'][0] ?? 'uploads/no-image.png') ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                    </a>
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

                    <label>Color</label>
                    <ul class="colors">
                        <?php foreach ($p['colors'] as $cl): ?>
                            <?php 
                                $color = trim($cl);
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
                        <a href="../../pages/home/cart.html"
                        class="add-to-cart-btn"
                        data-name="<?= htmlspecialchars($p['name']) ?>"
                        data-price="<?= $p['sales'] ? $discounted : $p['price'] ?>"
                        data-image="../../<?= htmlspecialchars($p['images'][0] ?? 'uploads/no-image.png') ?>"
                        data-sizes='<?= json_encode($p["sizes"]) ?>'
                        data-colors='<?= json_encode($p["colors"]) ?>'>
                        <i class="fa fa-shopping-cart"></i> Thêm Giỏ & Mua Ngay
                        </a>
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
        <script>
            document.addEventListener('DOMContentLoaded', function () {
            const buttons = document.querySelectorAll('.add-to-cart-btn');

            buttons.forEach(btn => {
                btn.addEventListener('click', function () {
                const product = {
                    name: this.dataset.name,
                    price: parseFloat(this.dataset.price),
                    image: this.dataset.image,
                    sizes: JSON.parse(this.dataset.sizes),
                    colors: JSON.parse(this.dataset.colors),
                    quantity: 1
                };

                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                cart.push(product);
                localStorage.setItem('cart', JSON.stringify(cart));
                });
            });
            });
        </script>
</body>
</html>
