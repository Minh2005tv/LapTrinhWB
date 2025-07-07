<?php
session_start();
$pdo = new PDO("mysql:host=localhost;dbname=selling_shoes;charset=utf8mb4", 'root', '');

$cart_id = $_SESSION['cart_id'] ?? 0;
$items = [];

if ($cart_id) {
    $stmt = $pdo->prepare("SELECT ci.*, p.name, p.price, (p.price * (1 - IFNULL(p.sales,0)/100)) as discounted_price,
                                  pi.image_url
                           FROM cart_items ci
                           JOIN products p ON ci.product_id = p.id
                           LEFT JOIN product_images pi ON pi.product_id = p.id
                           WHERE ci.cart_id = ?");
    $stmt->execute([$cart_id]);
    $items = $stmt->fetchAll();
}
?>
<!-- LapTrinhWB/pages/home/cart.html -->
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cart & Payment</title>
    <link rel="stylesheet" href="../../assets/css/home/cart.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  </head>
  <body>
    <header class="main-header"><span class="hd-badge">HD</span><span class="menu-title">menu</span></header>
    <main class="container">
      <!-- Shopping Bag -->
      <section class="shopping-bag">
        <div class="bag-header">
          <h3>Shopping Bag</h3>
          <i class="fa fa-shopping-bag"></i>
        </div>
        <div class="product-list">
          <!-- Sản phẩm mẫu -->
          <div class="product" data-price="20">
            <div class="product-img-wrap">
              <img src="../../assets/img/slide-1.jpg" alt="Product" />
            </div>
            <div class="product-info">
              <div class="product-title">Giày Nike Air</div>
              <div class="product-meta">
                <span class="product-price">20$</span>
              </div>
              <div class="product-options">
                <label class="size-label">Size
                  <select class="size-select">
                    <option>M</option>
                    <option>L</option>
                    <option>XL</option>
                  </select>
                </label>
                <div class="quantity-group">
                  <button class="decrease"><i class="fa fa-minus"></i></button>
                  <input type="number" value="1" readonly />
                  <button class="increase"><i class="fa fa-plus"></i></button>
                </div>
              </div>
            </div>
            <input type="checkbox" class="select-product" />
          </div>
          <div class="product" data-price="30">
            <div class="product-img-wrap">
              <img src="../../assets/img/slide-2.jpg" alt="Product" />
            </div>
            <div class="product-info">
              <div class="product-title">New Balance</div>
              <div class="product-meta">
                <span class="product-price">30$</span>
              </div>
              <div class="product-options">
                <label class="size-label">Size
                  <select class="size-select">
                    <option>28</option>
                    <option>29</option>
                    <option>30</option>
                    <option>31</option>
                  </select>
                </label>
                <div class="quantity-group">
                  <button class="decrease"><i class="fa fa-minus"></i></button>
                  <input type="number" value="1" readonly />
                  <button class="increase"><i class="fa fa-plus"></i></button>
                </div>
              </div>
            </div>
            <input type="checkbox" class="select-product" />
          </div>
          <div class="product" data-price="25">
            <div class="product-img-wrap">
              <img src="../../assets/img/slide-3.jpg" alt="Product" />
            </div>
            <div class="product-info">
              <div class="product-title">Adidas</div>
              <div class="product-meta">
                <span class="product-price">25$</span>
              </div>
              <div class="product-options">
                <label class="size-label">Size
                  <select class="size-select">
                    <option>M</option>
                    <option>L</option>
                    <option>XL</option>
                  </select>
                </label>
                <div class="quantity-group">
                  <button class="decrease"><i class="fa fa-minus"></i></button>
                  <input type="number" value="1" readonly />
                  <button class="increase"><i class="fa fa-plus"></i></button>
                </div>
              </div>
            </div>
            <input type="checkbox" class="select-product" />
          </div>
        </div>
        <label class="select-all-label"><input type="checkbox" id="select-all" /> All</label>
      </section>
      <!-- Payment Section -->
      <section class="payment-summary">
        <h3>PAYMENT</h3>
        <div class="info">
          DELIVERY METHOD
          <select id="delivery-method" required>
            <option value="">--Select--</option>
            <option value="fast">Giao hàng nhanh</option>
            <option value="save">Giao hàng tiết kiệm</option>
          </select>
        </div>
        <div class="info">
          ADDRESS
          <input id="address" type="text" placeholder="Nhập địa chỉ nhận hàng" required />
        </div>
        <div class="info">
          PAYMENT
          <select id="payment-method" required>
            <option value="">--Select--</option>
            <option value="cod">Thanh toán khi nhận hàng</option>
            <option value="bank">Chuyển khoản ngân hàng</option>
          </select>
        </div>
        <div class="info">NUMBER OF PRODUCTS: <span id="count">0</span></div>
        <div class="info">TOTAL: <span id="total">0$</span></div>
        <button id="pay-btn">PAYMENT</button>
      </section>
    </main>
    <footer class="main-footer"><span>FOOTER</span></footer>
    <script src="../../assets/js/cart.js"></script>
    <div id="popup-noti"></div>
  </body>
</html>