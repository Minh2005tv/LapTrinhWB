CREATE DATABASE IF NOT EXISTS selling_shoes;
USE selling_shoes;

-- Bảng người dùng (admin)
CREATE TABLE IF NOT EXISTS users (
id INT AUTO_INCREMENT PRIMARY KEY,
email VARCHAR(100) NOT NULL UNIQUE,
phone VARCHAR(20) NOT NULL,
password VARCHAR(255) NOT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng sản phẩm
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS product_sizes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    size VARCHAR(10) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS product_colors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    color VARCHAR(50) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

ALTER TABLE products 
ADD COLUMN date_time DATETIME NULL,
ADD COLUMN sales INT NULL;

CREATE TABLE giay (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_san_pham VARCHAR(255),
    thuong_hieu VARCHAR(100),
    gia DECIMAL(10, 2),
    mo_ta TEXT,
    kich_co VARCHAR(50),
    mau_sac VARCHAR(50),
    chat_lieu VARCHAR(100),
    tinh_trang VARCHAR(50),
    hinh_anh VARCHAR(255)
);
