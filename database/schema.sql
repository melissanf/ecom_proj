-- Beauty Store E-Commerce Database
CREATE DATABASE IF NOT EXISTS beauty_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE beauty_store;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL,
    slug VARCHAR(80) NOT NULL UNIQUE
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2) DEFAULT NULL,
    image VARCHAR(255) DEFAULT 'placeholder.svg',
    stock INT DEFAULT 100,
    on_sale TINYINT(1) DEFAULT 0,
    featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(150) NOT NULL,
    shipping_address TEXT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Default admin: admin@beauty.com / password
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@beauty.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

INSERT INTO categories (name, slug) VALUES
('Serums', 'serums'),
('Moisturizers', 'moisturizers'),
('Cleansers', 'cleansers'),
('Sun Care', 'sun-care');

INSERT INTO products (category_id, name, slug, description, price, sale_price, on_sale, featured) VALUES
(1, 'Niacinamide 10% + Zinc 1%', 'niacinamide-10-zinc-1', 'High-strength vitamin and mineral blemish formula that reduces the appearance of skin blemishes and congestion.', 12.90, 9.90, 1, 1),
(1, 'Hyaluronic Acid 2% + B5', 'hyaluronic-acid-2-b5', 'Hydrating formula with ultra-pure, vegan hyaluronic acid for multi-depth hydration and plumped skin.', 14.90, 11.90, 1, 1),
(1, 'Caffeine Solution 5% + EGCG', 'caffeine-solution-5-egcg', 'Reduces appearance of eye contour pigmentation and puffiness with caffeine and green tea catechins.', 9.80, 7.80, 1, 1),
(2, 'Natural Moisturizing Factors + HA', 'nmnf-ha', 'Surface hydration formula with amino acids, fatty acids, and hyaluronic acid for lasting moisture.', 11.50, NULL, 0, 1),
(2, 'Squalane Cleanser', 'squalane-cleanser', 'Gentle, effective cleanser that removes makeup while respecting skin barrier integrity.', 10.90, 8.90, 1, 0),
(3, 'Glycolic Acid 7% Toning Solution', 'glycolic-acid-7-toning', 'Exfoliating toner for improved radiance and visible texture with 7% glycolic acid.', 10.80, NULL, 0, 0),
(4, 'Mineral UV Filters SPF 30', 'mineral-uv-spf30', 'Broad-spectrum mineral sunscreen with antioxidants for daily protection without white cast.', 18.90, 15.90, 1, 0),
(1, 'Retinol 0.5% in Squalane', 'retinol-05-squalane', 'Water-free solution with pure retinol for visible anti-aging benefits and smoother skin texture.', 16.90, 13.90, 1, 0);
