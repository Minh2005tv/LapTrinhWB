<?php
$data = json_decode(base64_decode($_GET['data'] ?? ''), true);

$name = $data['name'] ?? 'Unknown';
$price = $data['price'] ?? '0.00';
$image = $data['image'] ?? 'https://via.placeholder.com/300x400';
$sizes = $data['sizes'] ?? [];
$colors = $data['colors'] ?? [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($name) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/home/product_detail.css">
    <link rel="stylesheet" href="../css/home/header.css">
    <link rel="stylesheet" href="../css/home/footer.css">
</head>
<body>
        <!-- Header -->
        <header class="header">
            <div class="logo">
                <a href="#"><img src="../../assets/img/logo.jpg" alt="Logo"></a>
            </div>    
            <nav class="nav">
                <a href="../../pages/home/home.html">Home</a>
                <a href="../../assets/connect/product_card.php?gender=boys">Boys</a>
                <a href="../../assets/connect/product_card.php?gender=girls">Girls</a>
                <a href="../../assets/connect/product_card.php?gender=kids">Kids</a>
                <a href="../../assets/connect/product_card.php?offer=1">special offer</a>
            </nav>
            <div class="search">
                <div class="search-box">
                    <input type="text" placeholder="Search..." required>
                    <i class='bx bx-search'></i>
                </div>
            <div class="user-icons">
                <a href="../../pages/error/error.html" class="user-icon" title="Tài khoản">
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
        <!-- Header -->

  <main class="container">
    <section class="product-image">
      <img src="<?= htmlspecialchars($image) ?>" alt="Product Image">
    </section>

    <section class="product-details">
      <h1 class="product-name"><?= htmlspecialchars($name) ?></h1>
      <p class="product-desc">
        Thiết kế thời thượng, chất liệu nhẹ và bền – hoàn hảo cho vận động hàng ngày hoặc phong cách streetwear. Đế êm ái hỗ trợ bước đi thoải mái.
      </p>
      <div class="product-price"><?= number_format($price, 0, ',', '.') ?>₫</div>

      <div class="product-meta">
        <label>Màu sắc:</label>
        <div id="color-options">
          <?php foreach ($colors as $index => $cl): ?>
            <?php if (!empty($cl)): ?>
              <label class="color-radio" style="background-color: <?= htmlspecialchars($cl) ?>;">
                <input type="radio" name="color" value="<?= htmlspecialchars($cl) ?>" <?= $index === 0 ? 'checked' : '' ?>>
                <span class="indicator"></span>
              </label>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="product-meta">
        <label>Kích thước:</label>
        <div class="size-options">
          <?php foreach ($sizes as $sz): ?>
            <label class="size-btn">
              <input type="radio" name="size" value="<?= htmlspecialchars($sz) ?>">
              <span><?= htmlspecialchars($sz) ?></span>
            </label>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="product-meta">
        <label>Số lượng:</label>
        <input type="number" id="qty" value="1" min="1">
      </div>

      <div class="product-actions">
        <button class="btn btn-cart" onclick="addToCart()">THÊM VÀO GIỎ</button>
        <button class="btn btn-buy" onclick="buyNow()">MUA NGAY</button>
      </div>
    </section>
  </main>

          <!--Footer-->
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
        <!--Footer-->

  <script>
    function getSelectedData() {
      const sizeInput = document.querySelector('input[name="size"]:checked');
      const colorInput = document.querySelector('input[name="color"]:checked');
      const qtyInput = document.getElementById("qty");

      const size = sizeInput ? sizeInput.value : null;
      const color = colorInput ? colorInput.value : null;
      const quantity = parseInt(qtyInput.value);

      return { size, color, quantity };
    }

    function validateSelection(data) {
      if (!data.size) {
        alert(" Vui lòng chọn kích thước.");
        return false;
      }
      if (!data.color) {
        alert(" Vui lòng chọn màu sắc.");
        return false;
      }
      if (!data.quantity || data.quantity < 1) {
        alert(" Vui lòng nhập số lượng hợp lệ.");
        return false;
      }
      return true;
    }

    function buildProductObject(data) {
      return {
        name: <?= json_encode($name) ?>,
        price: parseFloat(<?= json_encode($price) ?>),
        image: <?= json_encode($image) ?>,
        sizes: [data.size],
        colors: [data.color],
        quantity: data.quantity
      };
    }

    function addToCart() {
      const selected = getSelectedData();
      if (!validateSelection(selected)) return;

      const product = buildProductObject(selected);
      let cart = JSON.parse(localStorage.getItem("cart")) || [];
      cart.push(product);
      localStorage.setItem("cart", JSON.stringify(cart));
      alert("Sản phẩm đã được thêm vào giỏ hàng!");
    }

        function buyNow() {
        const selected = getSelectedData();
        if (!validateSelection(selected)) return;
        const product = buildProductObject(selected);
        localStorage.setItem("cart", JSON.stringify([product]));

        // Đường dẫn chính xác tới cart.html
        window.location.href = "/Website_Selling%20sports%20shoes/pages/home/cart.html";
        }

  </script>
</body>
</html>
