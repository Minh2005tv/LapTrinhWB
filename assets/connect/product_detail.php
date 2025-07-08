<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Page</title>
    <link rel="stylesheet" href="../css/home/product_detail.css">
</head>
<body>
    <div class="container">
        <div class="product-image">
            <img src="https://via.placeholder.com/300x400" alt="Product Image">
        </div>
        <div class="product-details">
            <h1>I'm a Product</h1>
            <div class="sku">SKU: 001</div>
            <div class="price">$18.00</div>
            <div class="form-group">
                <label>Size *</label>
                <select>
                    <option value="">Select</option>
                    <!-- Add size options as needed -->
                </select>
            </div>
            <div class="form-group">
                <label>Color *</label>
                <span class="color-option mint"></span> 
            </div>
            <div class="form-group">
                <label>Quantity *</label>
                <input type="number" value="1" min="1">
            </div>
            <button class="btn">Add to Cart</button>
            <button class="btn">Buy Now</button>
            <div class="product-info">PRODUCT INFO</div>
        </div>
    </div>
</body>
</html>